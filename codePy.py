# -*- coding: utf-8 -*-
"""
Éditeur de Spyder

Ceci est un script temporaire.
"""

import paho.mqtt.client as mqttClient
import time
import requests
import mysql.connector


#-----------------------------------------------------------Variables globales--------------------------------
Connected = False
Url = ""
Data = {}
Message = ""
HiddenMessage = ""
Key = 7

#--------------------------Méthode appelée lors de la connexion au broker MQTT-----------------------------------
def on_connect(client, userdata, flags, rc):
 
    if rc == 0:
 
        print("Connected to broker")
 
        global Connected               
        Connected = True                 
 
    else:
 
        print("Connection failed")
 
#--------------Méthode appelée lors de la réception d'un message via le broker----------------------------------
def on_message(client, userdata, message):
    print (message.payload)
    global Message
    global Data
    global Url
    global HiddenMessage
    global Key
    Message = str(message.payload.decode('utf-8'))[4:]
    messageList1 = [ord(c) for c in Message]
    print (messageList1)
    messageList2 = [item - Key for item in messageList1]
    print (messageList2)
    messageList3 = [chr(k) for k in messageList2]
    print (messageList3)
    messageList3.reverse()
    print (messageList3)
    messageList4 = [messageList3[1],messageList3[0],messageList3[3],messageList3[2],messageList3[5],messageList3[4],messageList3[7],messageList3[6]]
    print (messageList4)
    HiddenMessage = "".join(messageList4)
    print (HiddenMessage)
    
    try:
        
        db = mysql.connector.connect(
            host="localhost",
            user="root",
            passwd="root",
            database="test"
            )

        curseur = db.cursor()
        req = "SELECT acces FROM uids WHERE uid =%s "
        uid = HiddenMessage
        curseur.execute(req)
        retour = curseur.fetchone()
        acces = bool (retour[0])
        print (acces)
    
    except mysql.connector.Error as error:
        print ("Erreur à la connexion à la base de données", error)
    finally:
        if (db.is_connected()):
            curseur.close()
            db.close()
            print ("Fin de la connexion à la base de données MySQL")
    

#Variables qui concernent le broker MQTT.
#On part de l'hypothèse que le broker se trouve sur le raspy et fonctionne donc en localhost sur le port 1883 (par défaut)
broker_adress = "localhost"
port = 1883

#Définition du client MQTT Python avec appelle des méthodes de connexion et de réception d'un message via le protocole MQTT
client = mqttClient.Client("Python")
client.on_connect = on_connect
client.on_message = on_message

client.connect(broker_adress, port=port)
client.loop_start()

while Connected != True:
    time.sleep(0.1)
    
client.subscribe("guizard/hodor/uid")

try:
    while True:
        time.sleep(1)
    
except KeyboardInterrupt:
    print("exiting")
    client.disconnect()
    client.loop_stop()
    
