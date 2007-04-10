<?php
/**
 * Content Domain Edit
 * 
 * This content module is used for creating the domain edit form.
 *
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 *
 */

class content_domain_edit extends module_base
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
        $domain = $_GET["domain"]; 
        
        // new domain created or existing domain altert 
        if ( isset($_POST["submit"]) ) {
            // remove all non LDAP objects from submited form
            // an the submit and mode value
            $my_domain = remove_key_by_str($_POST,"nlo_");
            unset($my_domain["submit"]);
            unset($my_domain["mode"]);
            
            ( isset($_POST["mailstatus"]) ? $my_domain["mailstatus"] = "TRUE" : $my_domain["mailstatus"] = "FALSE");

            switch ($_POST["mode"]) {
                case "add":
                    $this->ldap->addDomain($my_domain);
                    $domain = $my_domain["dc"];
                break;
                case "modify": 
                    $this->ldap->modifyDomain($my_domain);
                break;
            }
            $submit_status = ldap_errno($this->ldap->cid);
            ( $submit_status == "0" ? $this->smarty->assign("submit_status",$submit_status) : $this->smarty->assign("submit_status",ldap_err2str($submit_status))); 
        } else {
            $this->smarty->assign("submit_status",-1);
        }

        if ( $domain == "new" ) {
            $this->smarty->assign("mode","add");
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("domain",$this->ldap->getDomain($domain));
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
		$_content = $this->smarty->fetch('content_domain_edit.tpl');
   	    return $_content;
  }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
