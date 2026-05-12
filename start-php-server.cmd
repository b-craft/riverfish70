@echo off
chcp 65001 >nul
cd /d "%~dp0"
where php >nul 2>nul
if errorlevel 1 (
    echo PHP не найден в PATH. Установите PHP ^(например с windows.php.net^) или используйте OpenServer / XAMPP.
    pause
    exit /b 1
)
echo.
echo  Встроенный сервер PHP в этой папке:
echo  http://127.0.0.1:8080/cms.html  — конструктор
echo  http://127.0.0.1:8080/index.html — сайт
echo.
echo  Остановка: Ctrl+C
echo.
php -S 127.0.0.1:8080
