unrar e db_lepsiacesta.rar
@REM mysqldump -u root lepsiacesta_db > lepsiacesta_db.sql
ECHO ==========================
mysql --host="localhost" --user="admin" --database="lepsiacesta_db" --password="klingac" < "C:/xampp/htdocs/lepsiacesta/db_lepsiacesta.sql"
ECHO ==========================
del C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql