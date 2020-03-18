#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 53
#define RST_PIN 5

MFRC522 rfid(SS_PIN, RST_PIN); // appel de la classe

#define PIN_LED_VERTE 8
#define PIN_LED_ROUGE 9

// liste clés valides
String keyOk[]={"15 c4 2d 5","15 c4 2d 6"};

void setup() { 
  Serial.begin(57600);
  SPI.begin(); // Init SPI bus
  rfid.PCD_Init(); // Init MFRC522 

  pinMode(PIN_LED_VERTE,OUTPUT);
  pinMode(PIN_LED_ROUGE,OUTPUT);

  // led eteintes a l'init
  digitalWrite(PIN_LED_VERTE,LOW);
  digitalWrite(PIN_LED_ROUGE,LOW);
}

void loop() {

  // Si on ne detecte pas une carte on reboucle au debut
  if ( ! rfid.PICC_IsNewCardPresent())
    return;

  // as t'on lu la carte
  if ( ! rfid.PICC_ReadCardSerial())
    return;

// recuperation de l'UID et concaténation de sa valeur en hex dans un string
String myKey=String(rfid.uid.uidByte[0],HEX) + " " + String(rfid.uid.uidByte[1],HEX) + " " + String(rfid.uid.uidByte[2],HEX) + " " + String(rfid.uid.uidByte[3],HEX);

// j'affiche la valeur de la carte sur le serial
Serial.println(myKey);

bool accessOk = 0;
// je boucle sur toutes les valeurs des clés autorisés et si je la trouve accesOK => 1
for(int y = 0; y < (sizeof(keyOk)/sizeof(String)) ; y++){

  if(keyOk[y] == myKey){
    accessOk = 1;
  }

}

  // Halt PICC, fin de lecture de carte
  rfid.PICC_HaltA();

  // Stop encryption on PCD
  rfid.PCD_StopCrypto1();

// La carte as t'elle acces , si oui led verte , si non led rouge
  if(accessOk == 1){
    digitalWrite(PIN_LED_VERTE,HIGH);
  }
  else{
    digitalWrite(PIN_LED_ROUGE,HIGH);
  }

// on eteind les led apres un delais
delay(2000);
digitalWrite(PIN_LED_VERTE,LOW);
digitalWrite(PIN_LED_ROUGE,LOW);

}
