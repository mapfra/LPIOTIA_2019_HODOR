# LPIOTIA_2019_HODOR - CODES ARDUINO
Groupe GUIOT MEZARD - système d'ouverture de porte sans clés

LECT_RFID.ino : 
Lecture, chiffrement et publication de l'UID de la carte scannée. Positionné sur l'ESP 8266.

Dépendances :
-	ESP8266WiFi.h : Utilisation de la WiFi
-	PubSubClient.h :Souscription et la publication via un broker MQTT
-	SPI.h et MFRC522.h : Lecture d’une carte/badge RFID pour l’ESP

SERV.ino :
Utilisation d'un servo moteur. Positionné sur un Arduino Nano BLE 33.

Dépendances :
-	Servo.h : Utilisation d'une instance de Servo


DoorStatusBLE.h :
Présente l'information du statut de la porte en BLE

Dépendances :
-	ArduinoBLE.h : Utilisation des fonctionnalités BLE.