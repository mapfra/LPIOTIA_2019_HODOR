- # LPIOTIA_2019_HODOR
Groupe GUIOT MEZARD - système d'ouverture de porte sans clés

Scripts Python, positionnée sur le Raspberry Pi:

-	CodePy.py :
	Mise en de la commnication entre Raspberry et ESP8266 via broker MQTT avec réception, déchiffrement et vérification en base de données de l'UID lue.

-	codePyDoorStatus.py:
	Mise en place de la communication entre Raspberry et Arduino Nano BLE pour demande et récpetion du statut réel de la porte.
	
ApplicationWebResponsive, positionnée sur le Raspberry Pi :

- A copier sur le serveur Apache2 du Raspberry Pi dans le dossier : /var/www/html/
- Fonctionnel avec php 7.3
- Activation du HTTPS à l'aide de : https://variax.wordpress.com/2017/03/18/adding-https-to-the-raspberry-pi-apache-web-server/ 
(Attention erreur dans une des dernières commandes, rajouter l'extension .conf au fichier de config  : $ sudo nano raspberrypi_orwhatever.conf et par la suite lancer : $ sudo a2ensite raspberrypi_orwhatever.conf)
- Faire attention à ce que le port activé et écouté pour le HTTPS soit bien le 443
- Installation du broker MQTT, MQTT mosquitto + MQTT mosquitto clients via Raspbian + faire attention que le port écouté soit le 1883