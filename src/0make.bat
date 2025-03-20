@echo off
set NAME=neo-html-protector
set FTP=x:\ftp\pub\Wordpress\%NAME%\snapshot
set CLOSURE=wsl npx google-closure-compiler

@echo on
%CLOSURE% --js=js/neo-html-protect.js --js_output_file=js/neo-html-protect.min.js
:pause

:wsl xgettext --language=PHP --from-code=utf-8 --keyword=__ --keyword=_e --output=languages/neo-html-protector.pot neo-html-protector.php classes/*.php

:wsl msginit --input=languages/neo-html-protector.pot --output=languages/neo-html-protector-en.po --locale=en

:wsl msginit --input=languages/neo-html-protector.pot --output=languages/neo-html-protector-en_US.po --locale=en_US
:pause

wsl msgfmt languages/neo-html-protector-ja.po -o languages/neo-html-protector-ja.mo

wsl msgfmt languages/neo-html-protector-en.po -o languages/neo-html-protector-en.mo

wsl msgfmt languages/neo-html-protector-en_US.po -o languages/neo-html-protector-en_US.mo

:pause
@4echo off
: 日付
for /f "tokens=2 delims==" %%I in ('"wmic os get localdatetime /value"') do set datetime=%%I
:DT=%datetime:~0,4%%datetime:~4,2%%datetime:~6,2%
copy ..\*.md .
copy ..\*.txt .
echo Snapshot %datetime:~0,14%

@echo on
wsl 7z a -tzip -mx9 %NAME%-%datetime:~0,14%.zip *.bat *.sh *.php *.md *.txt classes/*.php js/*.js languages/*.pot languages/*.po languages/*.mo

@echo off
copy %NAME%-%datetime:~0,14%.zip %FTP%
del *.md
del *.txt
del %NAME%-%datetime:~0,14%.zip
pause
