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
            // remove all non LDAP objects from submited form
            // an the submit and mode value
            $my_systemuser = remove_key_by_str($_POST,"nlo_");
            unset($my_systemuser["submit"]);
            unset($my_systemuser["mode"]);

            if (! $my_systemuser["clearpassword"] == "") { 
                $my_systemuser["userpassword"] =  "{MD5}".base64_encode(pack("H*",md5($my_systemuser["clearpassword"])));
            }

            if (! defined(SAVECLEARPASS)) {
                unset($my_systemuser["clearpassword"]);
            }

            unset($my_systemuser["clearpassword"]);

            $validation_errors = validate_systemuser($my_systemuser);
            if (count($validation_errors) == 0) {
                switch ($_POST["mode"]) {
                    case "add":
                        $this->ldap->addSystemUser($my_systemuser);
                    break;
                    case "modify": 
                        unset ($my_systemuser["domains"]);
                        $domainsin = $this->ldap->getSystemUsersDomains($systemuser);
                        
                        /* filter the values out of the dc */
                        $tmpdomainsin = $domainsin;

                        $domainsin = array();

                        $tmp = null;

                        foreach($tmpdomainsin as $domainin) {
                            $tmp = explode("=", $domainin);
                            array_push($domainsin, $tmp[1]);
                        }

                        if (isset($my_systemuser["domainsin"])) {
                            $tmpdomainsin = $my_systemuser["domainsin"];
                            unset($my_systemuser["domainsin"]);
                        } else {
                            $tmpdomainsin = array();
                        }

                        $this->ldap->modSystemUser($my_systemuser);

                        $addDomainAdmin = array();
                        $delDomainAdmin = array();


                        /* check if the user is admin already */
                        /* and put him onto the add array if not */
                        foreach($tmpdomainsin as $tmpdomainin) {
                            $isinarray = 0;
                            foreach($domainsin as $domainin) {
                                if ($domainin == $tmpdomainin) {
                                    $isinarray = 1;
                                    break;
                                }
                            }   

                            if ($isinarray == 0) {
                                array_push($addDomainAdmin, $tmpdomainin);
                            }                        
                        }
                        /* check if the user used to be admin */
                        /* and put him onto the del array if he isn't any longer */
                        foreach($domainsin as $domainin) {
                            $isinarray = 0;
                            foreach($tmpdomainsin as $tmpdomainin) {
                                if ($tmpdomainin == $domainin) {
                                    $isinarray = 1;
                                    break;
                                }
                            }

                            if ($isinarray == 0) {
                                array_push($delDomainAdmin, $domainin);
                            }
                        }

                        if (isset($addDomainAdmin)) {
                            foreach($addDomainAdmin as $domain) {
                                $this->ldap->addAdminUsers($domain, "uid=".$systemuser.",".LDAP_USERS_ROOT_DN);
                            }
                        }
                        
                        if (isset($delDomainAdmin)) {
                            foreach($delDomainAdmin as $domain) {
                                $this->ldap->delAdminUsers($domain, "uid=".$systemuser.",".LDAP_USERS_ROOT_DN);
                            }
                        }
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
            $domainsin = $this->ldap->getSystemUsersDomains($systemuser);
            $tmpdomains = $this->ldap->listDomains();

            /* check in which domains the selected user is */
            if ($_SESSION["userclass"] == "admin" ) {
                /* filter the dc part out of the dn */
                unset($tmpdomains["count"]);

                $tmp = array();
                $domains = array();

                foreach($tmpdomains as $domain) {
                    $tmp = ldap_explode_dn($domain["dn"], 0);
                    array_push($domains, $tmp[0]);
                }

                /* filter the values out of the dc */
                $tmpdomains = $domains;
                $tmpdomainsin = $domainsin;

                $domains = array();
                $domainsin = array();

                $tmp = null;

                foreach($tmpdomains as $domain) {
                    $tmp = explode("=", $domain);
                    array_push($domains, $tmp[1]);
                }

                foreach($tmpdomainsin as $domain) {
                    $tmp = explode("=", $domain);
                    array_push($domainsin, $tmp[1]);
                }

                /* we want to have only the domains in $domains which aren't in $domainsin already */
                $tmpdomains = $domains;
                $domains = array();

                foreach($tmpdomains as $domain) {
                    $isin = 0;

                    foreach($domainsin as $domainin) {
                        if ($domainin == $domain) {
                            $isin = 1;
                            break;
                        }
                    }

                    if ($isin == 0) {
                        array_push($domains, $domain);
                    }
                }

                /* assign domain vars only if the logged in user is an admin */ 
                $this->smarty->assign("domains", $domains);
                $this->smarty->assign("domainsin", $domainsin);
            }

            $this->smarty->assign("mode","modify");
            $this->smarty->assign("user",$this->ldap->getSystemUser($systemuser));
            $this->smarty->assign("isadmin",$isadmin);
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
