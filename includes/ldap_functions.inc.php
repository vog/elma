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
        $domains = $this->getDomain();
        return $domains;
    }

    function getDomain ($domain_dc = "*") {
        $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "dc=".$domain_dc);
        $domain = ldap_get_entries($this->cid, $result);
        if ( $domain_dc !== "*" ) $domain = $domain[0];
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

    function getUser ( $domain, $user_uid = "*") {
        $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailUser)(uid=$user_uid))");
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

    function getAlias ( $domain, $alias_uid = "*") {
        $result = ldap_list($this->cid,"dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailAlias)(uid=$alias_uid))");
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
    
    function listSystemusers ($mode="system") {
        $users = $this->getSystemuser("*", $mode);
        return $users;
    }

    function getSystemuser ($user_uid="*", $mode="system") {
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

    function addSystemuser ( $user ) {
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
    
    function modifySystemuser ( $user ) {
        ldap_modify($this->cid, "uid=".$user['uid'].",".LDAP_USERS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return $result;
    }

    function deleteSystemuser ( $user ) {
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

    # ADMINGROUP

    function listGroupusers ($domain="users") {
        $users = $this->getGroupuser($domain);
        return $users;
    }

    function getGroupuser ($domain="users") {
        if ($domain != "users") {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "cn=admingroup");
            $user = ldap_get_entries($this->cid, $result);   
        } else {
            $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "cn=admingroup");
            $user = ldap_get_entries($this->cid, $result);
        }

        return $user;
    }

    function addGroupusers ($domain=null, $users) {

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
    
    function delGroupusers ($domain=null, $users) {

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

    # Statistical functions
    function userCount ($domain=null, $active="*") {
        if ($domain != null) {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(&(objectclass=mailUser)(uid=*)))");
            $result = ldap_get_entries($this->cid, $result);
            $tmpcount = $result["count"];
        } else {
            $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "dc=*");

            $tmpresult = ldap_get_entries($this->cid, $result);
            $count = $tmpresult["count"];
            $tmpcount = 0;

            for ($i=0; $i<$count; $i++) {
                $tmpusersresult = ldap_list($this->cid, "dc=".$tmpresult[$i]["dc"][0].",".LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(&(objectclass=mailUser)(uid=*)))"); 
                $tmpusersresult = ldap_get_entries($this->cid, $tmpusersresult);
                $tmpcount += $tmpusersresult["count"];
            }
        }
        return $tmpcount;
    }
    
    function aliasCount ($domain=null, $active="*") {
        if ($domain != null) {
            $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(&(objectclass=mailAlias)(uid=*)))");
            $result = ldap_get_entries($this->cid, $result);
            $tmpcount = $result["count"];
        } else {
            $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "dc=*");

            $tmpresult = ldap_get_entries($this->cid, $result);
            $count = $tmpresult["count"];
            $tmpcount = 0;

            for ($i=0; $i<$count; $i++) {
                $tmpusersresult = ldap_list($this->cid, "dc=".$tmpresult[$i]["dc"][0].",".LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(&(objectclass=mailAlias)(uid=*)))"); 
                $tmpusersresult = ldap_get_entries($this->cid, $tmpusersresult);
                $tmpcount += $tmpusersresult["count"];
            }
        }
        return $tmpcount;
    }

    function domainCount ($active="*") {
            $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "(&(mailStatus=$active)(dc=*))");
            $result = ldap_get_entries($this->cid, $result);
            $tmpcount = $result["count"];

            return $tmpcount;
    }

    function systemuserCount () {
            $result = ldap_list($this->cid, LDAP_USERS_ROOT_DN, "(uid=*)");
            $result = ldap_get_entries($this->cid, $result);
            $tmpcount = $result["count"];

            return $tmpcount;

    }

}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler: 
