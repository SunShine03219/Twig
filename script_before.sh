#!/bin/bash
_FOLDER="/home/ubuntu/fundingtracker"
if [ -d $_FOLDER ]
    then
        sudo rm -rf "${_FOLDER}/"
    else
        mkdir -p "${_FOLDER}"
fi