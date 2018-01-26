#!/bin/bash

PLUGIN_PATH=/var/packages/VideoStation/target/plugins/syno_themoviedb
PLUGIN_ORIG_TGZ=${PLUGIN_PATH}.orig.tgz


DOWNLOAD_URL=$(\
  curl -s https://api.github.com/repos/jemyzhang/synoDoubanVideoInfo/releases/latest \
  | grep "browser_download_url.*tar" \
  | cut -d '"' -f 4 \
  )
VERSION=$(echo $DOWNLOAD_URL | sed 's#^.*/download/\(.*\)/douban.tar#\1#')
INSTALLED_VERSION=
DOWNLOADED_FILE=/tmp/douban.tar
VERSION_FLAG=${PLUGIN_PATH}/.douban.plugin

if [ -e ${VERSION_FLAG} ]; then
  INSTALLED_VERSION=$(cat ${VERSION_FLAG})
fi

if [ x"$INSTALLED_VERSION"x = x"$VERSION"x ]; then
  echo "The latest version of plugin was installed"
  exit 0
fi

if [ -z "$INSTALLED_VERSION" ]; then
  echo "The fresh installation, backing up the orignal plugins"
  tar czf ${PLUGIN_ORIG_TGZ} -C ${PLUGIN_PATH} .
fi

wget --no-check-certificate ${DOWNLOAD_URL} -O ${DOWNLOADED_FILE}
mkdir -p ${PLUGIN_PATH}
tar -xf ${DOWNLOADED_FILE} -C ${PLUGIN_PATH}
echo $VERSION > $VERSION_FLAG
chown VideoStation:VideoStation -R ${PLUGIN_PATH}

rm -f ${DOWNLOADED_FILE}

if [ -z "$INSTALLED_VERSION" ]; then
  echo "Plugin version: $VERSION installed"
else
  echo "Plugin updated from $INSTALLED_VERSION to $VERSION"
fi

