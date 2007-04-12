<?php
/**
 * Content User Edit
 * 
 * This content module is used for creating the user edit form.
 *
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 *
 */

class content_user_edit extends module_base
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
        $user = $_GET["user"]; 
        $domain =  $_GET["domain"];
        // new user created or existing user altert 
        if ( isset($_POST["submit"]) ) {
            // remove all non LDAP objects from submited form
            // an the submit and mode value
            $my_user = remove_key_by_str($_POST,"nlo_");
            unset($my_user["submit"]);
            unset($my_user["mode"]);

            ( isset($_POST["mailstatus"]) ? $my_user["mailstatus"] = "TRUE" : $my_user["mailstatus"] = "FALSE" );
            $my_user["userpassword"] =  $my_user["clearpassword"];

            switch ($_POST["mode"]) {
                case "add":
                    $this->ldap->addUser($domain,$my_user);
                    $user = $my_user["uid"];
                break;
                case "modify": 
                    $this->ldap->modifyUser($domain,$my_user);
                break;
            }
            $submit_status = ldap_errno($this->ldap->cid);
            ( $submit_status == "0" ? $this->smarty->assign("submit_status",$submit_status) : $this->smarty->assign("submit_status",ldap_err2str($submit_status))); 
        } else {
            $this->smarty->assign("submit_status",-1);
        }

        if ( $user == "new" ) {
            $this->smarty->assign("mode","add");
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("user",$this->ldap->getUser($domain,$user));
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
		$_content = $this->smarty->fetch('content_user_edit.tpl');
   	    return $_content;
  }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
