#!/bin/sh

TEMPLATE=$1
LOCALE=$2

msgfmt ../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.po --output-file=../templates/$TEMPLATE/locale/$LOCALE/LC_MESSAGES/messages.mo
