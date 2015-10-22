
cd `dirname $0`/tmp
rm -rf js/pickadate.js/lib/themes-source
rm -f js/pickadate.js/*.md
rm -f js/pickadate.js/lib/translations/*.md
rm -f js/lazygrid/README.md
rm -rf rest/tools
find . -name .cache | xargs rm -rf
