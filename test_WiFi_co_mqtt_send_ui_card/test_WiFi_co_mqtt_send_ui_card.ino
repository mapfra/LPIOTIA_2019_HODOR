#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN         0
#define SS_PIN          2
 
const char* ssid = "LPiOTIA"; // Enter your WiFi name
const char* password =  ""; // Enter WiFi password

const char* mqttServer = "10.0.4.116";
const int mqttPort = 1883;

WiFiClient espClient;
PubSubClient client(espClient);

// Instance MFRC522
MFRC522 lecteur(SS_PIN, RST_PIN);

// Init array contenant l'UID
byte UIDPICC[4];
char byteTochar[4];


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
    Serial.println("Connecting to WiFi..");
  }
  Serial.println("Connected to the WiFi network");
 
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

      client.publish("guizard/hodor/uid", str);
      client.publish("guizard/hodor/uid", "Ma carte");
      client.subscribe("guizard/hodor/uid");

      
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
