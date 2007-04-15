<?php
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
            return($result);
        }
    }
    return (ldap_delete($cid,$dn));
  }
}


class ELMA {
    var $tls       = false; // Don't use TLS by default
    var $basedn    = "";    // Base DN of LDAP Tree
    var $cid;               // Connection ID
    var $error     = "";    // Any error messages to be returned, value of 0 if no error, otherwise error string
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
        if ($this->cid = ldap_connect($this->hostname)) {
            ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
            if (function_exists("ldap_start_tls") && $this->tls) {
                ldap_start_tls($this->cid);
            }
            $this->result = 0;
        } else {
          $this->result = "Could not connect to LDAP server ".$this->hostname;
        }
    } 

    function bind () {
        if ($bind = ldap_bind($this->cid, $this->binddn, $this->bindpw)) {
            $this->result = 0;
        } else {
            $this->result = "Could not bind ".$this->binddn." to host ".$this->hostname;
        }
    }

    # DOMAIN

    function listDomains () {
        //$result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "dc=*");
        //$domains = ldap_get_entries($this->cid, $result);
        $domains = $this->getDomain();
        return($domains);
    }

    function getDomain ($domain_dc = "*") {
        $result = ldap_list($this->cid, LDAP_DOMAINS_ROOT_DN, "dc=".$domain_dc);
        $domain = ldap_get_entries($this->cid, $result);
        if ( $domain_dc !== "*" ) $domain = $domain[0];
        return($domain);
    }
 
    function addDomain ( $domain ) {
        $domain["objectclass"] = "mailDomain";
        ldap_add($this->cid, "dc=".$domain['dc'].",".LDAP_DOMAINS_ROOT_DN, $domain);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    } 

    function modifyDomain ( $domain ) {
        ldap_modify($this->cid,"dc=".$domain["dc"].",".LDAP_DOMAINS_ROOT_DN, $domain);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }

    function deleteDomain ( $domain ) {
        my_ldap_delete($this->cid,"dc=$domain,".LDAP_DOMAINS_ROOT_DN,true);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }


    # USER 

    function listUsers( $domain ) {
        //$result = ldap_list($this->cid,"dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailUser))");
        //$users = ldap_get_entries($this->cid, $result);
        $users = $this->getUser( $domain );
        return($users);
    }

    function getUser ( $domain, $user_uid = "*") {
        $result = ldap_list($this->cid, "dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "uid=".$user_uid);
        $user = ldap_get_entries($this->cid, $result);
        if ( $user_uid !== "*" ) $user = $user[0];
        return($user);
    } 

    function addUser ( $domain, $user) {
        $user["objectclass"] = "mailUser"; 
        ldap_add($this->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }

    function modifyUser ( $domain, $user) {
        ldap_modify($this->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $user);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }

    function deleteUser ( $domain, $user) {
        ldap_delete($this->cid, "uid=".$user.",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }

    # ALIAS

    function listAliases( $domain ) {
        $result = ldap_list($this->cid,"dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, "(&(objectclass=mailAlias))");
        $aliases = ldap_get_entries($this->cid, $result);
        return($aliases);
    }

    function addAlias ( $domain, $alias) {
        $alias["objectclass"] = "mailAlias";
        ldap_add($this->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $alias);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }

    function modifyAlias ( $domain, $alias) {
        ldap_modify($this->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN, $alias);
        if ( ldap_errno($this->cid) !== 0 ) {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }

    function deleteAlias ( $domain, $alias) {
        ldap_delete($this->cid, "uid=".$alias.",dc=".$domain.",".LDAP_DOMAINS_ROOT_DN);
        if ( ldap_errno($this->cid) !== 0 )
        {
            $result = ldap_error($this->cid);
        } else {
            $result = 0;
        }
        return ($result);
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>