#!/bin/bash
#<?php die();?>
source /etc/profile
umask 002
ulimit -c unlimited
base_dir=$(pwd)
php_exe="/usr/local/php7/bin/php"

php="${base_dir}/logservice.php --host 0.0.0.0 --port 12150 --worker 1 --task 2"
run="${php_exe} $php"
echo $run
count=1
for((i=1;i<=5;i++));do 
    count=`ps -fe |grep "$run" | grep -v "grep" | wc -l`
    if [ $count -eq 1 ]; then
        break
    fi
    sleep 0.1
done
ret=0
if [ $count -le 1 ]; then
    $(ps -eaf |grep "$php" | grep -v "grep"| awk '{print $2}'|xargs kill -9)
    $(ps -eaf |grep "$php" | grep -v "grep"| awk '{print $2}'|xargs kill -9)
    $(ps -eaf |grep "$php" | grep -v "grep"| awk '{print $2}'|xargs kill -9)
    sleep 2
    ulimit -c unlimited
    $run >/dev/null 2>&1 &
    ret=2
else
    ret=1
fi
echo 'ok'
