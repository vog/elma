<?php
require("includes/config.inc");
include("includes/smarty.inc");
require("includes/gettext.inc");
require("includes/ldap_functions.inc");
require("includes/my_functions.inc");

session_start ();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $_SESSION["username"] = $_POST["username"];
    if ( preg_match('/\@/',$_SESSION["username"]) ) {
        list($local_part,$domain) = split("@",$_SESSION["username"]);
        $LDAP_BINDDN = "uid=$local_part,dc=$domain,".LDAP_DOMAINDN;
        $LDAP_BINDPASS = $_POST["password"];
    } else if ( preg_match('/^admin$/',$_SESSION["username"]) ) {
        $LDAP_BINDDN = "cn=admin,".LDAP_USERS_ROOT_DN;
        $LDAP_BINDPASS = $_POST["password"];
    }
  
    if ( isset($LDAP_BINDDN) && isset($LDAP_BINDPASS) ) {
	$ldap_cid = my_ldapConnect(LDAP_HOSTNAME,LDAP_USE_TLS);
	if ( my_ldapBind($ldap_cid,$LDAP_BINDDN,$LDAP_BINDPASS) == 0 ) {
            $_SESSION["login"] = TRUE;
            $_SESSION["logintime"] = time();
            $_SESSION["username"] = $_POST["username"];
	    header ("Location: index.php");
            exit;
	} else  {
	    echo "error";
            session_destroy();
        }
    }
}
?>
