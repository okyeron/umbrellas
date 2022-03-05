# Umbrellas

a MIDI interface/router/host add-on for the Raspberry Pi 4

# Features:  
- 4 Hardware MIDI Ins - TRS/DIN (2 TRS, 2 are TRS or DIN)  
	Auto-sensing - Accepts either TRS Type-A or Type-B  

- 4 Hardware MIDI Outs - TRS/DIN (2 TRS, 2 are TRS or DIN)  
	TRS are switchable between Type-A or Type-B  

- 4 USBMIDI ports (and can support USB hubs for more)

- 1 USB Host adapter (USB-C with QtPy/XAIO add-on)*

- MIDI activity leds

- On/Off safe shutdown switch for Pi

- Web-based configuration tool for managing MIDI connections

- Uses stock RasPiOS (Desktop or Lite) with some configuration

Demo video: https://www.instagram.com/tv/CYU_olaNODE/  

\* The USB Host adapter is used to connect to a "USB Host" like a computer, iPad, norns, organelle, etc.

### see INSTALL for setup info

# FAQ: 

Q: Will this work with a Raspberry Pi 2/3/3b/3b+?
A: It's designed for Pi4 because we can take advantage ot it's 5 UARTs for the hardware MIDI I/O.  With Pi 2/3/3b/3b+ only 2 of the hardware MIDI ports will work as there's only 2 uarts.


