<?php
/**
 * @author Daniel Weuthen <daniel@weuthen-net.de> & Sven Ludwig <adan0s@adan0s.net
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


/**
 * function to delete a complete ldap subtree recursivly
 */
function my_ldap_delete($cid,$dn,$recursive=false) {
    if ( $recursive == false ) {
        return(ldap_delete($cid,$dn));
    } else {
        //searching for sub entries
        $search_result = ldap_list($cid,$dn,"ObjectClass=*",array(""));
        $info = ldap_get_entries($cid, $search_result);
        for ($i=0; $i<$info['count']; $i++) {
        //deleting recursively sub entries
        $result = my_ldap_delete($cid,$info[$i]['dn'],$recursive);
        if (!$result) {
            //return result code, if delete fails
            return $result;
        }
    }
    return ldap_delete($cid,$dn);
  }
}



class ELMA {
    var $tls       = false; // Don't use TLS by default
    var $basedn    = "";    // Base DN of LDAP Tree
    var $cid;               // Connection ID
    var $binddn    = "";    // DN for binding to LDAP
    var $bindpw    = "";    // Password for DN
    var $hostname  = "";    // Hostname or IP of LDAP Server

    function ELMA ($hostname, $tls = FALSE)
    {
      $this->tls      = $tls;
      $this->hostname = $hostname;
      $this->connect();

    } // end function LDAP


    function connect() {
        if ($this->cid = @ldap_connect($this->hostname)) {
            @ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
            if (function_exists("ldap_start_tls") && $this->tls) {
                @ldap_start_tls($this->cid);
            }
            $this->result = TRUE;
        } else {
          $this->result = FALSE;
        }
        return $this->result;
    } 

    function bind () {
        if ( @ldap_bind($this->cid, $this->binddn, $this->bindpw) ) {
            $this->result = TRUE;
        } else {
            $this->result = FALSE;
        }
        return $this->result;
    }

    function last_error () {
        $error = @ldap_error($this->cid);
        return $error;
    }

    # DOMAIN

    function listDomains () {
        $domains = $this->getDomain("*");
        return $domains;
    }

    function getDomain ($domain_dc = "*", $active="*") {
        $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(dc=$domain_dc))");
        $domain = ldap_get_entries($this->cid, $result);
        if (isset($domain[0])) {
            if ( $domain_dc !== "*" ) $domain = $domain[0];
        }
        return $domain;
    }
 
    function addDomain ( $domain , $admins ) {
        $domain["objectclass"] = "mailDomain";
        ldap_add($this->cid, "dc=".$domain['dc'].",".LDAP_DOMAINS_ROOT_DN, $domain);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }

        array_push ($admins, LDAP_ADMIN_DN);

        $group["cn"] = "admingroup";
        $group["objectclass"] = "groupOfNames";
        $group["member"] = $admins;

        ldap_add($this->cid, "cn=".$group["cn"].",dc=".$domain['dc'].",".LDAP_DOMAINS_ROOT_DN, $group);
        return $result;
    } 

    function modifyDomain ( $domain ) {
        ldap_modify($this->cid,"dc=".$domain["dc"].",".LDAP_DOMAINS_ROOT_DN, $domain);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    function deleteDomain ( $domain ) {
        my_ldap_delete($this->cid,"dc=$domain,".LDAP_DOMAINS_ROOT_DN,true);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }


    # USER 

    function listUsers( $domain ) {
        $users = $this->getUser( $domain );
        return $users;
    }

    function getUser ( $domain, $user_uid = "*", $active = "*") {
        if ($active == "*") {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailUser)(uid=$user_uid))");
        } else {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(&(objectclass=mailUser)(uid=*)))");
        }
        $user = ldap_get_entries($this->cid, $result);
        if ( $user_uid !== "*" ) $user = $user[0];
        return $user;
    }

    function addUser ( $domain, $user) {
        $user["objectclass"] = "mailUser";
        ldap_add($this->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    function modifyUser ( $domain, $user) {
        ldap_modify($this->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    function deleteUser ( $domain, $user) {
        $searchresult = ldap_search($this->cid, LDAP_BASEDN, "(&(member=*)(cn=admingroup))");
        $searchresult = ldap_get_entries($this->cid, $searchresult);
        
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }

        for ($i=0; $i<$searchresult["count"]; $i++) {
            for ($c=0; $c<$searchresult[$i]["member"]["count"]; $c++) {
                $member = explode(",", $searchresult[$i]["member"][$c]);

                if (isset($member[3])) {
                    if (($member[0] == "uid=".$user) && ($member[2].",".$member[3] == LDAP_DOMAINS_ROOT_DN)) {
                        $del["member"] = array($searchresult[$i]["member"][$c]);
                        ldap_mod_del($this->cid, $searchresult[$i]["dn"], $del); 
                    }
                    
                    if ( ldap_errno($this->cid) !== 0 ) {
                        $result = ldap_error($this->cid);
                        return $result;
                    } else {
                        $result = 0;
                    }
                }
            }
        }

        ldap_delete($this->cid, "uid=".$user.",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    # ALIAS

    function listAliases( $domain ) {
        $aliases = $this->getAlias( $domain );
        return $aliases;
    }

    function getAlias ( $domain, $alias_uid = "*", $active = "*") {
        if ($active == "*") {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailAlias)(uid=$alias_uid))");
        } else {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(&(objectclass=mailAlias)(uid=*)))");
        }
        $alias = ldap_get_entries($this->cid, $result);
        if ( $alias_uid !== "*" ) $alias = $alias[0];
        return $alias;
    }

    function addAlias ( $domain, $alias) {
        $alias["objectclass"] = "mailAlias";
        ldap_add($this->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $alias);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    function modifyAlias ( $domain, $alias) {
        ldap_modify($this->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $alias);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    function deleteAlias ( $domain, $alias) {
        ldap_delete($this->cid, "uid=".$alias.",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN);
        if ( ldap_errno($this->cid) !== 0 )
        {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    # SYSTEMUSER


    /**
     * listSystemUsers - links to the getSystemUser function
     *
     * This function is used to link to the getSystemUser function only
     *
     * @mode    string  used to choose the filter options
     */
    function listSystemUsers ($mode="system") {
        $users = $this->getSystemUser("*", $mode);
        return $users;
    }

    /**
     * getSystemUser - gets information about the systemUsers
     *
     * This function returns information about systemusers
     * 
     * when user_uid is set information about this user will be returned only
     * when user_uid has no value or "*" it will return information about all systemusers
     *
     * when mode is set to "system" only users with readable userPassword-attribute will be listed
     *
     * @user_uid    string  a uid= value
     * @mode        string  used for choosing the filter options
     */
    function getSystemUser ($user_uid="*", $mode="system") {
        if ($mode!="system") {
            $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(&(objectclass=inetOrgPerson)(uid=$user_uid))");
        } else {
            $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(&(userPassword=*)(&(objectclass=inetOrgPerson)(uid=$user_uid)))");
        }
        $user = ldap_get_entries($this->cid, $result);

        if ($user_uid != "*") {
        $user = $user[0];
        }

        return $user;
    }

    /**
     * addSystemUser - add a systemuser
     *
     * This function will add a systemuser using the submitted information
     *
     * @user        array   an array of information about the user
     */
    function addSystemUser ( $user ) {
        $user["objectClass"][0] = "inetOrgPerson"; 
        $user["objectClass"][1] = "simpleSecurityObject";

        ldap_add($this->cid, "uid=".$user['uid'].",".LDAP_USERS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * modSystemUser - modifies a systemUser's info
     *
     * This function will modify the information of a systemuser using
     * the submitted information
     *
     * @user        array   an array of information about the user
     */
    function modSystemUser ( $user ) {
        ldap_modify($this->cid, "uid=".$user['uid'].",".LDAP_USERS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * delSystemUser - removes a systemUser
     *
     * This function will remove a systemUser and his entries in all adminsgroups he's in
     *
     * @user        string  the uid= value of the user's dn
     */
    function delSystemUser ( $user ) {
        $result = 1;

        $searchresult = ldap_search($this->cid, LDAP_BASEDN, "(&(member=*)(cn=admingroup))");
        $searchresult = ldap_get_entries($this->cid, $searchresult);
        
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }

        for ($i=0; $i<$searchresult["count"]; $i++) {
            for ($c=0; $c<$searchresult[$i]["member"]["count"]; $c++) {
                $member = explode(",", $searchresult[$i]["member"][$c]);

                if (($member[0] == "uid=".$user) && ($member[1].",".$member[2] == LDAP_USERS_ROOT_DN)) {
                    $del["member"] = array($searchresult[$i]["member"][$c]);
                    ldap_mod_del($this->cid, $searchresult[$i]["dn"], $del); 
                }
                
                if ( ldap_errno($this->cid) !== 0 ) {
                    $result = ldap_error($this->cid);
                    return $result;
                } else {
                    $result = 0;
                }
            }
        }

        if ($result == 0) {
            ldap_delete($this->cid, "uid=".$user.",".LDAP_USERS_ROOT_DN);
            
            if ( ldap_errno($this->cid) !== 0 ) {
                $result = ldap_error($this->cid);
            } else {
                $result = 0;
            }
        }

        return $result;
    }

    /**
     * getSystemUsersDomain - lists administrated domains
     *
     * This function returns all domains the submitted user administrates
     *
     * @user        string  the uid= value of the user's dn
     */
    function getSystemUsersDomains ( $user ) {

        $userdn = "uid=".$user.",".LDAP_USERS_ROOT_DN;

        $searchresult = ldap_search($this->cid, LDAP_DOMAINS_ROOT_DN, "(member=$userdn)");
        $searchresult = ldap_get_entries($this->cid, $searchresult);

        unset($searchresult["count"]);

        $tmp = array();
        $domains = array();

        foreach($searchresult as $dn) {
            $tmp = ldap_explode_dn($dn["dn"], 0);
            array_push($domains, $tmp[1]);
        }

        return $domains;
    }

    # ADMINGROUP

    /**
     * listAdminUsers - links to the getAdminUser function
     *
     * This function is used to link to the getAdminUser function only
     *
     * @domain      string  dc= value of a domain
     */
    function listAdminUsers ($domain="users") {
        $users = $this->getAdminUser($domain);
        return $users;
    }

    /**
     * getAdminUser - lists users from an admingroup
     *
     * This function lists all users (excluding the main-admin)
     * listed in the global or a domain's admingroup
     *
     * when domain is not set the global admingroup will be used instead
     *
     * @domain      string  dc= value of a domain's dn
     */
    function getAdminUser ($domain="users") {
        if ($domain != "users") {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "cn=admingroup");
            $user = ldap_get_entries($this->cid, $result);   
        } else {
            $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(cn=admingroup)");
            $user = ldap_get_entries($this->cid, $result);
        }

        if (isset($user[0])) {
            $tmp = $user[0]["member"];
            $user[0]["member"] = array();
            $user[0]["member"]["count"] = $tmp["count"];

            for ($i=0; $i<$tmp["count"]; $i++) {
                if (!($tmp[$i] == LDAP_ADMIN_DN)) {
                    array_push($user[0]["member"], $tmp[$i]);
                } else {
                    $user[0]["member"]["count"]--;
                }
            }
        }
        
        return $user;
    }

    /**
     * addAdminUsers - adding users to an admingroup
     *
     * This function adds the submitted users to the global or to the submitted domain's admingroup
     *
     * when domain is not set the global admingroup will be used instead
     *
     * @domain      string  dc= value of a domain's dn
     * @users       array   dn's of one or more users
     */
    function addAdminUsers ($domain=null, $users) {

        $tmpusers["member"] = $users;

        if ($domain != null) {
            ldap_mod_add($this->cid, "cn=admingroup,dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $tmpusers);
        } else {
            ldap_mod_add($this->cid, "cn=admingroup,".LDAP_USERS_ROOT_DN, $tmpusers);
        }
        
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * delAdminUsers - remove users from an admingroup
     *
     * This function will remove the submitted users from the global or the submitted domain's admingroup
     *
     * when domain is not set the global admingroup will be used instead
     *
     * @domain      string  dc= value of a domain's dn
     * @users       array   dn's of one or more users
     */
    function delAdminUsers ($domain=null, $users) {

        $tmpusers["member"] = $users;

        if ($domain != null) {
            ldap_mod_del($this->cid, "cn=admingroup,dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $tmpusers);
        } else {
            ldap_mod_del($this->cid, "cn=admingroup,".LDAP_USERS_ROOT_DN, $tmpusers);
        }
        
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * isAdminUser - checks if user is global admin
     *
     * This function checks if the submitted user is in the global admingroup
     *
     * @user        string  uid= value of a user's dn
     */
    function isAdminUser ($user) {
        $userdn = "uid=".$user.",".LDAP_USERS_ROOT_DN;

        $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(&(member=$userdn)(cn=admingroup))");
        $result = ldap_get_entries($this->cid, $result);

        if ($result["count"] == 0) {
            return false;
        } else {
            return true;
        }
    }

    # Statistical functions

    /**
     * userCount - counting Users
     *
     * This function counts Users in a domain or globally
     *
     * when domain is set to null users will be counted globally
     * when active is set to "TRUE" only active users will be listed
     *
     * @domain      string  dc= value of a domain's dn
     * @active      string  * for global search, "TRUE" for actives only
     */
    function userCount ($domain=null, $active="*") {
        if ($domain != null) {
            $result = $this->getUser($domain, "*", $active);
            $tmpcount = $result["count"];
        } else {
            $result = $this->listDomains();

            $count = $result["count"];
            $tmpcount = 0;

            for ($i=0; $i<$count; $i++) {
                $tmpresult = $this->getUser($result[$i]["dc"][0], "*", $active);
                
                $tmpcount += $tmpresult["count"];
            }
        }

        return $tmpcount;
    }

    /**
     * aliasCount - counting Aliases
     *
     * This function counts Aliases in a domain or globally
     *
     * when domain is set to null users will be counted globally
     * when active is set to "TRUE" only active users will be listed
     *
     * @domain      string  dc= value of a domain's dn
     * @active      string  * for global search, "TRUE" for actives only
     */
    function aliasCount ($domain=null, $active="*") {
        if ($domain != null) {
            $result = $this->getAlias($domain, "*", $active);
            $tmpcount = $result["count"];
        } else {
            $result = $this->listDomains();

            $count = $result["count"];
            $tmpcount = 0;

            for ($i=0; $i<$count; $i++) {
                $tmpresult = $this->getAlias($result[$i]["dc"][0], "*", $active);
                
                $tmpcount += $tmpresult["count"];
            }
        }

        return $tmpcount;
    }

    /**
     * domain Count - counting Domains
     *
     * This function counts domains
     *
     * when active is set to "TRUE" only active domains will be listed
     *
     * @active      string  * for global search, "TRUE" for actives only
     */
    function domainCount ($active="*") {
        $result = $this->getDomain("*", $active);
        $tmpcount = $result["count"];

        return $tmpcount;
    }

    /**
     * systemuserCount - counting systemUsers
     *
     * This function counts systemUsers
     */
    function systemuserCount () {
            $tmp = listAdminUsers();
            $tmpcount = $tmp["count"];

            return $tmpcount;

    }

    /**
     * getEntry - gets a specific Entry
     *
     * This function gets a specific Entry from the ldap tree
     *
     * @dn          string  a ldap dn
     */
    function getEntry($dn) {
        $result = ldap_read($this->cid, $dn, "(objectClass=*)");
        $result = ldap_get_entries($this->cid, $result);

        return $result;
    }

}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler: 
