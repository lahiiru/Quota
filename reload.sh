#!/bin/bash  

#HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
#sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX .
#sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX .
     
echo Reloading...
sudo php app/console cache:clear --env=prod
sudo chown -R apache app/cache/prod
sudo chown -R apache app/cache
echo Done.