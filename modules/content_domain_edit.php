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
 * content domain edit
 * 
 * This content module is used for creating the domain edit form and 
 * handling the submited data.
 */

class content_domain_edit extends module_base
{

    /**
     * Constructor of this class
     */
    function content_domain_edit() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        $domain = $_GET["domain"]; 
        $this->smarty->assign("domain",$domain);
        
        // new domain created or existing domain altert 
        if (isset($_POST["submit"])) {
            // remove all non LDAP objects from submited form
            // an the submit and mode value
            $my_domain = remove_key_by_str($_POST,"nlo_");
            unset($my_domain["submit"]);
            unset($my_domain["mode"]);
            
            if (isset($_POST["mailstatus"]) {
                $my_domain["mailstatus"] = "TRUE";
            } else {
                 $my_domain["mailstatus"] = "FALSE");
            }

            $domain = $my_domain["dc"];

            switch ($_POST["mode"]) {
                case "add":
                    $this->ldap->addDomain($my_domain);
                break;
                case "modify": 
                    $this->ldap->modifyDomain($my_domain);
                break;
            }
            
            $submit_status = ldap_errno($this->ldap->cid);
            if ($submit_status == "0") {
                $this->smarty->assign("submit_status",$submit_status);
            } else { 
                $this->smarty->assign("submit_status",ldap_err2str($submit_status));
            }
        } else {
            $this->smarty->assign("submit_status",-1);
        }

        if ($domain == "new") {
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
