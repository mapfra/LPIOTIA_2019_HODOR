# LPIOTIA_2019_HODOR
Groupe GUIOT MEZARD - système d'ouverture de porte sans clés

Scripts Python, positionnée sur le Raspberry Pi:

-	CodePy.py :
	Mise en de la commnication entre Raspberry et ESP8266 via broker MQTT avec réception, déchiffrement et vérification en base de données de l'UID lue.

-	codePyDoorStatus.py:
	Mise en place de la communication entre Raspberry et Arduino Nano BLE pour demande et récpetion du statut réel de la porte.