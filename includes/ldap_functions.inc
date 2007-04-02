<?php

function my_ldapConnect($hostname, $tls = 0) {
    if ($cid = ldap_connect($hostname)) {
        @ldap_set_option($cid, LDAP_OPT_PROTOCOL_VERSION, 3);
        if (function_exists("ldap_start_tls") && $tls) {
            @ldap_start_tls($cid);
        }
        $result = $cid;
        
    } else {
      $result = "Could not connect to LDAP server ".$hostname;
    }
    return $result;
} 

function my_ldapBind ( $cid, $binddn, $bindpw) {
    if ($bind = ldap_bind($cid, $binddn, $bindpw)) {
        $result = 0;
    } else {
        $result = "Could not bind ".$binddn." to host ".$hostname;
    }
    return $result;
}

function my_ldap_delete($ldap,$dn,$recursive=false) {
    if ( $recursive == false ) {
        return(ldap_delete($ldap,$dn));
    } else {
        //searching for sub entries
        $search_result = ldap_list($ldap,$dn,"ObjectClass=*",array(""));
        $info = ldap_get_entries($ldap, $search_result);
        for ($i=0; $i<$info['count']; $i++) {
        //deleting recursively sub entries
        $result = my_ldap_delete($ldap,$info[$i]['dn'],$recursive);
        if (!$result) {
            //return result code, if delete fails
            return($result);
        }
    }
    return (@ldap_delete($ldap,$dn));
  }
}

# DOMAIN

function listDomains ( $ldap ) {
    $result = @ldap_list($ldap, LDAP_DOMAIN_ROOT_DN, "dc=*");
    $domains = @ldap_get_entries($ldap, $result);
    return($domains);
}
 
function addDomain ( $ldap, $domain ) {
    $domain["objectclass"] = "mailDomain";
    @ldap_add($ldap, "dc=".$domain['dc'].",".LDAP_DOMAIN_ROOT_DN, $domain);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
} 

function modifyDomain ( $ldap, $domain ) {
    @ldap_modify($ldap,"dc=".$domain["dc"].",".LDAP_DOMAIN_ROOT_DN, $domain);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

function deleteDomain ( $ldap, $domain ) {
    @my_ldap_delete($ldap,"dc=$domain,".LDAP_DOMAIN_ROOT_DN,$recursive=true);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}


# USER 

function listUsers( $ldap, $domain ) {
     $result = @ldap_list($ldap,"dc=".$domain.",".LDAP_DOMAIN_ROOT_DN, "(&(objectclass=mailUser))");
     $users = @ldap_get_entries($ldap, $result);
     return($users);
}

function addUser ( $ldap, $domain, $user) {
    $user["objectclass"] = "mailUser"; 
    @ldap_add($ldap, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAIN_ROOT_DN, $user);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

function modifyUser ($ldap, $domain, $user) {
    @ldap_modify($ldap, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAIN_ROOT_DN, $user);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

function deleteUser ($ldap, $domain, $user) {
    @ldap_delete($ldap, "uid=".$user.",dc=".$domain.",".LDAP_DOMAIN_ROOT_DN);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

# ALIAS

function listAliases( $ldap, $domain ) {
    $result = @ldap_list($ldap,"dc=".$domain.",".LDAP_DOMAIN_ROOT_DN, "(&(objectclass=mailAlias))");
    $aliases = @ldap_get_entries($ldap, $result);
    return($aliases);
}

function addAlias ( $ldap, $domain, $alias) {
    $alias["objectclass"] = "mailAlias";
    @ldap_add($ldap, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAIN_ROOT_DN, $alias);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

function modifyAlias ($ldap, $domain, $alias) {
    @ldap_modify($ldap, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAIN_ROOT_DN, $alias);
    if ( ldap_errno($ldap) !== 0 ) {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

function deleteAlias ($ldap, $domain, $alias) {
    @ldap_delete($ldap, "uid=".$alias.",dc=".$domain.",".LDAP_DOMAIN_ROOT_DN);
    if ( ldap_errno($ldap) !== 0 )
    {
        $result = ldap_error($ldap);
    } else {
        $result = 0;
    }
    return ($result);
}

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
