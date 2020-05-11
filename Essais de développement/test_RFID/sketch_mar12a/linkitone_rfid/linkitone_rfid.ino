//Libraries
#include <SPI.h>
#include <MFRC522.h>
//End Libraries


//RFID
#define Reset_Pin 9
#define Slave_Select_Pin 8
#define times 5
//Bytes
byte sector = 0;
byte blockAddr = 0; //Access sectors within card
byte trailerBlock = 1;
//End Bytes
//End RFID

//Instances
MFRC522 mfrc522(Slave_Select_Pin, Reset_Pin);

MFRC522::MIFARE_Key key;
//End Instances

signed long timeout;

void setup() {
  Serial.begin(115200);
  while(!Serial);
  // keep retrying until connected to AP
  Serial.println("Connecting to AP");

  SPI.begin();
  mfrc522.PCD_Init();
  
 for (byte i = 0; i < 6; i++) {   // Prepare the key (used both as key A and as key B)
  key.keyByte[i] = 0xFF;        // using FFFFFFFFFFFFh which is the default at chip delivery from the factory
  }
  
  dump_byte_array(key.keyByte, MFRC522::MF_KEY_SIZE);     //Get key byte size
  timeout = 0;
  delay(100);
  Serial.println("Start scanning a card");
  delay(500);
}

void loop() {
   // Look for new cards
    if ( ! mfrc522.PICC_IsNewCardPresent())
    {
        return;
    }
    Serial.println("Got a card");

    // Select one of the cards
    if ( ! mfrc522.PICC_ReadCardSerial())
        return;

    byte piccType = mfrc522.PICC_GetType(mfrc522.uid.sak);

    // Check for compatibility with Mifare card
    if (    piccType != MFRC522::PICC_TYPE_MIFARE_MINI
        &&  piccType != MFRC522::PICC_TYPE_MIFARE_1K
        &&  piccType != MFRC522::PICC_TYPE_MIFARE_4K) {
        return;
    }
    
  byte status;
  byte buffer[18];
  byte size = sizeof(buffer);
  
  // Read data from the block
  status = mfrc522.MIFARE_Read(blockAddr, buffer, &size);
  if (status != MFRC522::STATUS_OK) {
    Serial.print(F("MIFARE_Read() failed: "));
  }

  delay(50);
 
 
}


// TURN THE BUFFER ARRAY INTO A SINGLE STRING THAT IS UPPERCASE WHICH EQUALS OUR ID OF THE SECTOR AND BLOCK
String dump_byte_array(byte *buffer, byte bufferSize) {
          String out = "";
    for (byte i = 0; i < bufferSize; i++) {
        //Serial.print(buffer[i] < 0x10 ? " 0" : " ");
        //Serial.print(buffer[i], HEX);
        out += String(buffer[i] < 0x10 ? " 0" : " ") + String(buffer[i], HEX);
    }
    out.toUpperCase();
    out.replace(" ", "");
    return out;
}
//END DUMP_BYTE_ARRAY
