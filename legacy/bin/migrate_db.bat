@echo off

if exist c:\xampp\php\php.exe (
	set php=c:\xampp\php\php.exe
	goto exec
)
if exist d:\xampp\php\php.exe (
	set php=d:\xampp\php\php.exe
	goto exec
)
echo Unable to find PHP
goto done

:exec
%php% db.php migrate

:done
pause