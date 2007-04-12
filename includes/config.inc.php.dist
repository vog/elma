<?php
// ######## LDAP Settings ########

/* LDAP Server hostname */
define ("LDAP_HOSTNAME","ldap://127.0.0.1");

/* use TLS for LDAP connection */
define ("LDAP_USE_TLS","0");

/* LDAP Servers Base DN */
define ("LDAP_BASEDN","o=megabit");

/* LDAP Servers Base DN for domains*/
define ("LDAP_DOMAINS_ROOT_DN","ou=domains,".LDAP_BASEDN);

/* LDAP Servers Base DN for system user (e.g. admin)*/
define ("LDAP_USERS_ROOT_DN","ou=users,".LDAP_BASEDN);

// ######## Language Settings #### 

define ("DEFAULT_LANGUAGE","de_DE");
define ("AVAILABLE_LANGUAGES",serialize(array("deutsch" => "de_DE",
                                              "english" => "en_US"
                                             ))
       );

// ######## Template Settings ####

define ("TEMPLATE","simple");

// ######## Application Settings ####

define('APPROOT',getcwd());

define('MYCRYPT_KEY',"my little key");
?>
