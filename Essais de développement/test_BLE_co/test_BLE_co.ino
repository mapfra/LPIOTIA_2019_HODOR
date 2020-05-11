#include <ArduinoBLE.h>
#include <BLEStringCharacteristic.h>

BLEService helloThere("0000");
//BLECharacteristic helloChar("1111", BLERead | BLENotify, 20);
//BLECharacteristic helloChar("1111", BLERead | BLENotify);
BLEStringCharacteristic helloChar("1111", BLERead | BLENotify, 20);

void setup() {

Serial.begin(9600);

while(!Serial);
pinMode(LED_BUILTIN, OUTPUT);

if(!BLE.begin()) {
  Serial.println("Starting BLE failed");
}

BLE.setLocalName("GUIZARD_BLE");
BLE.setAdvertisedService(helloThere);
helloThere.addCharacteristic(helloChar);
BLE.addService(helloThere);

BLE.advertise();
Serial.println("Bluetooth device is active, waiting for connections...");
}

void loop() {

BLEDevice central = BLE.central();

if(central) {
  Serial.println("Connected to central : ");
  Serial.println(central.address());
  digitalWrite(LED_BUILTIN, HIGH);
  
while(central.connected()) {
  Serial.println("Hello there...");
  String obj = "General Kenobi";
  //obj = 'General Kenobi';
  helloChar.writeValue(obj);
  //helloChar.setValue("General Kenobi");
  delay(3000);
}
}
digitalWrite(LED_BUILTIN, LOW);
Serial.println("Disconnected from central : ");
Serial.println(central.address());
}
