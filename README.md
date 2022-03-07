# umbrellas
 Raspberry PI 4 MIDI Hat  
 
## Setup
 
[Get most recent RasPiOS](https://www.raspberrypi.com/software/operating-systems/)
 
Download image for `Raspberry Pi OS with desktop` or `Raspberry Pi OS Lite` 

(current setup tested on `Raspberry Pi OS with desktop, Release date: January 28th 2022`)

### Flash disk image to the sdcard

Use balenaEtcher - https://www.balena.io/etcher/

When etcher is finished it will unmount your SD card.

You can re-mount the sdcard and to add an `ssh` file and `wpa-supplicant.conf` file to the boot drive if you like. If using desktop, you can configure those on startup.  
 
## Install
 
```
git clone https://github.com/okyeron/umbrellas.git
cd umbrellas
./install.sh

```


## Bluetooth

```
turn on discoverable on umbrellas 
	- with desktop, use the menubar icon 
	- commandline
		`bluetoothctl`
		`discoverable on`

connect from your device and pair
```

Once your device is connected it should show up in the web application as a MIDI device.