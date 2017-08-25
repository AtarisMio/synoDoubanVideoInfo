#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

clear;

cd /tmp/;

rm -rf /var/packages/VideoStation/target/plugins/syno_themoviedb.removed;
mv /var/packages/VideoStation/target/plugins/syno_themoviedb /var/packages/VideoStation/target/plugins/syno_themoviedb.removed;

mv /var/packages/VideoStation/target/plugins/syno_themoviedb.orig /var/packages/VideoStation/target/plugins/syno_themoviedb;

chmod 0755 /var/packages/VideoStation/target/plugins/syno_themoviedb/*.php;
chown VideoStation:VideoStation -R /var/packages/VideoStation/target/plugins/syno_themoviedb;

cd -;
rm -rf uninstall.sh;