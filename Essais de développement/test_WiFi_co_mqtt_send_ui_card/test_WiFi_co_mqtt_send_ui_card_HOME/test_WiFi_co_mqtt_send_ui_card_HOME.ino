#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>
#include <ESP8266HTTPClient.h>

#define RST_PIN         0
#define SS_PIN          2

//Via routeur mobile 
//const char* ssid = "Xperia 10_a7a6";
//const char* password =  "12345678";

//PAI
const char* ssid = "SFR_3BA0";
const char* password =  "rkmarph4omerthondorc";

const char* datetime = ""; // Variable contenant la date à insérer

//const char* mqttServer = "10.0.4.116";
const char* mqttServer = "192.168.1.30"; //Mon IP locale
const int mqttPort = 1883;

//Remote server - Raspberry chez Raph
//const char* mqttServer = "90.116.66.46";
//const int mqttPort = 8086;

WiFiClient espClient;
PubSubClient client(espClient);

// Instance MFRC522
MFRC522 lecteur(SS_PIN, RST_PIN);

// Init array contenant l'UID
byte UIDPICC[4];
char byteTochar[4];


void setup() {
  Serial.begin(115200);
  while(!Serial);
 
  set_wifi();
  // Init du bus SPI
  SPI.begin();
  // Init du lecteur
  lecteur.PCD_Init();
  delay(500);

  client.setServer(mqttServer, mqttPort);
  client.setCallback(callback);
 
  while (!client.connected()) {
    Serial.println("Connecting to MQTT...");
 
    if (client.connect("ESP8266Client")) {
      Serial.println("connected");  
    } else {
      Serial.print("failed with state ");
      Serial.print(client.state());
      delay(2000);
    }
  }

  Serial.println("Lecteur actif, en attente d'une lecture"); 
}
 
void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived [");
  Serial.print(topic);
  Serial.print("] ");
  for (int i = 0; i < length; i++) {
    Serial.print((char)payload[i]);
  }
  Serial.println();
  Serial.println("-----------------------");
}
 
void loop() {

  while (!client.connected()) {
    //reconnect_pub("Hello World");
  }
  client.loop();

  //Instance du client Http
  HTTPClient http;

  //Outils ArduinoJson - capacité du JSON parsé
  const size_t capacity = JSON_OBJECT_SIZE(15) + 350;
  DynamicJsonDocument doc(capacity);
    
  // On relance le loop si aucune nouvelle carte n'est présentée
  if ( ! lecteur.PICC_IsNewCardPresent())
    return;

  // On relance le loop si l'UID de la carte n'a pas été lu
  if ( ! lecteur.PICC_ReadCardSerial())
    return;

  // On afficher via le port série les informations de la carte présentée
  Serial.print(F("PICC type --> "));
  MFRC522::PICC_Type piccType = lecteur.PICC_GetType(lecteur.uid.sak);
  Serial.println(lecteur.PICC_GetTypeName(piccType));


    //------------------------------------------------Envoi de la requete GET pour récupérer la date (Première version)--------------------------------------------------
    
    /*if (http.begin(espClient, "http://worldtimeapi.org/api/timezone/Europe/Paris")) {  // HTTP


      Serial.print("[HTTP] GET...\n");
      int httpCode = http.GET();

      // httpCode négatif si erreur
      if (httpCode > 0) {
        Serial.printf("[HTTP] GET... code: %d\n", httpCode);

        // fichier trouvé
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
          String payload = http.getString();
          // On affiche le payload ie le JSON reçu via l'API
          //Serial.println(payload);

          //Parsing en JSON
          deserializeJson(doc, http.getStream());
          datetime = doc["datetime"]; // Récupération de la dateTime
          Serial.println(datetime);

        }
      } else {
        // En cas d'erreur lors de la requête HTTP
        Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
      }

      http.end();
    } else {
      Serial.printf("[HTTP} Unable to connect\n");
    }
    */
  //------------------------------------------------Fin de la requete GET pour récupérer la date--------------------------------------------------

  // On va vérifier que la valeur de l'UID de la carte présentée correspond à la variable stockée
    if (lecteur.uid.uidByte[0] != UIDPICC[0] || 
      lecteur.uid.uidByte[1] != UIDPICC[1] || 
      lecteur.uid.uidByte[2] != UIDPICC[2] || 
      lecteur.uid.uidByte[3] != UIDPICC[3] ) {
      Serial.println(F("Nouvelle carte détectée ! "));

      // Stockons cette nouvelle UID dans notre variable
      for (byte i = 0; i<4; i++) {
        UIDPICC[i] = lecteur.uid.uidByte[i];
      }

      Serial.println(F("La nouvelle UID stockée est : "));
      Serial.println(F("HEX --> "));
      printHex(lecteur.uid.uidByte, lecteur.uid.size);
      
      Serial.println();

      Serial.println(F("DEC -->"));
      printDec(lecteur.uid.uidByte, lecteur.uid.size);
      Serial.println();

      char str[8] = "";
      char output[80];
      array_to_string(lecteur.uid.uidByte, 4, str); //Insert (byte array, length, char array for output)
      //Serial.println(str); //Print the output uid string
      //Serial.println(strcpy(output,str));
      //Serial.println(strcat(output, " "));
      //Serial.println(strcat(output, datetime));
      //Serial.println(output);
      crypt_output(str);

      // Publication via le broker des chaînes concaténées
      //client.publish("guizard/hodor/uid", output);
      //client.publish("guizard/hodor/uid", "Ma carte");
      //client.subscribe("guizard/hodor/uid");

      
      }
      else {
        // Dans le cas où la carte a déjà été présentée
        Serial.println(F("Carte déjà lue..."));
      }

      // Halt PICC
      lecteur.PICC_HaltA();

      // Stop encryption on PCD
      lecteur.PCD_StopCrypto1();

  http.end();
}

/**
 * =============================CHIFFREMENT MANUEL (KEY = 7) ET ENVOI VIA BROKER MQTT==========================================
 */

 void crypt_output(char str[8]) {
  char output[sizeof(str)];
/*
   for(byte i = 0; i < sizeof(str) - 1; i++){
   itoa((int)str[i],buff,10); //convert the next character to a string and store it in the buffer
   buff += strlen(buff); //move on to the position of the null character
   *buff = ' '; //replace with a space
   buff++; //move on ready for next
 }
 
 buff--; //move back a character to where the final space (' ') is
 *buff = '\0'; //replace it with a null to terminate the string
 */


  output[0] = str[1];
  output[1] = str[0];
  output[2] = str[3];
  output[3] = str[2];
  output[4] = str[5];
  output[5] = str[4];
  output[6] = str[7];
  output[7] = str[6];
    
  Serial.println(output);
  if (client.connected()) {
      yield();
    client.publish("guizard/hodor/uid", output);
  } else {
    reconnect_pub(output);
  }
 }

/**
 * =============================CONNEXION WiFi==========================================
 */
void set_wifi() {

  delay(10);
  // We start by connecting to a WiFi network
  Serial.println();
  Serial.print("Connexion : ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  //Affichage des informations de connexion
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}


/**
 * =============================RECONNEXIOIN BROKER==========================================
 */
 
 void reconnect_pub(char message[80]) {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Attempt to connect
    if (client.connect("ESP8266Client")) {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("guizard/hodor/uid", message);
      // ... and resubscribe
      client.subscribe("guizard/hodor/uid");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      // Wait 5 seconds before retrying
      delay(1000);
    }
  }
}

/**
 * =============================METHODES UTILES POUR AFFICHER LES DONNEES VIA LE PORT SERIE==========================================
 */

// Affichage HEX
void printHex(byte *buffer, byte bufferSize) {
  for (byte i = 0; i < bufferSize; i++) {
    Serial.print(buffer[i] < 0x10 ? " 0" : " ");
    Serial.print(buffer[i], HEX);
  }
}

// Affichage DEC
void printDec(byte *buffer, byte bufferSize) {
  for (byte i = 0; i < bufferSize; i++) {
    Serial.print(buffer[i] < 0x10 ? " 0" : " ");
    Serial.print(buffer[i], DEC);
  }
}

void array_to_string(byte array[], unsigned int len, char buffer[])
{
   for (unsigned int i = 0; i < len; i++)
   {
      byte nib1 = (array[i] >> 4) & 0x0F;
      byte nib2 = (array[i] >> 0) & 0x0F;
      buffer[i*2+0] = nib1  < 0xA ? '0' + nib1  : 'A' + nib1  - 0xA;
      buffer[i*2+1] = nib2  < 0xA ? '0' + nib2  : 'A' + nib2  - 0xA;
   }
   buffer[len*2] = '\0';
}
