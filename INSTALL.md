# UMBRELLAS INSTALL

### Start with a fresh copy of RasPiOS (Legacy/Bullseye)

Use the [Raspberry Pi Imager](https://www.raspberrypi.com/software/) tool (Do not use [balana etcher](https://www.balena.io/etcher/?ref=etcher_menu) in this case as the RasPi tool adds some extra features we need) If Pi Imager does not work on your system you will need to configure your user/password manually - [See the documentation](https://www.raspberrypi.com/documentation/computers/configuration.html#configuring-a-user).

Raspberry Pi OS with desktop is recommended if you want to occasionally connect an HDMI display or run other software. Raspberry Pi OS Lite should also be fine for a fully headless setup.

Raspberry Pi Imager will handle the disk image download for you - you should select the most recent Raspberry Pi OS (32-bit) from the operating system menu - or you can choose others. 

Before you "Write" the image - click the "gear" icon and set advanced options. Here you will want to enable SSH, set username and password and enable WiFi. In Raspberry Pi Imager __be sure to create a `pi` user. __ (this is required by the install script). Note - Do not create a differently named user as various processes expect the primary user to be `pi`. In advanced options, you can also set your locale, timezone, etc. 

If you want to do that config manually - Then after the image is burned, re-mount the SD Card and add an `ssh` and a `wpa-supplicant.conf` file to the to the boot partition. For ssh this is just an empty text file named `ssh`. [See the documentation for details](https://www.raspberrypi.com/documentation/computers/configuration.html#setting-up-a-headless-raspberry-pi) on setting up `wpa-supplicant.conf` file to enable wifi for headless use.


### First boot

The pi should go through a process of starting and then rebooting 

If running desktop with a monitor attached, follow on-screen wizard for setup.

If running headless you'll want to connect over SSH. Use a tool like [LanScan](https://apps.apple.com/us/app/lanscan/id472226235) to find the IP address.


### Download umbrellas code and run install

```
sudo apt-get install git
cd ~
git clone https://github.com/okyeron/umbrellas.git
cd umbrellas
./install.sh
```

At which point the pi will reboot.


### Flash firmware (UF2 file) to QTPy  

First find the firmware UF2 file in the /umbrellas_host_firmware directory.  

To load the firmware, connect the QTPy to your computer and then double click the reset button on the QTPy to drop it into bootloader mode. 
This should now show you a QTPY_BOOT drive on your computer desktop. Drag the UF2 file to QTPY_BOOT and it should dismount/reboot and come back as the *umbrellas_host*.  



# OPTIONAL

### protokol (desktop app for midi monitoring)
```
wget https://hexler.net/pub/protokol/protokol-0.4.4.86-linux-armhf.deb
sudo apt-get install ./protokol-0.4.4.86-linux-armhf.deb
sudo rm protokol-0.4.4.86-linux-armhf.deb
```

### osmid  
```
sudo apt install cmake -y
sudo apt-get -y install libx11-dev
git clone https://github.com/llloret/osmid.git
cd osmid
mkdir build
cd build
cmake ..
make
```

