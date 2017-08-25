#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

clear;

version='1.0';

cd /tmp/;

mv -R /var/packages/VideoStation/target/plugins/syno_themoviedb /var/packages/VideoStation/target/plugins/syno_themoviedb.orig

wget --no-check-certificate "https://github.com/AtrisMio/synoDoubanVideoInfo/releases/download/$version/douban.tar" -O douban.tar;
mkdir /var/packages/VideoStation/target/plugins/syno_themoviedb;
tar -xvf douban.tar -C /var/packages/VideoStation/target/plugins/syno_themoviedb;

rm -rf ./douban.tar;

cp /var/packages/VideoStation/target/plugins/syno_themoviedb.orig/INFO;
cp /var/packages/VideoStation/target/plugins/syno_themoviedb.orig/loader.sh;

chmod 0755 /var/packages/VideoStation/target/plugins/syno_themoviedb/*.php;

chown VideoStation:VideoStation -R /var/packages/VideoStation/target/plugins/syno_themoviedb;

cd -;
rm -rf install.sh;