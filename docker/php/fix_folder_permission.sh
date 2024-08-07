#!/bin/bash

echo "--------------pwd----------------"
echo `pwd`
echo "--------------pwd----------------"


# setting permissions
chmod -R 777 storage
chmod -R 777 storage/framework/sessions
chmod -R 777 bootstrap/cache
chmod -R 777 storage/logs/

#creating today log file
#DATE=`date +%Y-%m-%d`
#-u ec2-user touch $appDir/storage/logs/laravel-${DATE}.log
