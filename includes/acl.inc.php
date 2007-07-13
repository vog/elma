<?php

// ACLs
define ("ACL",serialize(array(
                          "systemadmin" => array("main","domains_list","domain_new","domain_new.mailstorageserver","domain_edit","domain_edit.mailstorageserver","domain_delete","users_list","user_new","user_edit","user_delete","alias_new","alias_edit","alias_delete","settings","systemusers_list","systemuser_edit","systemuser_delete","globaladmins_edit","statistics"),
                          "domainadmin" => array("main","domains_list","domain_edit","users_list","user_new","user_edit","user_delete","alias_new","alias_edit","alias_delete","statistics"),
                          "user" => array("main")
                       )));

?>
