if [ ! "$3" ]
then
	echo "$0 adresat url-kosciola log"
	exit
fi

cd `dirname $0`


sed "s/TO/$1/g" <header >x
sed "s/URL/$2/g" <body | php -r "echo chunk_split(base64_encode(iconv('utf-8','iso-8859-2',file_get_contents('php://stdin'))));" >>x


sudo sendmail -t -f piotr.podstawski@kiedymsza.pl <x

echo "$1" >> $3
