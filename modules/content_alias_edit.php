<?php
/**
 * Content Alias Edit
 * 
 * This content module is used for creating the alias edit form.
 *
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 *
 */

class content_alias_edit extends module_base
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
        $alias = $_GET["alias"]; 
        $domain =  $_GET["domain"];
        // new alias created or existing alias altert 
        if (isset($_POST["submit"])) {
            // remove all non LDAP objects from submited form
            // an the submit and mode value
            $my_alias = remove_key_by_str($_POST,"nlo_");
            unset($my_alias["submit"]);
            unset($my_alias["mode"]);

            $my_alias["mailaliasedname"] = explode("\n", $_POST['nlo_mailaliasedname']);

            switch ($_POST["mode"]) {
                case "add":
                    $this->ldap->addAlias($domain,$my_alias);
                    $alias = $my_alias["uid"];
                break;
                case "modify": 
                    $this->ldap->modifyAlias($domain,$my_alias);
                break;
            }
            $submit_status = ldap_errno($this->ldap->cid);
            ($submit_status == "0" ? $this->smarty->assign("submit_status",$submit_status) : $this->smarty->assign("submit_status",ldap_err2str($submit_status))); 
        } else {
            $this->smarty->assign("submit_status",-1);
        }

        if ( $alias == "new" ) {
            $this->smarty->assign("mode","add");
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("alias",$this->ldap->getAlias($domain,$alias));
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
        $_content = $this->smarty->fetch('content_alias_edit.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
