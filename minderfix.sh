#!/usr/bin/env bash

sudo rm -r /home/pi/umbrellas/ttymidi/amidiminder

cd ~/umbrellas
# git clone https://github.com/mzero/amidiminder.git
# cd amidiminder
# make
# sudo dpkg -i build/amidiminder.deb
wget https://github.com/mzero/amidiminder/releases/download/0.70/amidiminder_0.70_armhf.deb
sudo dpkg -i amidiminder_0.70_armhf.deb

sudo cp --remove-destination /home/pi/umbrellas/install/amidiminder.rules  /home/pi/umbrellas/amidiminder.rules
sudo chown pi:pi /home/pi/umbrellas/amidiminder.rules
sudo chmod 775 /home/pi/umbrellas/amidiminder.rules
sudo cp --remove-destination /home/pi/umbrellas/install/systemd/amidiminder.service /lib/systemd/system/amidiminder.service
