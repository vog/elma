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

    /**
     * listDomains - listing domains
     *
     * This function lists any domain
     */
    function listDomains ($active="*", $attribute=array() ) {
        $domains = $this->getDomain("*", $active="*", $attribute=array());
        return $domains;
    }

    /**
     * getDomain - gets information about domain(s)
     *
     * This function get information about domain(s) inside the ldap-tree
     *
     * when active is "TRUE" only active domains will be listed
     *
     * @domain_dc       string  dc= value of a domain's DN
     * @active          string  "*" for listing any, "TRUE" for listing active domains only
     */
    function getDomain ( $domain_dc="*", $active="*", $attribute=array() ) {
        $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "(&(objectClass=mailDomain)(dc=$domain_dc)(mailStatus=$active))", $attribute);
        $domain = ldap_get_entries($this->cid, $result);
        if (isset($domain[0])) {
            if ( $domain_dc !== "*" ) $domain = $domain[0];
        }
        return $domain;
    }

    /**
     * addDomain - adds a domain
     *
     * This functions adds a domain and an admingroup
     * the main-admin is included in this admingroup by default
     *
     * @domain      array   information about the domain
     * @admins      array   admin dns
     */
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

    /**
     * modifyDomain - modifying a domain's information
     *
     * This function modifies a domain's information
     *
     * @domain      array   information about the domain
     */
    function modifyDomain ( $domain ) {
        ldap_modify($this->cid,"dc=".$domain["dc"].",".LDAP_DOMAINS_ROOT_DN, $domain);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * deleteDomain - deleting a Domain
     *
     * This function deletes a domain
     *
     * @domain      string  dc= value of a domain's DN
     */
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

    /**
     * listUsers - listing any user in a domain
     *
     * This function lists any user in the specified domain
     *
     * @domain      string  dc= value of a domain's DN
     */
    function listUsers( $domain, $active="*", $attribute = array() ) {
        $users = $this->getUser( $domain, "*", $active="*", $attribute );
        return $users;
    }

    /**
     * getUser - getting information about a user
     *
     * This function gets information about a specific user
     *
     * @domain      string  dc= value of a domain's DN where the user is in
     * @user_uid    string  uid= value of the user's DN
     * @active      string  "*" shows any user, "TRUE" shows active users only
     */
    function getUser ( $domain, $user_uid = "*", $active="*", $attribute = array() ) {
        $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailUser)(uid=$user_uid))", $attribute);
        $user = ldap_get_entries($this->cid, $result);
        if ( $user_uid !== "*" ) $user = $user[0];
        return $user;
    }

    /**
     * addUser - adding a user
     *
     * This function adds a user to the ldap-tree
     *
     * @domain      string  dc= value of the domain the user should belong to
     * @user        array   information about the user
     */
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

    /**
     * modifyUser - modifying a user
     *
     * This function modifies the information of a user
     *
     * @domain      string  dc= value of the domain the user is in
     * @user        array   information about the user
     */
    function modifyUser ( $domain, $user) {
        ldap_modify($this->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * deleteUser - deleting a user
     *
     * This function removes a user from the ldap-tree
     * (including his presence in any admingroup)
     *
     * @domain      string  dc= value of the domain the user is in
     * @user        string  uid= value of the users DN
     */
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

    /**
     * listAliases - listing all aliases in a domain
     *
     * This function lists all aliases in a specific domain
     *
     * @domain      string  dc= value of a domain's DN
     */
    function listAliases( $domain, $active="*", $attribute=array() ) {
        $aliases = $this->getAlias( $domain, "*", $active="*", $attribute=array());
        return $aliases;
    }

    /**
     * getAlias - gets information about an alias
     *
     * This function gets information about an alias
     *
     * @domain      string  dc= value of the domain the alias is in
     * @alias_uid   string  uid= value of the alias
     * @active      string  "*" lists any alias, "TRUE" lists active aliases only
     */ 
    function getAlias ( $domain, $alias_uid = "*", $active="*", $attribute=array() ) {
        $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailAlias)(uid=$alias_uid)(mailStatus=$active))", $attribute);
        $alias = ldap_get_entries($this->cid, $result);
        if ( $alias_uid !== "*" ) $alias = $alias[0];
        return $alias;
    }

    /**
     * addAlias - adds an alias
     *
     * This function add an alias to the specified domain
     *
     * @domain      string  dc= value of a domain's DN
     * @alias       array   information about an alias
     */
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

    /**
     * modifyAlias - modifying an alias
     *
     * This function modifies an alias in a specific domain
     *
     * @domain      string  dc= value of a domain's DN
     * @alias       array   information about the alias
     */
    function modifyAlias ( $domain, $alias ) {
        ldap_modify($this->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $alias);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * deleteAlias - deletes an alias
     *
     * This function deletes an alias inside a specific domain
     *
     * @domain      string  dc= value of a domain's DN
     * @alias       string  uid= value of the alias
     */
    function deleteAlias ( $domain, $alias ) {
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
     */
    function listSystemUsers ($attributes = array()) {
        $users = $this->getSystemUser("*", $attributes);
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
     * @user_uid    string  a uid= value
     *
     */
    function getSystemUser ( $user_uid = "*", $attributes = array() ) {
        $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(&(objectclass=inetOrgPerson)(uid=$user_uid))", $attributes);
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
     * modifySystemUser - modifies a systemUser's info
     *
     * This function will modify the information of a systemuser using
     * the submitted information
     *
     * @user        array   an array of information about the user
     */
    function modifySystemUser ( $user ) {
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
    function deleteSystemUser ( $user ) {
        /* delete admin from admingroups where neccessary */
        $adminofdomains = $this->getSystemUsersDomains($user);
        foreach ($adminofdomains as $adminofdomain) {
            $this->delAdminUsers($adminofdomain, "uid=$user,".LDAP_USERS_ROOT_DN);
        }

        /* if the above was successfull delete the user object */
        if ($result == 0) {
            ldap_delete($this->cid, "uid=".$user.",".LDAP_USERS_ROOT_DN);
            
            if ( ldap_errno($this->cid) !== 0 ) {
                $result = ldap_error($this->cid);
            } else {
                $result = 0;
            }
        } else {
            $result = 1;
        }

        return $result;
    }

    /**
     * getSystemUsersDomain - lists administrated domains
     *
     * This function returns all domain names for which the given
     * system user has administraive rights.
     *
     * @user        string  the uid= value of the user's dn
     */
    function getSystemUsersDomains ( $user ) {

        $userdn = "uid=".$user.",".LDAP_USERS_ROOT_DN;
        $search_result = ldap_search($this->cid, LDAP_DOMAINS_ROOT_DN, "(member=$userdn)");
        $domains_dn = ldap_get_entries($this->cid, $search_result);
        unset($domains_dn["count"]);

        // extract the domain name from each dn found
        $domains = array();
        foreach($domains_dn as $domain_dn) {
            $domain = ldap_explode_dn($domain_dn["dn"], 1);
            array_push($domains, $domain[1]);
        }

        return $domains;
    }

    # ADMINGROUP

    /**
     * listAdminUsers - lists users from the given domain's admin group
     *
     * This function lists all users listed in a domain's admingroup.
     *
     * @domain      string  dc= value of a domain
     * @dn_only     boolean should only the dn be returned
     */
    function listAdminUsers ($domain=null, $dn_only = "FALSE") {
        if ( $domain != null ) {
            $result = ldap_read($this->cid, "cn=admingroup,dc=".$domain.",".LDAP_DOMAINS_ROOT_DN,"member=*");
        } else {
            $result = ldap_read($this->cid, LDAP_ADMIN_GROUP.",".LDAP_USERS_ROOT_DN,"member=*");
        }
        $admingroup = ldap_get_entries($this->cid, $result);

        $adminusers_dn = $admingroup[0]["member"];
        unset($adminusers_dn["count"]);

        /* remove the LDAP_ADMIN_DN from the list, because
         * it should never be shown in the frontend.
         */
        $admin_key = array_search(LDAP_ADMIN_DN, $adminusers_dn);
        if ( $admin_key !== "" ) {
            unset ($adminusers_dn[$admin_key]);
        }

        if ( $dn_only != "TRUE" ) {
            $adminusers = array();
            foreach ($adminusers_dn as $adminuser) {
                $adminusers = array_merge($adminusers,$this->getEntry($adminuser));       
            }
        } else {
            $adminusers = $adminusers_dn;
        }
        return $adminusers;
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
    function addAdminUsers ($domain=null, $newadmins=array()) {
        $admingroup["member"] = $newadmins;

        if ($domain != null) {
            ldap_mod_add($this->cid, "cn=admingroup,dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $admingroup);
        } else {
            ldap_mod_add($this->cid, LDAP_ADMIN_GROUP.",".LDAP_USERS_ROOT_DN, $admingroup);
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
    function deleteAdminUsers ($domain=null, $users) {

        if ( count($users) > 0 ) {
            $admins["member"] = $users;
            if ($domain != null) {
                ldap_mod_del($this->cid, "cn=admingroup,dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $admins);
            } else {
                ldap_mod_del($this->cid, LDAP_ADMIN_GROUP.",".LDAP_USERS_ROOT_DN, $admins);
            }
        }
        
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
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
    function getEntry($dn, $filter="(objectClass=*)", $attributes = array()) {
        $result = ldap_read($this->cid, $dn, $filter, $attributes);
        $result = ldap_get_entries($this->cid, $result);

        return $result;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler: 
