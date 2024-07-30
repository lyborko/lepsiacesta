call drush sql:dump --result-file=C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql
@REM mysqldump -u root lepsiacesta > db_lepsiacesta.sql
ECHO =========================
rar a C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.rar C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql
ECHO ==========================
del C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql