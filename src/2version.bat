@echo off
set VERSION=%1
wsl cat neo-html-protector.php | wsl php -r 'echo preg_replace("/\d+\.\d+\.\d+/", "%VERSION%", file_get_contents("php://stdin"));' > temp
wsl mv temp neo-html-protector.php
wsl cat 1make.bat | wsl php -r 'echo preg_replace("/\d+\.\d+\.\d+/", "%VERSION%", file_get_contents("php://stdin"));' > temp
wsl mv temp 1make.bat
