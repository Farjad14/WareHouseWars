---------------------LOGIN INFORMATION---------------------------
global $location= mcsdb.utm.utoronto.ca
$dbname= abbass13_309
$dbuser=abbass13
$dbpassword=90444
$dbport=90444
-----------------------------------------------------------------


PGPASSWORD=$password psql -h $location -d $dbname -U $user -f setup.sql
