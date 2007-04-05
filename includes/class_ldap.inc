<?php

 
function myldap_delete($ds,$dn,$recursive=false){
  if($recursive == false)
  {
      return(ldap_delete($ds,$dn));
  } else
  {
    //searching for sub entries
    $sr = ldap_list($ds,$dn,"ObjectClass=*",array(""));
    $info = ldap_get_entries($ds, $sr);
    for($i=0;$i<$info['count'];$i++){
      //deleting recursively sub entries
      $result = myldap_delete($ds,$info[$i]['dn'],$recursive);
      if(!$result)
      {
        //return result code, if delete fails
        return($result);
      }
    }
    return (@ldap_delete($ds,$dn));
  }
}
 

class USERS{

  var $owner;

  function USERS( &$ref ) {
    $this->owner = &$ref;
  }

  function get( $domaindn, $search )
  {
     $result = @ldap_list($this->owner->cid,$domaindn, "(&(objectclass=mailUser)(uid=".$search.'))');
     $users = @ldap_get_entries($this->owner->cid, $result);
     return($users);
  }

  function add ($user,$domain)
  {
    ldap_add($this->owner->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINDN, $user);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }

  function edit ($user,$domain)
  {
    @ldap_modify($this->owner->cid, "uid=".$user['uid'].",dc=".$domain.",".LDAP_DOMAINDN, $user);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }

}

class ALIASES{

  var $owner;

  function ALIASES( &$ref ) {
    $this->owner = &$ref;
  }

  function get( $domaindn, $search )
  {
     $result = @ldap_list($this->owner->cid,$domaindn, "(&(objectclass=mailAlias)(uid=".$search.'))');
     $aliases = @ldap_get_entries($this->owner->cid, $result);
     return($aliases);
  }

  function add ($alias,$domain)
  {
    @ldap_add($this->owner->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINDN, $alias);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }

  function edit ($alias,$domain)
  {
    @ldap_modify($this->owner->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINDN, $alias);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }

  function delete ($alias,$domain)
  {
    @ldap_delete($this->owner->cid, "uid=".$alias['uid'].",dc=".$domain.",".LDAP_DOMAINDN);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }

}

class DOMAINS{

  var $owner;

  function DOMAINS( &$ref ) {
    $this->owner = &$ref;
  }

  function get( $rootdn, $search)
  {
     $result = @ldap_list($this->owner->cid,$rootdn, "dc=".$search);
     $domains = @ldap_get_entries($this->owner->cid, $result);
     return($domains);
  }

  function add ($domain)
  {
    @ldap_add($this->owner->cid, "dc=".$domain['dc'].",".LDAP_DOMAINDN, $domain);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }

  function delete ($domain)
  {
    @myldap_delete($this->owner->cid,"dc=$domain,".LDAP_DOMAINDN,$recursive=true);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }
  
  function set_adm($domaindn,$admin,$mode)
  {
    if ( count($admin['uniquemember']) )
    {
      if ( $mode == 'mod' )
        @ldap_modify($this->owner->cid,"cn=admin,$domaindn",$admin);
      elseif ( $mode == 'add' )
        @ldap_add($this->owner->cid,"cn=admin,$domaindn",$admin);
    }
    else
    {
      @ldap_delete($this->owner->cid,"cn=admin,$domaindn");
    }
  }

  function get_adm($domaindn)
  {
    $result = ldap_list($this->owner->cid,$domaindn,"cn=admin");
    $entries = ldap_get_entries($this->owner->cid,$result);
    if ( $entries['count'] != 0 )
    {
      return $entries[0];
    }
    else
    {
      return 0;
    }
  }

  function get_asp($basedn)
  {
    $result = @ldap_search($this->owner->cid,"$basedn","(&(uid=*)(objectclass=mailuser))");
    $entries_user = @ldap_get_entries($this->owner->cid,$result);

    return $entries_user;
  }

  function modify ($domain)
  {

    @ldap_modify($this->owner->cid, "dc=".$domain["dc"].",".LDAP_DOMAINDN, $domain);
    if ( ldap_errno($this->owner->cid) !== 0 )
    {
      $result = ldap_error($this->owner->cid);
    } else
    {
      $result = 0;
    }
    return ($result);
  }
}

class LDAP{

  var $tls       = false; // Don't use TLS by default
  var $basedn    = "";    // Base DN of LDAP Tree
  var $cid;	              // Connection ID
  var $error     = "";    // Any error messages to be returned, value of 0 if no error, otherwise error string
  var $binddn    = "";    // DN for binding to LDAP
  var $bindpw    = "";    // Password for DN
  var $hostname  = "";    // Hostname or IP of LDAP Server
  var $users;
  var $domains;

  function LDAP ($binddn, $bindpw, $hostname, $tls= FALSE)
  {
    $this->tls      = $tls;
    $this->binddn   = $binddn;
    $this->bindpw   = $bindpw;
    $this->hostname = $hostname;
    $this->connect();

    $this->users = new USERS(&$this);
    $this->aliases = new ALIASES(&$this);
    $this->domains = new DOMAINS(&$this);
  } // end function LDAP

  function connect()
  {
    if ($this->cid = ldap_connect($this->hostname)) {
      @ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);

      if (function_exists("ldap_start_tls") && $this->tls){
        @ldap_start_tls($this->cid);
      }

      $this->error = "";
      if ($bind = ldap_bind($this->cid, $this->binddn, $this->bindpw)) {
        $this->error = "";
      } else {
        $this->error = "Could not bind ".$this->binddn." to host ".$this->hostname;
      }
    } else {
      $this->error = "Could not connect to LDAP server ".$this->hostname;
    }
  } // end function connect
}



// vim:tabstop=2:expandtab:shiftwidth=2:filetype=php:syntax:ruler:
?>
