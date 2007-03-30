<?php

function ic_returnStatus ($statuscode) {
  if ( $statuscode !== 0 ) { 
      echo "<span id=red>failed: $statuscode</span><br>"; 
  } else { 
      echo "<span id=green>Ok</span><br>"; 
  }
}

function ic_connect($hostname) {
    print "connect...<br />";
    $cid = my_ldapConnect($hostname,0);
    return $cid;
}

function ic_bind($cid) {
    print "binding...";
    $rc = my_ldapBind($cid,"cn=admin,ou=users,o=megabit","nimda");
    ic_returnStatus($rc);
}

# Domain Install Checks
function ic_listDomains($cid) {
    print "list domains...<br>";
    $domains = listDomains($cid);
    for ($i=0; $i < $domains["count"]; $i++) {
        echo "<li>".$domains[$i]["dc"][0].", status: ".$domains[$i]["mailstatus"][0]."</li>";
    }
}

function ic_addDomain($cid,$domain) {
    print "adding domain ".$domain["dc"]."...";
    $rc = addDomain($cid,$domain);
    ic_returnStatus($rc);
}

function ic_modifyDomain($cid,$domain) {
    print "modifying domain ".$domain["dc"]."...";
    $rc = modifyDomain($cid,$domain);
    ic_returnStatus($rc);
}

function ic_deleteDomain($cid,$domain) {
    print "deleting ".$domain["dc"]."...";
    $rc = deleteDomain($cid,$domain["dc"]);
    ic_returnStatus($rc);
}

# User Install Checks
function ic_listUsers($cid,$domain) {
    print "listing users...<br />";
    $users = listUsers($cid,$domain["dc"]);
    for ($i=0; $i < $users["count"]; $i++) {
        echo "<li>".$users[$i]["uid"][0].", common name: ".$users[$i]["cn"][0]."</li>";
    }
}

function ic_addUser($cid,$domain,$user) {
    print "adding user ".$user['uid']."...";
    $rc = adduser($cid,$domain["dc"],$user);
    ic_returnStatus($rc);
}

function ic_modifyUser($cid,$domain,$user) {
    print "modifying user ".$user['uid']."...";
    $rc = modifyuser($cid,$domain["dc"],$user);
    ic_returnStatus($rc);
}

function ic_deleteUser($cid,$domain,$user) {
    print "delete user ".$user['uid']."...";
    $rc = deleteUser($cid,$domain["dc"],$user["uid"]);
    ic_returnStatus($rc);
}


# Alias Install Checks
function ic_listAliases($cid,$domain) {
    print "listing aliases...<br />";
    $aliases = listAliases($cid,$domain["dc"]);
    for ($i=0; $i < $aliases['count']; $i++) {
        echo "<li>".$aliases[$i]['uid'][0]."</li>";
    }
}

function ic_addAlias($cid,$domain,$alias) {
    print "adding alias ".$alias['uid']."...";
    $rc = addAlias($cid,$domain['dc'],$alias);
    ic_returnStatus($rc);
}

function ic_modifyAlias($cid,$domain,$alias) {
    print "modifying alias ".$alias['uid']."...";
    $rc = modifyAlias($cid,$domain['dc'],$alias);
    ic_returnStatus($rc);
}

function ic_deleteAlias($cid,$domain,$alias) {
    print "deleting alias ".$alaias['uid']."...";
    $rc = deleteAlias($cid,$domain['dc'],$alias['uid']);
    ic_returnStatus($rc);
}

# main

function runinstchecks() {


    $domain["dc"] = "test.org";
    
    $user["uid"] = "jd";
    $user["sn"] = "Doe";
    $user["cn"] = "John Doe";
    $user["userPassword"] = "abc";
    $user["clearpassword"] = "123";

    $alias["uid"] = "john.doe";
    $alias["mailAliasedName"][0] = $user["uid"]."@".$domain["dc"];
 
    $cid = ic_connect("ldap://127.0.0.1");
    ic_bind($cid);
    ic_addDomain($cid,$domain);
    ic_listDomains($cid);
    echo "<span id=italic>You should see at least one domain named ".$domain["dc"]."</span><br>";
    $domain["mailStatus"] = "TRUE";
    ic_modifyDomain($cid,$domain);
    ic_listDomains($cid);
    echo "<span id=italic>You should see at least one domain named ".$domain["dc"]." and with status: TRUE</span><br>";
    ic_addUser($cid,$domain,$user);
    ic_listUsers($cid,$domain);
    echo "<span id=italic>You should see at least one user named ".$user["uid"]." and with common name '".$user["cn"]."'</span><br>";
    $user["cn"] = "Jane Doe";
    ic_modifyUser($cid,$domain,$user);
    ic_listUsers($cid,$domain);
    echo "<span id=italic>You should see at least one user named ".$user["uid"]." and with common name '".$user["cn"]."'</span><br>";
    ic_addAlias($cid,$domain,$alias);
    ic_listAliases($cid,$domain);
    echo "<span id=italic>You should see at least one alias named ".$alias["uid"]." and alias to ".$alias["mailAliasedName"][0].".</span><br>";
    $alias["mailAliasedName"][1] = "jane.doe@".$domain["dc"];
    ic_modifyAlias($cid,$domain,$alias);
    ic_listAliases($cid,$domain);
    echo "<span id=italic>You should see at least one alias named ".$alias["uid"]." and alias to ".$alias["mailAliasedName"][0]." and ".$alias["mailAliasedName"][1].".</span><br>";
    ic_deleteAlias($cid,$domain,$alias);
    ic_listAliases($cid,$domain);
    echo "<span id=italic>You should see no alias named ".$alias["uid"].".</span><br>";
    ic_deleteUser($cid,$domain,$user);
    ic_listUsers($cid,$domain);
    echo "<span id=italic>You should see no users named ".$user["uid"].".</span><br>";
    ic_deleteDomain($cid,$domain);
    ic_listDomains($cid);
    echo "<span id=italic>You should see no domain named ".$domain["dc"]."</span><br>";
    
   

    /* Do smarty setup */
    #require("/usr/share/php/smarty/libs/Smarty.class.php");
    #$smarty = new Smarty;
    #$smarty->template_dir = 'tpl/';
    #$smarty->caching= false;
    ##$smarty->php_handling= SMARTY_PHP_REMOVE;

    #$smarty->assign("domains", $domains);
    #$content = $smarty->fetch("/var/www/elma/templates/test.tpl");
    #print $content;
}

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
