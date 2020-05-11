#include <ESP8266WiFi.h>

const char *ssid =  "LPiOTIA";     // replace with your wifi ssid and wpa2 key
const char *pass =  "";

WiFiClient client;
 
void setup() 
{
       Serial.begin(9600);
       delay(500);
               
       Serial.println("Connexion à -->");
       Serial.println(ssid); 
 
       WiFi.begin(ssid, pass); 
       while (WiFi.status() != WL_CONNECTED) 
          {
            delay(500);
            Serial.print(".");
          }
      Serial.println("");
      Serial.println("Connecté !");
      Serial.println("IP locale --> ");
      Serial.println(WiFi.localIP());
}
 
void loop() 
{      
  if (WiFi.status() == WL_CONNECTED)
    {
      delay(10000);
      Serial.println("Toujours connecté !");
    }
    else {
      Serial.println("Déconnecté ...");
    }
}
