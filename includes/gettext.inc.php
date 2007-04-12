<?php

if ( isset($_SESSION["language"]) ) {
    $set_language = $_SESSION["language"];
} else {
    $set_language = DEFAULT_LANGUAGE;	
}

putenv("LANG=".$set_language);
setlocale(LC_ALL, $set_language);

// Set the text domain as 'messages'
$domain = 'messages';
bindtextdomain($domain, "/home/dweuthen/public_html/elma/templates/".TEMPLATE."/locale");
textdomain($domain);

?>
