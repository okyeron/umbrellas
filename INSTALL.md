# UMBRELLAS INSTALL SETUP

### raspi-config

### installs  

```
sudo apt update
sudo apt-get update
sudo apt-get install -y git bc g++ make i2c-tools libudev-dev libevdev-dev liblo-dev libavahi-compat-libdnssd-dev libasound2-dev libncurses5-dev
sudo apt install rpi-eeprom
```

```
git clone https://github.com/okyeron/umbrellas.git
cd umbrellas
```

### copy systemd units  

```
sudo chmod 644 install/systemd/*
sudo cp install/systemd/* /etc/systemd/system

sudo systemctl enable ttymidi0.service 
sudo systemctl enable ttymidi1.service 
sudo systemctl enable ttymidi2.service 
sudo systemctl enable ttymidi3.service 
sudo systemctl enable ttymidi4.service 
```

### copy boot configs  

```
sudo cp --remove-destination /home/pi/umbrellas/install/boot/config.txt  /boot/config.txt
sudo cp --remove-destination /home/pi/umbrellas/install/boot/cmdline.txt  /boot/cmdline.txt
```


### Not available on bullseye
	#patchage
	#aconnectgui
	sudo apt-get install -y aconnectgui, patchage


### GPIO shutdown/start ref links
https://github.com/seamusdemora/PiFormulae/blob/master/docs/gpio-shutdown_20210620.md
https://www.stderr.nl/Blog/Hardware/RaspberryPi/PowerButton.html#comments

### php
```
sudo apt install php php-fpm
```

### nginx (or lighttpd?)
```
sudo apt install nginx

sudo adduser pi www-data
sudo adduser www-data audio
```
edit `/etc/nginx/sites-available/default`  
uncomment php stuff and change default directory  

### ttymidi  
```
git clone https://github.com/okyeron/ttymidi.git
cd ttymidi
make
sudo make install
```

### amidiminder  
```
git clone https://github.com/mzero/amidiminder.git
cd amidiminder
make
sudo dpkg -i build/amidiminder.deb
```
remove default setup from `/etc/amidiminder.rules`  

### protokol -- OPTIONAL
```
wget https://hexler.net/pub/protokol/protokol-0.4.2.84-linux-armhf.deb
sudo apt-get install ./protokol-0.4.2.84-linux-armhf.deb
sudo rm protokol-0.4.2.84-linux-armhf.deb
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

### qjackctl ?

### desktop icons
/usr/share/applications

### move udev rules  
sudo cp install/99-com.rules /etc/udev/rules.d/99-com.rules

### paths  
/etc/systemd/system
/var/www/html


# UMBRELLAS WORK LIST
-----------------------------
* figure out bluetooth midi setup

* Bullseye has Bluetooth Midi enabled?

## needs some editing to only do the BT setup
```
git clone https://github.com/Mylab6/PiBluetoothMidSetup
cd PiBluetoothMidSetup
sudo python3 setup_midi.py
```

### raspi-config
`sudo raspi-config`


### /boot/config.txt setup / reference  

```
hdmi_force_hotplug=1
config_hdmi_boost=10
hdmi_group=2
hdmi_mode=87
hdmi_cvt 1024 600 60 6 0 0 0

# Uncomment some or all of these to enable the optional hardware interfaces
dtparam=i2c_arm=on
dtparam=i2s=on
#dtparam=spi=on

# Additional overlays and parameters are documented /boot/overlays/README
force_turbo=1
enable_uart=1

core_freq=500
core_freq_min=500
dtoverlay=miniuart-bt
#dtparam=krnbt=on

dtoverlay=uart0
dtoverlay=midi-uart0
#dtoverlay=uart1
#dtoverlay=midi-uart1
dtoverlay=uart2
dtoverlay=midi-uart2
dtoverlay=uart3
dtoverlay=midi-uart3
dtoverlay=uart4
dtoverlay=midi-uart4
dtoverlay=uart5
dtoverlay=midi-uart5

```
