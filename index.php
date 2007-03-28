<html>
    <head>
	<link rel="stylesheet" type="text/css" href="install.css" />
    </head>
    <body>

<?php

// I18N support information here
$language = 'en';
putenv("LANG=$language"); 
setlocale(LC_ALL, $language);

// Set the text domain as 'messages'
$domain = 'messages';
bindtextdomain($domain, "locale"); 
textdomain($domain);

/* LDAP Servers Base DN */
define ("LDAP_BASEDN","o=megabit");

/* LDAP Servers Base DN for domains*/
define ("LDAP_DOMAIN_ROOT_DN","ou=domains,".LDAP_BASEDN);

require("includes/ldap_functions.inc");
require("includes/my_functions.inc");




include("includes/inst_checks.inc");

runinstchecks();

?>

    </body>
</html>
