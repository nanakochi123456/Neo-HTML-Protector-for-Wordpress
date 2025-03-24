@echo off
set VERSION=0.0.32
set NAME=neo-html-protector
set FTP=x:\ftp\pub\Wordpress\%NAME%\snapshot
set CLOSURE=wsl npx google-closure-compiler --compilation_level SIMPLE_OPTIMIZATIONS --rewrite_polyfills false  --assume_function_wrapper
rem ADVANCED_OPTIMIZATIONS
rem WHITESPACE_ONLY
rem SIMPLE_OPTIMIZATIONS

@echo on
%CLOSURE% --js=js/neo-html-protect.js --js_output_file=js/neo-html-protect.min.js  --externs js/externs.js
@echo off
:pause

:pause
@echo off
: 日付
for /f "tokens=2 delims==" %%I in ('"wmic os get localdatetime /value"') do set datetime=%%I
:DT=%datetime:~0,4%%datetime:~4,2%%datetime:~6,2%
copy ..\*.md .
copy ..\*.txt .
echo Snapshot %datetime:~0,14%
call 2version.bat %VERSION% %datetime:~0,14%

@echo on
wsl 7z a -t7z -mx9 %NAME%-%datetime:~0,14%.7z *.bat *.sh *.php *.md *.txt cache/*.txt build/* classes/*.php js/*.js languages/*

@echo off
copy %NAME%-%datetime:~0,14%.7z %FTP%
del *.md
del *.txt
del %NAME%-%datetime:~0,14%.7z
pause
