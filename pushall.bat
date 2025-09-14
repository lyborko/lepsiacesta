@REM Dump databázy
mysqldump --host="localhost" --user="root" --password="" --databases lepsiacesta > "C:/xampp/htdocs/lepsiacesta/db_lepsiacesta.sql"

ECHO =========================
rar a C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.rar C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql
ECHO ==========================

del C:\xampp\htdocs\lepsiacesta\db_lepsiacesta.sql

REM Git add všetkých zmien
git add -A

REM Commit s parametrom (ak nie je zadaný, použije default message)
set arg1=%1
if "%arg1%"=="" set arg1=Automated commit
git commit -m "%arg1%"

REM Push na remote
git push origin master