/**
 * Code pour NodeMCU ESP8266
 * On lit une carte, on récupère son UID et on le garde en mémoire 
 */

#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN         0
#define SS_PIN          2

// Instance MFRC522
MFRC522 lecteur(SS_PIN, RST_PIN);

// Init array contenant l'UID
byte UIDPICC[4];

void setup() {
  Serial.begin(9600);
  while(!Serial);

  // Init du bus SPI
  SPI.begin();

  // Init du lecteur
  lecteur.PCD_Init();

  delay(500);
  Serial.println("Lecteur actif, en attente d'une lecture");
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
      }
      else {
        // Dans le cas où la carte a déjà été présentée
        Serial.println(F("Carte déjà lue..."));
      }

      // Halt PICC
      lecteur.PICC_HaltA();

      // Stop encryption on PCD
      lecteur.PCD_StopCrypto1();
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
