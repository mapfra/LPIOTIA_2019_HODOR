/*
Branchements sur Nano:

SDA   pin 10 Blanc
SCK   pin 13 Vert
MOSI  pin 11 Jaune
MISO  pin 12 Orange
---
GND   GND    Noir
RESET pin  9 Bleu
VCC   3.3v   Rouge

*/

//##########################################################################
//                               Library
//##########################################################################

#include <SPI.h>
#include <RFID.h>

//##########################################################################
//                              Variables
//##########################################################################

int j;                   //indice de ligne du tableau 'Badge'

int UID[5]={};

RFID monModuleRFID(10,9);   //SDA,RST

//##########################################################################
//                           Base de données
//##########################################################################

int Badge[3][5]={
  {198, 84,107,144,105}, //Bleu 0
  { 90,213, 83,204, 16}, //Noir 1
  {160,110,152,168,254}, //Noir 2
  };

//##########################################################################
//                                Setup
//##########################################################################

    void setup()
    {
      Serial.begin(9600);
      SPI.begin();
      monModuleRFID.init(); 

     Serial.println("Démarage");
    }

//##########################################################################
//                               Loop
//##########################################################################

    void loop()
    {
        LectureRFID ();
    }

//##########################################################################
//                             Fonction
//##########################################################################

int LectureRFID ()
{
if (monModuleRFID.isCard()) { 
              if (monModuleRFID.readCardSerial()) {       
                Serial.print("L'UID est: ");
               
                for(int i=0;i<=4;i++)
                {
                  UID[i]=monModuleRFID.serNum[i];
                  Serial.print(UID[i],DEC);
                  Serial.print(".");
                }
                Serial.println("");
             
              for(int j=0;j<=2;j++)
                {
              if (UID[0] == Badge[j][0]
               && UID[1] == Badge[j][1]
               && UID[2] == Badge[j][2]
               && UID[3] == Badge[j][3]
               && UID[4] == Badge[j][4])
              {
                  Serial.println ("ok");
                  break;
              }
              else
              {
                  Serial.println ("Not ok");
              }         
              monModuleRFID.halt();
              }
        }
        delay(1);   
    }
 
}
