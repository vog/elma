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
     * This function lists any domain, to which access is granted through LDAP ACLs.
     *
     * @active          string  "*" for listing all, "TRUE" for listing active only, "FALSE" for listing inactive only
     * @attribute       array   array of ldap attributes to return, empty array returns everything
     */
    function listDomains ($active="*", $attributes=array() ) {
        $domains = $this->getDomain("*", $active="*", $attributes=array());
        return $domains;
    }

    /**
     * getDomain - gets information about domain(s)
     *
     * This function gets information about domain(s) from LDAP
     *
     * @domain_dc       string  dc= value of a domain's DN
     * @active          string  "*" for listing all, "TRUE" for listing active only, "FALSE" for listing inactive only
     * @attribute       array   ldap attributes to return, empty array returns everything
     */
    function getDomain ( $domain_dc="*", $active="*", $attributes=array() ) {
        $result = @ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "(&(objectClass=mailDomain)(dc=$domain_dc)(mailStatus=$active))", $attributes);
        if ($result === FALSE) {
            return array();
        }
        ldap_sort($this->cid,$result,"dc");
        $domain = ldap_get_entries($this->cid, $result);

        if (isset($domain[0])) {
            if ( $domain_dc !== "*" ) {
                $domain = $domain[0];
                if ( count($attributes) == 1 ) {
                    $domain = $domain[$attributes[0]];
                }
            }
        }
        return $domain;
    }

    /**
     * addDomain - adds a domain
     *
     * This functions adds a domain and an admingroup 
     * the main-admin is included in this admingroup by default
     *
     * @domain          array   information about the domain
     * @admins          array   admin dn's
     */
    function addDomain ( $domain , $admins ) {
        $domain["objectclass"] = "mailDomain";

        ldap_add($this->cid, "dc=".$domain['dc'].",".LDAP_DOMAINS_ROOT_DN, $domain);

        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }

        /* create the admingroup object */
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

        /* set the mailstorage server for all domain users changed */
        if ( isset($domain["mailstorageserver"]) ) {
            $domain_users = $this->listUsers($domain["dc"]);
            for ($i=0; $i<$domain_users["count"]; $i++) {
                $domain_user["uid"] = $domain_users[$i]["uid"][0];               
                $domain_user["mailstorageserver"] = $domain["mailstorageserver"];
                $this->modifyUser($domain["dc"],$domain_user);
            }
        }

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
     * @domain_dc       string  dc= value of a domain's DN
     * @active          string  "*" for listing all, "TRUE" for listing active only, "FALSE" for listing inactive only
     * @attribute       array   ldap attributes to return, empty array returns everything
     */
    function listUsers( $domain_dc, $active="*", $attribute = array() ) {
        $users = $this->getUser( $domain_dc, "*", $active="*", $attribute );
        return $users;
    }

    /**
     * getUser - getting information about a user
     *
     * This function gets information about a specific user
     *
     * @domain_dc       string  dc= value of a domain's DN where the user is in
     * @user_uid        string  uid= value of the user's DN
     * @active          string  "*" for listing all, "TRUE" for listing active only, "FALSE" for listing inactive only
     * @attribute       array   ldap attributes to return, empty array returns everything
     */
    function getUser ( $domain_dc, $user_uid="*", $active="*", $attribute = array() ) {
        $result = ldap_list($this->cid, "dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailUser)(mailStatus=$active)(uid=$user_uid))", $attribute);
        ldap_sort($this->cid,$result,"uid");
        $user = ldap_get_entries($this->cid, $result);
        if ( $user_uid !== "*" ) $user = $user[0];
        return $user;
    }

    /**
     * addUser - adding a user
     *
     * This function adds a user to the ldap-tree
     *
     * @domain_dc       string  dc= value of the domain the user should belong to
     * @user            array   information about the user
     */
    function addUser ( $domain_dc, $user ) {
        $user["mailStorageserver"] = $this->getDomain($domain_dc,"*",array("mailstorageserver"));
        $user["mailStorageserver"] = $user["mailStorageserver"][0]; 
        $user["homeDirectory"] = DEFAULT_HOMEDIR_ROOT."/$domain_dc/".$user['uid'];
        $user["objectclass"] = "mailUser";

        ldap_add($this->cid, "uid=".$user['uid'].",dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, $user);

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
     * @domain_dc       string  dc= value of the domain the user is in
     * @user            array   information about the user
     */
    function modifyUser ( $domain_dc, $user ) {
        ldap_modify($this->cid, "uid=".$user['uid'].",dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, $user);

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
     * This function removes a user from the LDAP
     * (including his presence in any admingroup)
     *
     * @domain_dc       string  dc= value of the domain the user is in
     * @user_uid        string  uid= value of the users DN
     */
    function deleteUser ( $domain_dc, $user_uid) {
        $searchresult = ldap_search($this->cid, LDAP_BASEDN, "(&(member=*)(cn=admingroup))");
        $searchresult = ldap_get_entries($this->cid, $searchresult);
        
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }

        /* find out where user is admin. guess this can be done easier. */
        for ($i=0; $i<$searchresult["count"]; $i++) {
            for ($c=0; $c<$searchresult[$i]["member"]["count"]; $c++) {
                $member = explode(",", $searchresult[$i]["member"][$c]);

                if (isset($member[3])) {
                    if (($member[0] == "uid=".$user_uid) && ($member[2].",".$member[3] == LDAP_DOMAINS_ROOT_DN)) {
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

        ldap_delete($this->cid, "uid=".$user_uid.",dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN);

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
     * @domain          string  dc= value of a domain's DN
     * @active          string  "*" for listing all, "TRUE" for listing active only, "FALSE" for listing inactive only
     * @attribute       array   ldap attributes to return, empty array returns everything
     */
    function listAliases( $domain_dc, $active="*", $attributes=array() ) {
        $aliases = $this->getAlias( $domain_dc, "*", $active="*", $attributes=array());
        return $aliases;
    }

    /**
     * getAlias - gets information about an alias
     *
     * This function gets information about an alias
     *
     * @domain_dc       string  dc= value of the domain the alias is in
     * @alias_uid       string  uid= value if the alias, "*" returns all aliases for the given domain
     * @active          string  "*" for listing all, "TRUE" for listing active only, "FALSE" for listing inactive only
     * @attributes      array   ldap attributes to return, empty array returns everything
     */ 
    function getAlias ( $domain_dc, $alias_uid="*", $active="*", $attributes=array() ) {
        $result = ldap_list($this->cid, "dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailAlias)(uid=$alias_uid)(mailStatus=$active))", $attributes);
        ldap_sort($this->cid,$result,"uid");
        $alias = ldap_get_entries($this->cid, $result);
        if ( $alias_uid !== "*" ) $alias = $alias[0];
        return $alias;
    }

    /**
     * addAlias - adds an alias
     *
     * This function add an alias to the specified domain
     *
     * @domain_dc       string  dc= value of a domain's DN
     * @alias           array   information about an alias
     */
    function addAlias ( $domain_dc, $alias) {
        $alias["objectclass"] = "mailAlias";
        ldap_add($this->cid, "uid=".$alias['uid'].",dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, $alias);

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
     * @domain_dc       string  dc= value of a domain's DN
     * @alias           array   information about the alias
     */
    function modifyAlias ( $domain_dc, $alias ) {
        ldap_modify($this->cid, "uid=".$alias['uid'].",dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, $alias);

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
     * @domain_dc       string  dc= value of a domain's DN
     * @alias_uid       string  uid= value of the alias's DN
     */
    function deleteAlias ( $domain_dc, $alias_uid ) {
        ldap_delete($this->cid, "uid=".$alias_uid.",dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN);

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
     * @attributes      array   ldap attributes to return, empty array returns everything
     */
    function listSystemUsers ( $attributes = array() ) {
        $systemusers = $this->getSystemUser("*", $attributes);
        return $systemusers;
    }

    /**
     * getSystemUser - gets information about the systemUsers
     *
     * This function returns information about systemusers
     * 
     * @user_uid        string  uid= value of systemuser, "*" returns all systemusers
     * @attributes      array   ldap attributes to return, empty array returns everything
     */
    function getSystemUser ( $systemuser_uid = "*", $attributes = array() ) {
        $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(&(objectclass=inetOrgPerson)(uid=$systemuser_uid))", $attributes);
        ldap_sort($this->cid,$result,"uid");
        $systemuser = ldap_get_entries($this->cid, $result);

        if ($systemuser_uid != "*") {
            $systemuser = $systemuser[0];
        }

        return $systemuser;
    }

    /**
     * addSystemUser - add a systemuser
     *
     * This function will add a systemuser using the submitted information
     *
     * @systemuser      array   information about the systemuser
     */
    function addSystemUser ( $systemuser ) {
        $systemuser["objectClass"][0] = "inetOrgPerson"; 
        $systemuser["objectClass"][1] = "simpleSecurityObject";

        ldap_add($this->cid, "uid=".$systemuser['uid'].",".LDAP_USERS_ROOT_DN, $systemuser);

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
     * @systemuser      array   information about the systemuser
     */
    function modifySystemUser ( $systemuser ) {
        ldap_modify($this->cid, "uid=".$systemuser['uid'].",".LDAP_USERS_ROOT_DN, $systemuser);

        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * deleteSystemUser - removes a systemUser
     *
     * This function will remove a systemUser and his entries in all adminsgroups he's in
     *
     * @systemuser      string  uid= value of the systemuser's DN
     */
    function deleteSystemUser ( $systemuser ) {
        /* define 0 as default value for $result for further error checks */
        $result = 0;

        /* delete admin from admingroups where neccessary */
        $adminofdomains = $this->getSystemUsersDomains($systemuser);
        foreach ($adminofdomains as $adminofdomain) {
            if ($this->deleteAdminUsers($adminofdomain, "uid=$systemuser,".LDAP_USERS_ROOT_DN) != 0) {
                $result = 1;
            }
        }
        
        /* if the above was successfull delete the user object */
        if ($result == 0) {
            ldap_delete($this->cid, "uid=".$systemuser.",".LDAP_USERS_ROOT_DN);
            
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
     * @systemuser_uid        string  uid= value of systemuser's DN
     */
    function getSystemUsersDomains ( $systemuser_uid ) {

        $systemuserdn = "uid=".$systemuser_uid.",".LDAP_USERS_ROOT_DN;
        $search_result = ldap_search($this->cid, LDAP_DOMAINS_ROOT_DN, "(member=$systemuserdn)");
        $domains_dn = ldap_get_entries($this->cid, $search_result);
        unset($domains_dn["count"]);

        /* extract the domain name from each dn found */
        $domains = array();
        foreach($domains_dn as $domain_dn) {
            $domain = ldap_explode_dn($domain_dn["dn"], 1);
            array_push($domains, $domain[1]);
        }

        return $domains;
    }

    # ADMINGROUP

    /**
     * listAdminUsers - lists users from the give admingroup
     *
     * This function lists all users from the global or a domain's admingroup.
     *
     * @domain_dc       string  dc= value of a domain
     * @dn_only         boolean should only the dn be returned
     */
    function listAdminUsers ($domain_dc=null, $dn_only = "FALSE") {
        if ( $domain_dc != null ) {
            $result = ldap_read($this->cid, "cn=admingroup,dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN,"member=*");
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
     * @domain_dc       string  dc= value of a domain's dn
     * @newadmins       array   dn's of one or more users
     */
    function addAdminUsers ($domain_dc=null, $newadmins=array()) {
        $admingroup["member"] = $newadmins;

        if ($domain_dc != null) {
            ldap_mod_add($this->cid, "cn=admingroup,dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, $admingroup);
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
     * deleteAdminUsers - remove users from an admingroup
     *
     * This function will remove the submitted users from the global or the submitted domain's admingroup
     *
     * when domain is not set the global admingroup will be used instead
     *
     * @domain_dc       string  dc= value of a domain's dn
     * @users           array   dn's of one or more users
     */
    function deleteAdminUsers ($domain_dc=null, $users) {
        if ( count($users) > 0 ) {
            $admins["member"] = $users;
            if ($domain_dc != null) {
                ldap_mod_del($this->cid, "cn=admingroup,dc=".$domain_dc.",".LDAP_DOMAINS_ROOT_DN, $admins);
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
     * @domain_dc       string  dc= value of a domain's dn
     * @active          string  * for global search, "TRUE" for actives only
     */
    function userCount ($domain_dc=null, $active="*") {
        $nrofusers = 0;
        if ($domain_dc != null) {
            $users = $this->getUser($domain_dc, "*", $active);
            $nrofusers = $users["count"];
        } else {
            $domains = $this->listDomains();
            for ($i=0; $i<$domains["count"]; $i++) {
                $users = $this->getUser($domains[$i]["dc"][0], "*", $active);
                $nrofusers += $users["count"];
            }
        }
        return $nrofusers;
    }

    /**
     * aliasCount - counting Aliases
     *
     * This function counts Aliases in a domain or globally
     *
     * when domain is set to null users will be counted globally
     * when active is set to "TRUE" only active users will be listed
     *
     * @domain_dc       string  dc= value of a domain's dn
     * @active          string  * for global search, "TRUE" for actives only
     */
    function aliasCount ($domain_dc=null, $active="*") {
        $nrofaliases = 0;
        if ($domain_dc != null) {
            $aliases= $this->getAlias($domain_dc, "*", $active);
            $nrofaliases = $aliases["count"];
        } else {
            $domains = $this->listDomains();
            for ($i=0; $i<$domains["count"]; $i++) {
                $aliases = $this->getAlias($domains[$i]["dc"][0], "*", $active);
                $nrofaliases += $aliases["count"];
            }
        }

        return $nrofaliases;
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
        $domains = $this->getDomain("*", $active);
        $nrofdomains = $domains["count"];

        return $nrofdomains;
    }

    /**
     * systemuserCount - counting systemUsers
     *
     * This function counts systemUsers
     */
    function systemuserCount () {
        $systemusers = listAdminUsers();
        $nrofsystemusers = $systemusers["count"];

        return $nrofsystemusers;
    }

    /**
     * isAdminUser - checks if user is global admin
     *
     * This function checks if the submitted user is in the global admingroup
     *
     * @user_uid         string  uid= value of a user's dn
     */
    function isAdminUser ($user_uid) {
        $user_dn = "uid=".$user_uid.",".LDAP_USERS_ROOT_DN;
        $result = @ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(&(member=$user_dn)(cn=admingroup))");

        if ($result === FALSE) {
            return FALSE;
        } else {
            $entries = ldap_get_entries($this->cid, $result);
            return $entries["count"] > 0;
        }
    }

    /**
     * getEntry - gets a specific Entry
     *
     * This function gets a specific Entry from the ldap tree
     *
     * @dn          string  a ldap dn
     */
    function getEntry($dn, $filter="(objectClass=*)", $attributes = array()) {
        $result = @ldap_read($this->cid, $dn, $filter, $attributes);
        if ($result === FALSE) {
            return array();
        } else {
            return ldap_get_entries($this->cid, $result);
        }
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler: 
