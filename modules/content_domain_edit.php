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

            if (isset($_POST["admins"])) {
                $admins = $my_domain["admins"];
                unset($my_domain["admins"]);
            }

            if (isset($_POST["nonadmins"])) {
                $nonadmins = $my_domain["nonadmins"];
                unset($my_domain["nonadmins"]);
            }

            unset($my_domain["submit"]);
            unset($my_domain["mode"]);
            
            if (isset($_POST["mailstatus"])) {
                $my_domain["mailstatus"] = "TRUE";
            } else {
                $my_domain["mailstatus"] = "FALSE";
            }
         
            $validation_errors = validate_domain($my_domain);
            if (count($validation_errors) == 0) {
                switch ($_POST["mode"]) {
                    case "add":
                        if (!isset($admins)) {
                            $admins = array();
                        }

                        $this->ldap->addDomain($my_domain, $admins);
                        break;
                    case "modify": 
                        $this->ldap->modifyDomain($my_domain);

                        $ldapadmins = $this->ldap->listAdminUsers($domain);
                        
                        $count = 0;

                        if (!isset($admins)) {
                            $admins = array();
                        }

                        /* create array of new admins */
                        foreach ($admins as $admin) {
                            $isinarray = 0;
                            for ($c=0; $c < $ldapadmins[0]["member"]["count"]; $c++) {
                                if ($admin == $ldapadmins[0]["member"][$c]){
                                    $isinarray = 1;
                                    break;
                                }
                            }

                            if ($isinarray == 0) {
                                $adminsadd[$count] = $admin;
                                $count++;
                            }
                        }

                        $count = 0;

                        /* create array of removed admins */
                        for ($i=0; $i < $ldapadmins[0]["member"]["count"]; $i++) {
                            $isinarray = 0;
                            foreach ($admins as $admin) {
                                if ($ldapadmins[0]["member"][$i] == $admin) {
                                    $isinarray = 1;
                                    break;
                                }
                            }

                            if ($isinarray == 0) {
                                $adminsdel[$count] = $ldapadmins[0]["member"][$i];
                                $count++;
                            }
                        }

                        if (isset($adminsadd)) {
                            $this->ldap->addAdminUsers($domain, $adminsadd);
                        }
                        if (isset($adminsdel)) {
                            $this->ldap->delAdminUsers($domain, $adminsdel);
                        }
                        break;
                }
                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $domain = $my_domain["dc"];
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

        if ($domain == "new") {
            $this->smarty->assign("mode","add");
            $this->smarty->assign("domain",array());

            $users = $this->ldap->listSystemUsers();

            if (isset($users)) {
                unset($users["count"]);

                $tmpusers = $users;
                $users = array();

                foreach ($tmpusers as $user) {
                    $user["mailUser"] = 0;
                    array_push($users, $user);
                }

                $tmp["sysUser"] = 1;

                $this->smarty->assign("nonadmins", $users);
                $this->smarty->assign("notnullnonadmins", $tmp);
            }
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("domain",$this->ldap->getDomain($domain));
            
            $tmpadmins = $this->ldap->listAdminUsers($domain);
            $tmpusers = $this->ldap->listSystemUsers();
            $mailusers = $this->ldap->listUsers($domain);

            $users = array();
            $admins = array();

            unset($tmpusers["count"]);
            unset($mailusers["count"]);

            foreach ($tmpusers as $tmpuser) {
                array_push($users, $tmpuser);
            }

            foreach ($mailusers as $mailuser) {
                array_push($users, $mailuser);
            }

            $tmpusers = $users;
            $users = array();

            if (isset($tmpadmins[0])) {
                foreach ($tmpusers as $user)
                {
                    $isset = 0;
                    unset ($tmpadmins[0]["member"]["count"]);

                    foreach ($tmpadmins[0]["member"] as $admin) {
                        if ($user["dn"] == $admin) {
                            $isset = 1;
                            $tmp = $this->ldap->getEntry($admin);
                            if (strstr($tmp[0]["dn"], LDAP_DOMAINS_ROOT_DN)) {
                                $tmp[0]["mailUser"] = 1;
                                $notnulladmins["mailUser"] = 1;
                            } else {
                                $tmp[0]["mailUser"] = 0;
                                $notnulladmins["sysUser"] = 1;
                            }
                            array_push($admins, $tmp[0]);
                            break;
                        }
                    }

                    if ($isset == 0) {
                        if (strstr($user["dn"], LDAP_DOMAINS_ROOT_DN)) {
                            $user["mailUser"] = 1;
                            $notnullusers["mailUser"] = 1;
                        } else {
                            $user["mailUser"] = 0;
                            $notnullusers["sysUser"] = 1;
                        }
                        array_push($users, $user);
                    }
                }
            } else {
            }

            if (isset($admins)) {
                $this->smarty->assign("admins", $admins);
                if (isset($notnulladmins)) {
                    $this->smarty->assign("notnulladmins", $notnulladmins);
                }
            }

            if (isset($users)) {
                $this->smarty->assign("nonadmins", $users);
                if (isset($notnullusers)) {
                    $this->smarty->assign("notnullnonadmins", $notnullusers);
                }
            }
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
