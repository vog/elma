#!/bin/sh

TEMPLATE=$1
LOCALE=$2

tsmarty2c.php ../templates/$TEMPLATE/*.tpl > ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.c
xgettext -C ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.c -o ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.po --keyword=_ --add-comments --from-code=utf-8
