#!/bin/sh

PATH=''

# checks
echo "vars:" $1 $2
echo "num of vars:" $#
# log and backup should be root only
# file lock


# initial variables
username=$1
password=$2
md5_pass=`/usr/bin/openssl passwd -1 $password`
logfile='password_change.log'
yp_dir='/var/yp'
passwd='/var/yp/master.passwd'
backup='master.passwd.last'
date=`/bin/date "+%Y-%m-%d %H:%M:%S"`


# backup original username/password in YP
/usr/bin/grep $username $passwd \
    | /usr/bin/awk -F':' -vd="$date" '{print d,$1":"$2}' \
    >> $logfile

# change password 
# 1. backup
/bin/cp -p $passwd $backup
# 2. modify
/usr/bin/sed -E 's/^('$username':)[^:]*(.*)$/\1'$md5_pass'\2/' < $backup > $passwd
# 3. make
# (cd $yp_dir && make) >> $logfile
