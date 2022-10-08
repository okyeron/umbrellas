#!/usr/bin/env bash

sudo apt update
sudo apt-get update
sudo apt-get install -y git bc g++ make i2c-tools libudev-dev libevdev-dev liblo-dev libavahi-compat-libdnssd-dev libasound2-dev libncurses5-dev
sudo apt install -y rpi-eeprom
sudo apt install -y php php-fpm nginx

sudo adduser pi www-data
sudo adduser www-data audio

sudo chmod 644 install/systemd/*
sudo cp install/systemd/ttymidi* /etc/systemd/system

sudo systemctl enable ttymidi0.service 
sudo systemctl enable ttymidi1.service 
sudo systemctl enable ttymidi2.service 
sudo systemctl enable ttymidi3.service 
sudo systemctl enable ttymidi4.service 

sudo sed -i s/console=serial0,115200// /boot/cmdline.txt
sudo cp --remove-destination /home/pi/umbrellas/install/boot/config.txt  /boot/config.txt
sudo cp --remove-destination /home/pi/umbrellas/install/webserver/default  /etc/nginx/sites-available/default
sudo cp -r --remove-destination /home/pi/umbrellas/install/html/umbrellas  /var/www/html/umbrellas
sudo chown -R www-data:www-data /var/www/html/umbrellas
sudo chmod -R 755 /var/www/html/umbrellas/
sudo cp --remove-destination /home/pi/umbrellas/install/99-com.rules  /etc/udev/rules.d/99-com.rules

cd ~/umbrellas
git clone https://github.com/okyeron/ttymidi.git
cd ttymidi
make
sudo make install

cd ~/umbrellas
git clone https://github.com/mzero/amidiminder.git
cd amidiminder
make
sudo dpkg -i build/amidiminder.deb
sudo cp --remove-destination /home/pi/umbrellas/install/amidiminder.rules  /home/pi/umbrellas/amidiminder.rules
sudo chown pi:pi /home/pi/umbrellas/amidiminder.rules
sudo chmod 775 /home/pi/umbrellas/amidiminder.rules
sudo cp --remove-destination /home/pi/umbrellas/install/systemd/amidiminder.service /lib/systemd/system/amidiminder.service

sudo raspi-config nonint do_hostname "umbrellas"

sudo reboot