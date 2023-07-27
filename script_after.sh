#!/bin/bash
_FOLDER="/home/ubuntu/fundingtracker"
if [ -d $_FOLDER ]
    then
        sudo chown www-data:ubuntu "${_FOLDER}/" -R
fi