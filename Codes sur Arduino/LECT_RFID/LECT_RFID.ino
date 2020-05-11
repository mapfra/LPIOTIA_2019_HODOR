#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN         0
#define SS_PIN          2
 
const char* ssid = "SFR_3BA0";
const char* password =  "rkmarph4omerthondorc";

//const char* mqttServer = "192.168.1.30"; //Mon IP locale
//const int mqttPort = 1883;

//Remote server - Raspberry chez Raph
const char* mqttServer = "90.116.66.46";
const int mqttPort = 8086;

WiFiClient espClient;
PubSubClient client(espClient);

// Instance MFRC522
MFRC522 lecteur(SS_PIN, RST_PIN);

// Init array contenant l'UID
byte UIDPICC[4];
char byteTochar[4];

//Init de l'entier variable
int tempo = 0;
char tempoStr[5];

void setup() {
  Serial.begin(9600);
  while(!Serial);

  WiFi.begin(ssid, password);

  // Init du bus SPI
  SPI.begin();

  // Init du lecteur
  lecteur.PCD_Init();

  delay(500);
 
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.println("Connexion WiFi..");
  }
  Serial.println("Connecté au réseau WiFi");
 
  client.setServer(mqttServer, mqttPort);
  client.setCallback(callback);
 
  while (!client.connected()) {
    Serial.println("Connexion au serveur MQTT...");
 
    if (client.connect("ESP8266Client")) {
      Serial.println("Connecté");  
    } else {
      Serial.print("Erreur : ");
      Serial.print(client.state());
      delay(2000);
    }
  }

  Serial.println("Lecteur actif, en attente d'une lecture"); 
}
 
void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived in topic: ");
  Serial.println(topic);
  Serial.print("Message:");
  for (int i = 0; i < length; i++) {
    Serial.print((char)payload[i]);
  }
  Serial.println();
  Serial.println("-----------------------");
}
 
void loop() {

    //On va 'randomiser' le message transmis via le broker avec une variable entière
  if(tempo<9) {
    tempo = tempo +1;
    //tempoStr = "000" + tempo;
    sprintf(tempoStr,"%s%d","000",tempo);

  }

  if(tempo>=9 && tempo <99) {
    tempo = tempo +1;
    //tempoStr = "00" + tempo;
    sprintf(tempoStr,"%s%d","00",tempo);
  } if(tempo>=99) {
    tempo = 0;
    sprintf(tempoStr,"%s%d","000",tempo);

  }
    
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
      char str[32] = "";
      array_to_string(lecteur.uid.uidByte, 4, str); //Insert (byte array, length, char array for output)
      Serial.println(str); //Print the output uid string
      crypt_output(str);      
      }
      else {
        // Dans le cas où la carte a déjà été présentée
        Serial.println(F("Carte déjà lue..."));
      }

      // Halt PICC
      lecteur.PICC_HaltA();

      // Stop encryption on PCD
      lecteur.PCD_StopCrypto1();
  
  client.loop();
}

/**
 * =============================CHIFFREMENT MANUEL (KEY = 7) ET ENVOI VIA BROKER MQTT==========================================
 */

 void crypt_output(char str[8]) {
  int key = 7;
  char output[sizeof(str)+1];
  
  byte premier = str[0] + key;
  byte deuxieme = str[1] + key;
  byte troisieme = str[2] + key;
  byte quatrieme = str[3] + key;
  byte cinquieme = str[4] + key;
  byte sixieme = str[5] + key;
  byte septieme = str[6] + key;
  byte huitieme = str[7] + key;

  output[8] = '\0';
  output[7] = deuxieme;
  output[6] = premier;
  output[5] = quatrieme;
  output[4] = troisieme;
  output[3] = sixieme;
  output[2] = cinquieme;
  output[1] = huitieme;
  output[0] = septieme;
  
    if (client.connected()) {
      yield();
    client.publish("guizard/hodor/uid", strcat(tempoStr,output));
  } else {
    reconnect_pub(output);
  }
  
  
  client.subscribe("guizard/hodor/uid");
  
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
      client.subscribe("guizard/hodor/uid");
    } else {
      Serial.print(client.state());
      Serial.println(" nouvel essai imminent");
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
