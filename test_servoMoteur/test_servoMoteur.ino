#include <Servo.h>

Servo myservo;

int pos = 0;

int state; int flag=0;

void setup()

{

myservo.attach(9);

Serial.begin(9600);

myservo.write(60);

delay(1000); }

void loop()

{

if(Serial.available() > 0)

{

state = Serial.read();

flag=0;

} // if the state is '0' the DC motor will turn off

if (state == '0')

{

myservo.write(8);

delay(1000);

Serial.println("Door Locked");

}

else if (state == '1')

{

myservo.write(55);

delay(1000);

Serial.println("Door UnLocked");

}

}
