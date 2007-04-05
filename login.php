<?php
require("includes/config.inc");
require("includes/ldap_functions.inc");
require("includes/my_functions.inc");
require("includes/crypt.inc");

session_start ();

$ldap = new ELMA(LDAP_HOSTNAME);
$ldap->connect();

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
	if ( $ldap->bind($LDAP_BINDDN,$LDAP_BINDPASS) == 0 ) {
            $_SESSION["login"] = TRUE;
            $_SESSION["logintime"] = time();
	    $_SESSION["username"] = $_POST["username"];
	    $_SESSION["language"] = $_POST["language"];

	    $crypt = new mycrypt();
	    $_SESSION["ldap_binddn"] = $crypt->encrypt($LDAP_BINDDN);
	    $_SESSION["ldap_bindpass"] = $crypt->encrypt($LDAP_BINDPASS);
	
	    header ("Location: index.php");
            exit;
	} else  {
	    echo "error";
            session_destroy();
        }
    }
}
?>
