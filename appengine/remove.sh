
cd `dirname $0`/tmp
rm -rf js/pickadate.js/lib/themes-source
rm -rf js/pickadate.js/*.md
rm -rf js/pickadate.js/lib/translations/*.md
rm -rf rest/tools
find . -name .cache | xargs rm -rf
