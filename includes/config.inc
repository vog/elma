<?php
// ######## LDAP Settings ########


/* LDAP Servers Base DN */
define ("LDAP_BASEDN","o=megabit");

/* LDAP Servers Base DN for domains*/
define ("LDAP_DOMAIN_ROOT_DN","ou=domains,".LDAP_BASEDN);


// ######## Language Settings #### 

define ("DEFAULT_LANGUAGE","de");


// ######## Template Settings ####

define ("TEMPLATE","simple");

?>
