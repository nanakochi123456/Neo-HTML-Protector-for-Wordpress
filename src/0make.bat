@echo off
set VERSION=0.1.8
: https://github.com/brix/crypto-js/tags
set CRYPTOJS=4.2.0
set NAME=neo-html-protector
set FTP=x:\ftp\pub\Wordpress\%NAME%\snapshot
set BROTLI=wsl brotli --quality=11 
set GZIP=wsl 7za a -tgzip -mx9 -mpass=10 -mfb=256
set CLOSURE=wsl npx google-closure-compiler --compilation_level=SIMPLE_OPTIMIZATIONS --assume_function_wrapper=false --rewrite_polyfills=false
rem --assume_function_wrapper
rem ADVANCED_OPTIMIZATIONS
rem WHITESPACE_ONLY
rem SIMPLE_OPTIMIZATIONS

@echo on
%CLOSURE% --js=js/neo-html-protect.js --js_output_file=js/neo-html-protect.min.js  --externs js/externs.js
:wsl rm js/neo-html-protect.min.js.br
:wsl rm js/neo-html-protect.min.js.gz
:%BROTLI% js/neo-html-protect.min.js
:%GZIP% js/neo-html-protect.min.js.gz js/neo-html-protect.min.js
wsl perl build/makeuninstaller.pl > classes/uninstall-getoptions.php

:wsl curl -o js/crypto-js.js https://cdnjs.cloudflare.com/ajax/libs/crypto-js/%CRYPTOJS%/crypto-js.js

:wsl sleep 1

:wsl curl -o js/crypto-js.min.js https://cdnjs.cloudflare.com/ajax/libs/crypto-js/%CRYPTOJS%/crypto-js.min.js
:wsl rm js/crypto-js.min.js.br
:wsl rm js/crypto-js.min.js.js

:%BROTLI% js/crypto-js.min.js
:%GZIP% js/crypto-js.min.js.gz js/crypto-js.min.js

@echo off
:pause

:pause
@echo off
: 日付
for /f "tokens=2 delims==" %%I in ('"wmic os get localdatetime /value"') do set datetime=%%I
:DT=%datetime:~0,4%%datetime:~4,2%%datetime:~6,2%
copy ..\*.md .
echo Snapshot %datetime:~0,14%
call 2version.bat %VERSION% %datetime:~0,14%

@echo on
wsl 7z a -t7z -mx9 %NAME%-%datetime:~0,14%.7z *.bat *.sh *.php *.md *.txt cache/*.txt build/* classes/* js/*.js languages/*

@echo off
copy %NAME%-%datetime:~0,14%.7z %FTP%
del *.md
del %NAME%-%datetime:~0,14%.7z
pause
