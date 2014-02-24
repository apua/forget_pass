#!/bin/sh

PATH='/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin'

# checks:
# log and backup should be root only
# file lock

#echo "vars:" $1 $2 # for debug
#echo "num of vars:" $# # for debug


# initial variables
username=$1
password=$2
md5_hash=`openssl passwd -1 $password`

yp_dir='/var/yp'
bk_dir='backup'
passwd="$yp_dir/master.passwd"
backup="$bk_dir/master.passwd.`echo $(ls $bk_dir | wc -l)`"
                                        
date=`/bin/date '+%Y-%m-%d %H:%M:%S'`
logfile='chpass.log'

#echo $md5_hash $backup $date # for debug


# backup original username/password in YP
grep $username $passwd | awk -F':' -vd="$date" '{print d,$1":"$2}' >> $logfile


# change password 
# 1. backup
cp -p $passwd $backup
# 2. modify
sed -E "s:^($username\:)[^\:]*(.*)$:\1$md5_hash\2:" < $backup > $passwd
# 3. make
(cd $yp_dir && make) >> $logfile

#diff $backup $passwd # for debug
