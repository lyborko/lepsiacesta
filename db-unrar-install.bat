unrar e db_lepsiacesta.rar
@REM mysqldump -u root lepsiacesta_db > lepsiacesta_db.sql
ECHO ==========================
mysql --host="localhost" --user="root" --database="lepsiacesta" --password="" < "C:/xampp/htdocs/lepsiacesta/db_lepsiacesta.sql"
ECHO ==========================
del C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql