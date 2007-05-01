#!/bin/sh

TEMPLATE=$1
LOCALE=$2
MODE=$3

tsmarty2c.php ../templates/$TEMPLATE/*.tpl > ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.c
xgettext $MODE -C ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.c -o ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.po --keyword=_ --add-comments --from-code=utf-8
