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
 * content alias new
 * 
 * This content module is used for creating the alias new form and handling
 * the submited data.
 */

class content_alias_new extends module_base
{

    /**
     * Constructor of this class
     */
    function content_alias_new() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        if ( !empty($_GET["alias"]) ) {
            $alias = $_GET["alias"]; 
        }

        $domain =  $_GET["domain"];
        $this->smarty->assign("domain",$domain);

        // new alias created or existing alias altert 
        if (isset($_POST["submit"])) {
            SmartyValidate::connect($this->smarty);

            if(SmartyValidate::is_valid($_POST)) {

                if(!empty($_POST["nlo_next_step"])) {
                    $next_step = $_POST["nlo_next_step"];
                }
                else {
                    $next_step = "";
                }

                // remove all non LDAP objects from submited form
                // an the submit and mode value
                $my_alias = remove_key_by_str($_POST,"nlo_");
                $my_alias["uid"] = strtolower($my_alias["uid"]);
                unset($my_alias["submit"]);

                if (isset($_POST["mailstatus"])) {
                    $my_alias["mailstatus"] = "TRUE";
                } else {
                    $my_alias["mailstatus"] = "FALSE";
                }

                $my_alias["mailaliasedname"] = preg_split("/\r?\n/", $_POST['nlo_mailaliasedname']);

                // add alias to LDAP
                $this->ldap->addAlias($domain,$my_alias);

                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $alias = $my_alias["uid"];
                    switch($next_step) {
                    case 'show_overview':
                        SmartyValidate::disconnect();
                        Header("Location: ?module=users_list&domain=" . urlencode($domain) );
                        exit;
                        break;
                    case 'add_another':
                        // nothing
                        break;
                    }
                } else { // LDAP error occured
                     $this->smarty->assign("submit_status",ldap_err2str($submit_status));
                }
            } else { // input validation failed
                my_print_r($_POST);
                $this->smarty->assign("alias",$_POST);
            }
        } else { // form has not yet been submitted
            $this->smarty->assign("submit_status",-1);
            SmartyValidate::connect($this->smarty, true);
            SmartyValidate::register_validator('uid', 'uid', 'notEmpty');
            SmartyValidate::register_validator('nlo_mailaliasedname', 'nlo_mailaliasedname', 'notEmpty');
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
        $_content = $this->smarty->fetch('content_alias_new.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
