# -*- coding: utf-8 -*-
"""
Éditeur de Spyder

Ceci est un script temporaire.
"""

import paho.mqtt.client as mqttClient
import time
import requests

def on_connect(client, userdata, flags, rc):
 
    if rc == 0:
 
        print("Connected to broker")
 
        global Connected               
        Connected = True                 
 
    else:
 
        print("Connection failed")
 
    #Méthode appelée lors de la réception d'un message via le broker
def on_message(client, userdata, message):
    print (message.payload)
    global Message
    global Data
    global Url
    Message = str(message.payload.decode('utf-8'))
    Data = {'uid': Message}
    request = requests.post('https://httpbin.org/post', data = Data)
    print (request.text)
    print (r.json())
    
    
#Variables globales
Connected = False
Url = ""
Data = {}
Message = ""

broker_adress = "localhost"
port = 1883

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
    
