<?php

require("includes/config.inc.php");
require("includes/acl.inc.php");
require("includes/smarty.inc.php");
require("includes/gettext.inc.php");
require("includes/ldap_functions.inc.php");
require("includes/my_functions.inc.php");
require("includes/crypt.inc.php");
require("includes/sieve.inc.php");

$sieveValues = array( RECIPIENT => "dw@megabit.net",
    MESSAGE => "Dies ist ein test");

$sieveFilterScript = createSieveFilter( $sieveFilter, $sieveValues );

my_print_r($sieveFilterScript);


// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
