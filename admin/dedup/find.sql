

start=1000

while [ $start -lt 41000 ]
do
stop=`expr $start + 100`

echo "
select distinct CONCAT(ch1.name,';',ch1.id,';',ch2.name,';',ch2.id)
from churches ch1
left join churches ch2 on ch1.id<>ch2.id and ch2.active=1 and ch2.successor is null
and geo_distance(ch1.lat,ch1.lng,ch2.lat,ch2.lng)<0.3 
where ch1.id>=$start and ch1.id<$stop and ch1.successor is null and ch1.active=1 and ch2.id is not null;
" | mysql -h 173.194.250.90 -u kiedymsza --password=crucemtuam podstawski  >> /home/piotr/www/kiedymsza/admin/dedup/dedup.csv

start=$stop

done

exit
