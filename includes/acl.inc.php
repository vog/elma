<?php

// ACLs
define ("ACL",serialize(array(
                          "systemadmin" => array("main","domains_list","domain_new","domain_new.mailstorageserver","domain_new.spamfilter","domain_edit","domain_edit.mailstorageserver","domain_edit.spamfilter","domain_delete","users_list","user_new","user_edit","user_delete","alias_new","alias_edit","alias_delete","settings","systemusers_list","systemuser_edit","systemuser_delete","globaladmins_edit","statistics"),
                          "domainadmin" => array("main","domains_list","domain_edit","domain_edit.spamfilter","users_list","user_new","user_edit","user_delete","alias_new","alias_edit","alias_delete","statistics"),
                          "user" => array("main, user_edit, user_edit_himself")
                       )));

?>
