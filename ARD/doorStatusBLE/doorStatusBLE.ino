#include <ArduinoBLE.h>

/*
 * On utilise les spécifications fournies via le site internet www.bluetooth.com/specifications/gatt/services/ 
 * Respect des spécifications des services GATT - Ici on souhaite rendre disponible le status de la porte,
 * on va donc utiliser 'Generic Access' d'où l'UUID '0x1800'.
 */

 // On définit un service
BLEService doorStatusService("1800");

// Et une caractéristiques associées
BLEBoolCharacteristic doorStatusChar("0000", BLERead | BLENotify);

bool oldDoorStatus = false;

void setup() {
Serial.begin(9600);
while (!Serial);

  // On initialise le device BLE
  if (!BLE.begin()) {
    Serial.println("Erreur démarrage du BLE");

    while (1);
  }

  // On définit un nom local pour le device BLE ainsi que le service et la caractéristique que l'on souhaite rendre disponible
  BLE.setLocalName("DoorStatusMonitor");
  BLE.setAdvertisedService(doorStatusService);
  doorStatusService.addCharacteristic(doorStatusChar);
  BLE.addService(doorStatusService);
  doorStatusChar.writeValue(oldDoorStatus);

  // On peut enfin rendre 'disponible' notre device BLE
  BLE.advertise();
  Serial.println("Device BLE actif, en attente de connexion");

  

}

void loop() {
  
  // On se met en attente d'un central BLE (ici le raspPi)
  BLEDevice central = BLE.central();

  // En cas de connexion on indique l'adresse du central connecté et on allume une des LED
  if (central) {
    Serial.println("Connecté au central");
    Serial.println(central.address());
    digitalWrite(LED_BUILTIN, HIGH);

    while (central.connected()) {
      delay(2000);
      //updateDoorStatus();
    }
  }
  // En cas de déconnexion on éteind la LED et l'on affiche les informations du central
  digitalWrite(LED_BUILTIN, LOW);
  oldDoorStatus = !oldDoorStatus;
  Serial.println("Déconnexion du central");
  Serial.println(central.address());
}

/**
 * On définit la méthode actualisant le status de la porte - doit se trouver ici le code relatif au servomoteur.
 * On doit pour voir récupérer le status du servo moteur (ouvert ou fermé). Tel quel le code ne permet pas en place cette fonctionnalité
 * Le status de la porte est simulé (alternance ouvert/fermé)
 */
 void updateDoorStatus() {
  if (oldDoorStatus = false) {
    oldDoorStatus = true;
  } else {
    oldDoorStatus = false;
  }
 }
