<?php
/**
 * Content User Delete
 * 
 * This content module is used to get a delete confirmation
 *
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 *
 */

class content_user_delete extends module_base
{

    /**
     * Constructor of this class
     *
     */
    function content_template() 
    {

    }

    /**
     * This method is called after the constructor by the main page
     *
     */
    function proceed() 
    {
        if ( isset($_POST["submit"]) ) {
            $uid = $_POST["uid"];
            $domain =  $_POST["domain"];
            $this->ldap->deleteUser($domain,$uid);
            $submit_status = ldap_errno($this->ldap->cid);
            ($submit_status == "0" ? $this->smarty->assign("submit_status",$submit_status) : $this->smarty->assign("submit_status",ldap_err2str($submit_status)));
        } else {
            $uid = $_GET["uid"];
            $domain =  $_GET["domain"];
            $this->smarty->assign("domain",$domain);
            $this->smarty->assign("user",$this->ldap->getUser($domain,$uid));
            $this->smarty->assign("submit_status",-1);
        }
    }

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() 
    {
        $_content = $this->smarty->fetch('content_user_delete.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
