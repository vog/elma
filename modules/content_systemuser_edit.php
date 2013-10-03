<?php
/**
 * @author Sven Ludwig <adan0s@adan0s.net>
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

class content_systemuser_edit extends module_base
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
        $systemuser = $_GET["user"]; 
        $this->smarty->assign("uid",$systemuser);

        // new systemuser created or existing systemuser modified
        if (isset($_POST["submit"])) {

            SmartyValidate::connect($this->smarty);
            if (SmartyValidate::is_valid($_POST)) {

                // save all needed information which are no ldap objects themself
                if ( !empty($_POST["nlo_adminofdomains"]) ) {
                    $new_adminofdomains = $_POST["nlo_adminofdomains"];
                }

                if(!empty($_POST["nlo_next_step"])) {
                    $next_step = $_POST["nlo_next_step"];
                }
                else {
                    $next_step = "";
                }

                // remove all non LDAP objects from submited form
                // an the submit and mode value
                $my_systemuser = remove_key_by_str($_POST,"nlo_");
                unset($my_systemuser["submit"]);
                unset($my_systemuser["mode"]);

                if (! $my_systemuser["clearpassword"] == "") { 
                    $my_systemuser["userpassword"] =  "{MD5}".base64_encode(pack("H*",md5($my_systemuser["clearpassword"])));
                }

                //if (! defined(SAVECLEARPASS) || empty($my_systemuser["clearpassword"])) {
                    unset($my_systemuser["clearpassword"]);
                // }

                if ( !isset($new_adminofdomains) || count($new_adminofdomains) == 0) $new_adminofdomains = array();
                $old_adminofdomains = $this->ldap->getSystemUsersDomains($systemuser);
                unset ($my_systemuser["adminofdomains"]);
                
                $this->ldap->modifySystemUser($my_systemuser);

                $addDomainAdmin = array();
                $delDomainAdmin = array();
                

                /* check if the user is admin already */
                /* and put him onto the add array if not */
                $addDomainAdmin = array_diff($new_adminofdomains,$old_adminofdomains);
                
                /* check if the user used to be admin */
                /* and put him onto the del array if he isn't any longer */
                $delDomainAdmin = array_diff($old_adminofdomains,$new_adminofdomains);

                if ( count($addDomainAdmin) > 0 ) {
                    foreach($addDomainAdmin as $domain) {
                        $this->ldap->addAdminUsers($domain, "uid=".$systemuser.",".LDAP_USERS_ROOT_DN);
                    }
                }
                
                if ( count($delDomainAdmin) > 0) {
                    foreach($delDomainAdmin as $domain) {
                        $this->ldap->deleteAdminUsers($domain, "uid=".$systemuser.",".LDAP_USERS_ROOT_DN);
                    }
                }

                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $systemuser = $my_systemuser["uid"];
                    switch($next_step) {
                    case 'show_overview':
                        Header("Location: ?module=systemusers_list" );
                        exit;
                        break;
                    case 'add_another':
                        Header("Location: ?module=systemuser_edit&user=new") ;
                        exit;
                        break;
                    case 'edit_current':
                        //nothing..
                        break;
                    }
                } else {  // LDAP error occured
                    $this->smarty->assign("submit_status",ldap_err2str($submit_status));
                }
            } else { // input validation failed
                $this->smarty->assign($_POST);
            }
        } else { // form has not yet been submitted
            $this->smarty->assign("submit_status",-1);
            SmartyValidate::connect($this->smarty, true);
            SmartyValidate::register_validator('uid', 'uid', 'notEmpty');
            SmartyValidate::register_validator('cn', 'cn', 'notEmpty');
            SmartyValidate::register_validator('sn', 'sn', 'notEmpty');
            //SmartyValidate::register_validator('password', 'clearpassword', 'notEmpty');
        }


        $adminofdomains = $this->ldap->getSystemUsersDomains($systemuser);
        $domains_dn = $this->ldap->listDomains();

        /* check in which domains the selected user is */
        if ($_SESSION["userclass"] == "systemadmin" ) {
            /* filter the dc part out of the dn */
            unset($domains_dn["count"]);

            $domain = array();
            $domains = array();

            foreach($domains_dn as $domain_dn) {
                $domain = ldap_explode_dn($domain_dn["dn"], 1);
                array_push($domains, $domain[0]);
            }

            /* we want to have only the domains in $domains which aren't in $domainsin already */
            $available_domains = array();
            $available_domains = array_diff($domains,$adminofdomains);

            /* assign domain vars only if the logged in user is an admin */ 
            $this->smarty->assign("availabledomains", $available_domains);
            $this->smarty->assign("adminofdomains", $adminofdomains);
        }

        if ( $systemuser == "new" ) {
            $this->smarty->assign("mode","add");
            
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("user",$this->ldap->getSystemUser($systemuser));
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
