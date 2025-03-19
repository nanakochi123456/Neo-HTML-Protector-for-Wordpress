@echo off
set NAME=neo-html-protector
set FTP=x:\ftp\pub\Wordpress\%NAME%\snapshot
set CLOSURE=wsl npx google-closure-compiler
set XGETTEXT=wsl xgettext --from-code=utf-8 --default-domain=ja --output-dir=languages
set MSGFMT=wsl msgfmt
@echo on
%CLOSURE% --js=js/neo-html-protect.js --js_output_file=js/neo-html-protect.min.js
:pause
%XGETTEXT% neo-html-protecter.php classes/*.php
%MSGFMT% languages/ja.po -o languages/ja.mo
%MSGFMT% languages/en.po -o languages/en.mo
:pause
@echo off
: 日付
for /f "tokens=2 delims==" %%I in ('"wmic os get localdatetime /value"') do set datetime=%%I
:DT=%datetime:~0,4%%datetime:~4,2%%datetime:~6,2%
copy ..\*.md .
copy ..\*.txt .
echo Snapshot %datetime:~0,14%

@echo on
wsl 7z a -tzip -mx9 %NAME%-%datetime:~0,14%.zip *.bat *.sh *.php *.md *.txt classes/*.php js/*.js languages/*.po languages/*.mo

@echo off
copy %NAME%-%datetime:~0,14%.zip %FTP%
del *.md
del *.txt
del %NAME%-%datetime:~0,14%.zip
pause
