#!/bin/sh

ZIP_FILE_NAME=shopgate-shopware5-sfs-${TAG_NAME}.zip

rm -rf src/SgateShipFromStore/vendor release/package $ZIP_FILE_NAME
mkdir -p release/package
composer install -vvv --no-dev
rsync -av --exclude-from './release/exclude-filelist.txt' ./src/ release/package/
rsync -av ./README.md release/package/SgateShipFromStore/
rsync -av ./LICENSE.md release/package/SgateShipFromStore/
rsync -av ./CONTRIBUTING.md release/package/SgateShipFromStore/
rsync -av ./CHANGELOG.md release/package/SgateShipFromStore/
cd release/package
zip -r ../$ZIP_FILE_NAME .
cd ..
rm -rf package
cd ..
rm -rf src/SgateShipFromStore/vendor
