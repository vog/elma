<?php
/**
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 * @version $LastChangedRevision$
 * @package ELMA
 *
 * $Id$
 * $LastChangedBy$
 *
 * =====================================================================
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA
 *
 * =====================================================================
 */

session_start ();

require("includes/config.inc.php");
require("includes/ldap_functions.inc.php");
require("includes/my_functions.inc.php");
require("includes/crypt.inc.php");

$ldap = new ELMA(LDAP_HOSTNAME);
$ldap->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION["username"] = $_POST["username"];
    if (preg_match('/\@/',$_SESSION["username"])) {
        list($local_part,$domain) = split("@",$_SESSION["username"]);
        $LDAP_BINDDN = "uid=$local_part,dc=$domain,".LDAP_DOMAINS_ROOT_DN;
        $LDAP_BINDPASS = $_POST["password"];
    } else {
        $LDAP_BINDDN = "uid=".$_SESSION["username"].",".LDAP_USERS_ROOT_DN;
        $LDAP_BINDPASS = $_POST["password"];
    }
  
    if (isset($LDAP_BINDDN) && isset($LDAP_BINDPASS)) {
        $ldap->binddn = $LDAP_BINDDN;
        $ldap->bindpw = $LDAP_BINDPASS;
        
        if ( $ldap->bind() ) {
            $_SESSION["login"] = TRUE;
            $_SESSION["logintime"] = time();
            $_SESSION["username"] = $_POST["username"];
            $_SESSION["language"] = $_POST["language"];

            $systemuser = $ldap->listSystemUsers();
            $adminuser = $ldap->isAdminUser($_SESSION["username"]);
            $domaincount = $ldap->domainCount();

            if ($adminuser == true) {
                $userclass = "systemadmin";
            } else {
                if ($domaincount == 0) {
                    $userclass = "user";
                } else {
                    $userclass = "domainadmin";
                }
            }
            
            $_SESSION["userclass"] = $userclass;

            $crypt = new mycrypt();
            $_SESSION["ldap_binddn"] = $crypt->encrypt($LDAP_BINDDN);
            $_SESSION["ldap_bindpass"] = $crypt->encrypt($LDAP_BINDPASS);
        
            header ("Location: index.php");
            exit;
        } else  {
            session_destroy();
            header ("Location: index.php?loginerror=TRUE");
        }
    } else {
        header ("Location: index.php?loginerror=TRUE");
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
