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
 * content systemsystemuser edit
 * 
 * This content module is used for creating the systemsystemuser edit/add 
 * form and handling the submited data.
 */

class content_systemsystemuser_edit extends module_base
{

    /**
     * Constructor of this class
     */
    function content_systemsystemuser_edit() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        $systemuser = $_GET["systemuser"]; 
        $domain =  $_GET["domain"];
        $this->smarty->assign("domain",$domain);

        // new systemuser created or existing systemuser modified
        if (isset($_POST["submit"])) {
            // remove all non LDAP objects from submited form
            // an the submit and mode value
            $my_systemuser = remove_key_by_str($_POST,"nlo_");
            unset($my_systemuser["submit"]);
            unset($my_systemuser["mode"]);

            if (isset($_POST["mailstatus"])) {
                $my_systemuser["mailstatus"] = "TRUE";
            } else {    
                $my_systemuser["mailstatus"] = "FALSE";
            }

            if (! $my_systemuser["clearpassword"] == "") { 
                $my_systemuser["systemuserpassword"] =  "{MD5}".base64_encode(pack("H*",md5($my_systemuser["clearpassword"])));
            }
        
            if (! defined(SAVECLEARPASS)) {
                unset($my_systemuser["clearpassword"]);
            }
                   
            $validation_errors = validate_systemuser($my_systemuser);
            if (count($validation_errors) == 0) {
                switch ($_POST["mode"]) {
                    case "add":
                        $this->ldap->addUser($domain,$my_systemuser);
                    break;
                    case "modify": 
                        $this->ldap->modifyUser($domain,$my_systemuser);
                    break;
                }

                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $systemuser = $my_systemuser["uid"];
                } else {
                    $this->smarty->assign("submit_status",ldap_err2str($submit_status));
                }
            } else {
               $this->smarty->assign("submit_status","Invalid Data");
               $this->smarty->assign("validation_errors",$validation_errors);
            }
        } else {
            $this->smarty->assign("submit_status",-1);
        }

        if ( $systemuser == "new" ) {
            $this->smarty->assign("mode","add");
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("systemuser",$this->ldap->getUser($domain,$systemuser));
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
        $_content = $this->smarty->fetch('content_systemuser_edit.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
