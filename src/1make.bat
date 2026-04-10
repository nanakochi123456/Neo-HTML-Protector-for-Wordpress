@echo off
:deepl auto transrate

set VERSION=0.4.6
set PACKAGE=Neo HTML Protector
set EMAIL=plugin@773.moe

IF "%1" neq "" goto exec

@echo on

wsl xgettext --language=PHP --from-code=utf-8 --keyword=__ --keyword=_e --output=languages/neo-html-protector.pot neo-html-protector.php classes/*.php

:https://developers.deepl.com/docs/resources/supported-languages#translation-target-languages

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

:20250326 add
call 1make.bat af
call 1make.bat sq
call 1make.bat an
call 1make.bat hy
call 1make.bat as
call 1make.bat az
call 1make.bat ba
call 1make.bat eu
call 1make.bat be
call 1make.bat bn
call 1make.bat bs
call 1make.bat my
call 1make.bat ca
call 1make.bat hr
call 1make.bat eo
call 1make.bat gl
call 1make.bat gn
call 1make.bat gu
call 1make.bat ht
call 1make.bat ha
call 1make.bat he
call 1make.bat hi
call 1make.bat is
call 1make.bat ig
call 1make.bat ga
call 1make.bat jv
call 1make.bat kk
call 1make.bat ku
call 1make.bat ky
call 1make.bat la
call 1make.bat ln
call 1make.bat lb
call 1make.bat mk
call 1make.bat mg
call 1make.bat ms
call 1make.bat ml
call 1make.bat mt
call 1make.bat mi
call 1make.bat mr
call 1make.bat mn
call 1make.bat ne
call 1make.bat oc
call 1make.bat om
call 1make.bat ps
call 1make.bat fa
call 1make.bat pa
call 1make.bat qu
call 1make.bat sa
call 1make.bat sr
call 1make.bat st
call 1make.bat su
call 1make.bat sw
call 1make.bat tl
call 1make.bat tg
call 1make.bat ta
call 1make.bat tt
call 1make.bat te
call 1make.bat th
call 1make.bat tk
call 1make.bat ur
call 1make.bat uz
call 1make.bat vi
call 1make.bat cy
call 1make.bat xh
call 1make.bat yi
call 1make.bat zu

:20260409 add

goto end

:exec
set LANG=%1
@echo on

wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po

wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

@echo off

goto exit

:end
pause
:exit
