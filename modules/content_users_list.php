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

        $domain = $_GET["domain"];
        $this->smarty->assign('domain',$domain);

        /**
         * prepare users array for smarty output
         */
        $my_users = array();
        $users = $this->ldap->listUsers($domain);
        for ($i = 0; $i < $users["count"]; $i++) {
            $user['uid'] = $users[$i]["uid"][0]; 
            $user['mailstatus'] = $users[$i]["mailstatus"][0];
            $user['deletelink'] = $_SERVER['PHP_SELF']."?module=user_delete&amp;domain=".$domain."&amp;uid=".$user['uid']."&amp;mode=delete";
            $user['editlink'] = $_SERVER['PHP_SELF']."?module=user_edit&amp;domain=".$domain."&amp;user=".$user['uid']; 
            array_push($my_users,$user);
        }
        $this->smarty->assign("link_newuser",$_SERVER['PHP_SELF']."?module=user_edit&amp;domain=".$domain."&amp;user=new");
        $this->smarty->assign("link_newalias",$_SERVER['PHP_SELF']."?module=alias_edit&amp;domain=".$domain."&amp;alias=new");
        $this->smarty->assign('users',$my_users);

        /**
         * prepare aliases array for smarty output
         */
        $my_aliases = array();
        $aliases = $this->ldap->listAliases($domain);
        for ($i = 0; $i < $aliases["count"]; $i++) {
            $alias['uid'] = $aliases[$i]["uid"][0]; 
            $alias['mailaliasedname'] = $aliases[$i]["mailaliasedname"];
            $alias['deletelink'] = $_SERVER['PHP_SELF']."?module=aliases_list&amp;domain=".$domain."&amp;alias=".$alias['uid']."&amp;mode=delete";
            $alias['editlink'] = $_SERVER['PHP_SELF']."?module=alias_edit&amp;domain=".$domain."&amp;alias=".$alias['uid']; 
            array_push($my_aliases,$alias);
        }
        $this->smarty->assign("link_newalias",$_SERVER['PHP_SELF']."?module=alias_edit&amp;domain=".$domain."&amp;alias=new");
        $this->smarty->assign("link_newalias",$_SERVER['PHP_SELF']."?module=alias_edit&amp;domain=".$domain."&amp;alias=new");
        $this->smarty->assign('aliases',$my_aliases);
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
