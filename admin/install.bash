location=ENTER YOUR LOCATION
dbname=ENTER YOUR DATABASE NAME
utorid=ENTER YOUR UTORID
password=ENTER YOUR PASSWORD


sed -i 's/$UTORID/'$utorid'/g' ../api/api.php
sed -i 's/$DBPASSWORD/'$password'/g' ../api/api.php
sed -i 's/$LOCATION/'$location'/g' ../api/api.php
sed -i 's/$DBNAME/'$dbname'/g' ../api/api.php

PGPASSWORD=$password psql -h $location -d $dbname -U $utorid -f schema.sql
