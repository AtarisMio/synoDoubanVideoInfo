#!/bin/bash

PLUGIN_PATH=/var/packages/VideoStation/target/plugins/syno_themoviedb
PLUGIN_ORIG_TGZ=${PLUGIN_PATH}.orig.tgz

if [ ! -f ${PLUGIN_ORIG_TGZ} ]; then
  echo "douban plugin was not installed"
  exit 1
fi

rm -rf $PLUGIN_PATH

mkdir -p ${PLUGIN_PATH}
tar xf ${PLUGIN_ORIG_TGZ} -C ${PLUGIN_PATH}

chown VideoStation:VideoStation -R ${PLUGIN_PATH}

rm -f ${PLUGIN_ORIG_TGZ}
