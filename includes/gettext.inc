<?php

putenv("LANG=".DEFAULT_LANGUAGE);
setlocale(LC_ALL, DEFAULT_LANGUAGE);

// Set the text domain as 'messages'
$domain = 'messages';
bindtextdomain($domain, "/home/dweuthen/public_html/elma/locale");
textdomain($domain);

?>
