@echo off
:deepl (by dptran) auto transrate

set VERSION=0.0.20
set PACKAGE=Neo HTML Protector
set EMAIL=plugin@773.moe

@echo on

wsl xgettext --language=PHP --from-code=utf-8 --keyword=__ --keyword=_e --output=languages/neo-html-protector.pot neo-html-protector.php classes/*.php

:pause

:en
set LANG=en
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:en_US
set LANG=en_US
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:en_GB
set LANG=en_GB
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:en_GB
set LANG=en_GB
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:zh
set LANG=zh
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:zh_CN
set LANG=zh_CN
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:zh_TW
set LANG=zh_TW
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo

:ko
set LANG=ko
wsl perl build/autotransrate.pl --from=ja --to=%LANG% --cache=cache/%LANG%.txt --input=languages/neo-html-protector.pot --table=build/language_table.txt --package='%PACKAGE%' --email='%EMAIL%' --version='%VERSION%' > languages/neo-html-protector-%LANG%.po
wsl msgfmt languages/neo-html-protector-%LANG%.po -o languages/neo-html-protector-%LANG%.mo


pause
