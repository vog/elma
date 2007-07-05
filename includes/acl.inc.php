<?php

// ACLs
define ("ACL",serialize(array(
                          "systemadmin" => array("main","domains_list","domain_edit","domain_delete","users_list","user_edit","user_delete","alias_edit","alias_delete","settings","systemusers_list","systemuser_edit","systemuser_delete","globaladmins_edit"),
                          "domainadmin" => array("main","domains_list","domain_edit","users_list","user_edit","user_delete","alias_edit","alias_delete"),
                          "user" => array("main")
                       )));

?>
