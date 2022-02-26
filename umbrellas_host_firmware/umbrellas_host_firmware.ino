/* Create a "class compliant " USB to 1 MIDI IN and 1 MIDI OUT interface.

   MIDI receive (6N138 optocoupler) input circuit and series resistor
   outputs need to be connected to Serial1, Serial2 and Serial3.

   You must select MIDI from the "Tools > USB Type" menu

   This example code is in the public domain.
*/

#include <Arduino.h>
#include <Adafruit_TinyUSB.h>
#include <MIDI.h>
#include <Adafruit_NeoPixel.h> 
#include <elapsedMillis.h>
#include <Wire.h>

// DEVICE INFO FOR ADAFRUIT M0 or M4 
char mfgstr[32] = "denki-oto";
char prodstr[32] = "umbrellas-host";

// I2C
#define I2C_ADDR 0x42
#define MEM_LEN 256
uint8_t databuf[MEM_LEN];
volatile uint8_t received;

#define DEBUG      


// MIDI
Adafruit_USBD_MIDI usb_midi;

// Create the Serial and USB MIDI ports
MIDI_CREATE_INSTANCE(HardwareSerial, Serial1, MIDI1);       // MIDI1 is Hardware MIDI
MIDI_CREATE_INSTANCE(Adafruit_USBD_MIDI, usb_midi, MIDI2);  // MIDI2 is USB MIDI

unsigned long usbMidiNotes[16][127]; // array to store MIDI Note On/Off that come in via USB MIDI Host
unsigned long usbMidiCCs[16][127]; // array to store MIDI CCs that come in via USB MIDI Host


// NEOPIXEL
Adafruit_NeoPixel onePixel = Adafruit_NeoPixel(1, 11, NEO_GRB + NEO_KHZ800);
// A variable to know how long the LED has been turned on
elapsedMillis ledOnMillis;
bool activity = false;

void setup() {
//  pad_with_nulls(mfgstr, 32);
//  pad_with_nulls(prodstr, 32);
  USBDevice.setManufacturerDescriptor(mfgstr);
  USBDevice.setProductDescriptor(prodstr);
  
  MIDI1.begin(MIDI_CHANNEL_OMNI);
  MIDI2.begin(MIDI_CHANNEL_OMNI);
  MIDI1.turnThruOff();
  MIDI2.turnThruOff();

  // data init
  received = 0;
  memset(databuf, 0, sizeof(databuf));

  // setup for Follower mode, address (= 66)
  Wire.begin(I2C_ADDR);
  Wire.onReceive(receiveEvent);
  Wire.onRequest(requestEvent);

  Serial.begin(115200);

  // wait until device mounted
  while ( !USBDevice.mounted() ) delay(1);

  onePixel.begin();             // Start the NeoPixel object
  onePixel.clear();             // Set NeoPixel color to black (0,0,0)
  onePixel.setBrightness(50);   // Affects all subsequent settings

  onePixel.setPixelColor(0, 100, 0, 0);
  onePixel.show();
  delay(200);
  onePixel.clear();
  onePixel.show();              // Update the pixel state

}


void loop() {
  activity = false;
  
  int r=0, g=0, b=100;

	// PROCESS MIDI
	if (MIDI1.read()) {
		// get a MIDI IN1 (Serial) message
		midi::MidiType type = MIDI1.getType();
		byte channel = MIDI1.getChannel();
		byte data1 = MIDI1.getData1();
		byte data2 = MIDI1.getData2();

		if (type == midi::Clock) {
		  // no led activity on clock
		  //Serial.println("clock");
		  activity = false;
		  ledOnMillis = 0;
		} else {
		  activity = true;
		}

		// forward the message to USB MIDI virtual cable #0
		if (type != midi::SystemExclusive) {
		  // Normal messages, simply give the data to the MIDI2.send()
		  MIDI2.send(type, data1, data2, channel);
		} else {
		  // SysEx messages are special.  The message length is given in data1 & data2
		  unsigned int SysExLength = data1 + data2 * 256;
		  //MIDI2.sendSysEx(SysExLength, MIDI.getSysExArray(), true, 0);
		}
	}


	if (MIDI2.read()) {
		// get the USB MIDI message 
		midi::MidiType type = MIDI2.getType();
		byte channel = MIDI2.getChannel();
		byte data1 = MIDI2.getData1();
		byte data2 = MIDI2.getData2();
		//byte cable = MIDI2.getCable();

		if (type == midi::Clock) {
		  // no led activity on clock
		  activity = false;
		  ledOnMillis = 0;
		} else {
		  activity = true;
		}

		// forward this message to Serial MIDI OUT ports
		if (type != midi::SystemExclusive) {
		  // Normal messages, first we must convert MIDI2's type (an ordinary
		  // byte) to the MIDI library's special MidiType.
		  midi::MidiType mtype = (midi::MidiType)type;

		  // Then simply give the data to the MIDI library send()
		  MIDI1.send(mtype, data1, data2, channel);
  
		} else {
		  // SysEx messages are special.  The message length is given in data1 & data2
		  unsigned int SysExLength = data1 + data2 * 256;
			  //MIDI1.sendSysEx(SysExLength, MIDI2.getSysExArray(), true);
		}
	}

	// blink the LED when any activity has happened
	if (activity) {
		onePixel.setPixelColor(0, r, g, b);
		onePixel.show();
		ledOnMillis = 0;
	}
	if (ledOnMillis > 20 ) {
		onePixel.clear(); 
		onePixel.show();
	}
}
// END LOOP

// *********************************  I2C  *********************************

// function for receiving I2C messages
void receiveEvent(int count) {
  if(count<MEM_LEN) {
    // copy Rx data to databuf
//    Wire.read(databuf, count);
    // set received flag to count, this triggers main loop  
//    received += count;           

	(void)count; // avoid compiler warning about unused parameter
	while(1 < Wire.available()) // loop through all but the last
	{
		char c = Wire.read(); // receive byte as a character
		Serial.print(c);         // print the character
	}
	int x = Wire.read();    // receive byte as an integer
	Serial.println(x);         // print the integer  
	}
}


// function for receiving I2C QUERY messages
void requestEvent(){
  #ifdef DEBUG
    Serial.print("DATABUF_Q 0: "); Serial.println(databuf[0]);
    Serial.print("DATABUF_Q 1: "); Serial.println(databuf[1]);
    Serial.print("DATABUF_Q 2: "); Serial.println(databuf[2]);
    Serial.print("DATABUF_Q 3: "); Serial.println(databuf[3]);
  #endif
  
  int channel = databuf[0];
  if (channel < 1 || channel > 16) return;

  int CCnumber = (int16_t)(databuf[1] << 8 | databuf[2]);  
  if (CCnumber < 0 || CCnumber > 127) return;
  
  int value = usbMidiCCs[channel-1][CCnumber];
  if (value < 0 || value > 127) return;

  #ifdef DEBUG
    Serial.print("CC Nb: "); Serial.println(CCnumber);
  #endif
  Wire.write(usbMidiCCs[channel-1][CCnumber]);
  
}


// ***************************************************************************
// **                                HELPERS                                **
// ***************************************************************************

// Pad a string of length 'len' with nulls
void pad_with_nulls(char* s, int len) {
  int l = strlen(s);
  for( int i=l;i<len; i++) {
    s[i] = '\0';
  }
}
