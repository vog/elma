<?php
/**
* Domain Module
*
* This module is used to create a list for users for the given domain.
*
* @author Daniel Weuthen
*
*/

class content_users_list extends module_base {

    /**
     * Constructor of this class
     *
     */
    function content_users_list() {
        parent::module_base();
    }


    /**
     * This method is called after the constructor by the main page
     *
     */
    function proceed() {

        if ((isset($_GET["mode"])) && ($_GET["mode"] == "delete")) {
            $this->ldap->deleteUser($_GET["user"]);
        }

        $my_users = array();
        $domain = $_GET["domain"];
        $users = $this->ldap->listUsers($domain);
        for ( $i = 0; $i < $users["count"]; $i++ ) {
            $user['uid'] = $users[$i]["uid"][0]; 
            $user['mailstatus'] = $users[$i]["mailstatus"][0];
            $user['deletelink'] = $_SERVER['PHP_SELF']."?module=users_list&domain=".$domain."&user=".$user['uid']."&mode=delete";
            $user['editlink'] = $_SERVER['PHP_SELF']."?module=domain_edit&domain=".$domain."&user=".$user['uid']; 
            array_push($my_users,$user);
        }
        $this->smarty->assign("link_newuser",$_SERVER['PHP_SELF']."?module=user_edit&domain=".$domain."&user=new");
        $this->smarty->assign('userss',$my_users);   
	}

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() {
        $_content = $this->smarty->fetch('content_users_list.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
