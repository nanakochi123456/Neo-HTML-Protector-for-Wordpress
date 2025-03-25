@echo off
:deepl (by dptran) auto transrate

set VERSION=0.0.35
set PACKAGE=Neo HTML Protector
set EMAIL=plugin@773.moe

IF "%1" neq "" goto exec

@echo on

wsl xgettext --language=PHP --from-code=utf-8 --keyword=__ --keyword=_e --output=languages/neo-html-protector.pot neo-html-protector.php classes/*.php

call 1make.bat en
call 1make.bat en_US
call 1make.bat en_GB
call 1make.bat zh
call 1make.bat zh_CN
call 1make.bat zh_TW
call 1make.bat ko
call 1make.bat fr
call 1make.bat it
call 1make.bat ru
call 1make.bat uk
call 1make.bat ar
call 1make.bat bg
call 1make.bat cs
call 1make.bat da
call 1make.bat de
call 1make.bat el
call 1make.bat es
call 1make.bat et
call 1make.bat fi
call 1make.bat hu
call 1make.bat id
call 1make.bat lt
call 1make.bat lv
call 1make.bat no
call 1make.bat nl
call 1make.bat pl
call 1make.bat pt
call 1make.bat pt_BR
call 1make.bat pt_PT
call 1make.bat ro
call 1make.bat sk
call 1make.bat sl
call 1make.bat sv
call 1make.bat tr



goto end

:exec
set LANG=%1

wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po

wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

goto exit

:end
pause
:exit
