# -*- coding: utf-8 -*-
"""
@author: wilfr
"""

import paho.mqtt.client as mqttClient
import pygatt

Connected = False
DoorStatus = False

#--------------------------Méthode appelée lors de la connexion au broker MQTT---------------------------------
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
    global DoorStatus
    Message = str(message.payload.decode('utf-8'))
    if ( Message == "status"):
        #On cherche le status de la porte (ouverte ou fermée)
        if (True):
            DoorStatus = True
            client.publish("guizard/hodor/doorStatus", DoorStatus)
        else :
            DoorStatus = False
            client.publish("guizard/hodor/doorStatus", Doorstatus)
        
        
        
#--------------------------Variables propres à la connexion au broker MQTT--------------------------------------
broker_adress = "localhost"
port = 1883
client = mqttClient.Client("DoorStatus")
client.on_connect = on_connect
client.on_message = on_message
client.connect(broker_adress, port=port)
client.loop_start()

while Connected != True:
    time.sleep(0.1)
    
client.subscribe("guizard/hodor/doorStatus")

try:
    while True:
        time.sleep(1)
    
except KeyboardInterrupt:
    print("exiting")
    client.disconnect()
    client.loop_stop()