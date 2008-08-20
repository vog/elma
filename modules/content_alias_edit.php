<?php
/**
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 * @version $LastChangedRevision$
 * @package ELMA
 *
 * $Id$
 * $LastChangedBy$
 *
 * =====================================================================
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA
 *
 * =====================================================================
 */

/**
 * content alias edit
 * 
 * This content module is used for creating the alias edit form and handling
 * the submited data.
 */

class content_alias_edit extends module_base
{

    /**
     * Constructor of this class
     */
    function content_alias_edit() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        $alias = $_GET["alias"]; 
        $domain =  $_GET["domain"];
        $this->smarty->assign("domain",$domain);

        // existing alias altert 
        if (isset($_POST["submit"])) {
            SmartyValidate::connect($this->smarty);
            if (SmartyValidate::is_valid($_POST)) {
                // remove all non LDAP objects from submited form
                // an the submit and mode value
                $my_alias = remove_key_by_str($_POST,"nlo_");
                unset($my_alias["submit"]);

                if (isset($_POST["mailstatus"])) {
                    $my_alias["mailstatus"] = "TRUE";
                } else {
                    $my_alias["mailstatus"] = "FALSE";
                }
                
                $my_alias["mailaliasedname"] = preg_split("/\r?\n/", $_POST['nlo_mailaliasedname']);
                
                $this->ldap->modifyAlias($domain,$my_alias);
                
                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $alias = $my_alias["uid"];
                } else { // LDAP error occured
                     $this->smarty->assign("submit_status",ldap_err2str($submit_status));
                }
            } else { // input validation failed
                 $this->smarty->assign($_POST);
            } 
        } else {
            $this->smarty->assign("submit_status",-1);
            SmartyValidate::connect($this->smarty, true);
            SmartyValidate::register_validator('nlo_mailaliasedname', 'nlo_mailaliasedname', 'notEmpty');
        }

        $al = $this->ldap->getAlias($domain,$alias);
        unset($al['mailaliasedname']['count']);
        $al['mailaliasedname'] = implode("\r\n", $al['mailaliasedname']);
        $this->smarty->assign("alias",$al);
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
