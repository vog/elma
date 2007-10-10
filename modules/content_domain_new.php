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
 * content domain new
 * 
 * This content module is used for creatinga a new domain and 
 * handling the submited data.
 */

class content_domain_new extends module_base
{

    /**
     * Constructor of this class
     */
    function content_domain_new() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        if ( ! empty($_GET["domain"]) ) { 
            $domain = $_GET["domain"];  
            $this->smarty->assign("domain",$domain);
        }
        $this->smarty->assign("mailstorageservers",unserialize(MAILSTORAGESERVERS));

        // new domain created or existing domain altert 
        if (isset($_POST["submit"])) {
            if(!empty($_POST["nlo_next_step"])) {
                $next_step = $_POST["nlo_next_step"];
            }
            else {
                $next_step = "";
            }

            // remove all non LDAP objects from submited form
            // an the submit value
            $my_domain = remove_key_by_str($_POST,"nlo_");

            if (isset($_POST["admins"])) {
                $admins = $my_domain["admins"];
                unset($my_domain["admins"]);
            }

            if (isset($_POST["nonadmins"])) {
                $nonadmins = $my_domain["nonadmins"];
                unset($my_domain["nonadmins"]);
            }

            unset($my_domain["submit"]);
            
            if (isset($_POST["mailstatus"])) {
                $my_domain["mailstatus"] = "TRUE";
            } else {
                $my_domain["mailstatus"] = "FALSE";
            }
         
            $validation_errors = validate_domain($my_domain);
            if (count($validation_errors) == 0) {
                if (!isset($admins)) {
                    $admins = array();
                }

                $my_domain["mailsievefilter"] = createEximFilter( loadEximFilterTemplates() );
                $this->ldap->addDomain($my_domain, $admins);
                        
                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $domain = $my_domain["dc"];
                    switch($next_step) {
                    case 'show_overview':
                        Header("Location: index.php?module=users_list&domain=" . urlencode($domain) );
                        exit;
                        break;
                    case 'show_domain_list':
                        Header("Location: index.php?module=domains_list");
                        exit;
                        break;
                    case 'add_another':
                        // nothing
                        break;
                    }
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

        $this->smarty->assign("domain",array());
            
        $systemusers = $this->ldap->listSystemUsers();
        unset($systemusers["count"]);

        $this->smarty->assign("nonadmins", $systemusers);
    }

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() 
    {
        $_content = $this->smarty->fetch('content_domain_new.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
