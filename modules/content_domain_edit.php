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
                        if (isset($admins)) {
                            $this->ldap->addDomain($my_domain, $admins);
                        } else {
                            exit("Please specify an admin first"); // this needs to be redone
                        }
                        break;
                    case "modify": 
                        $this->ldap->modifyDomain($my_domain);

                        $ldapadmins = $this->ldap->listGroupusers($domain);
                        
                        $count = 0;


                        if (isset($admins)) {
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
                        }

                        if (isset($adminsadd)) {
                            $this->ldap->addGroupusers($domain, $adminsadd);
                        }
                        if (isset($adminsdel)) {
                            $this->ldap->delGroupusers($domain, $adminsdel);
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
            $users = $this->ldap->listSystemusers();

            $count=0;

            for ($i=0; $i < $users["count"]; $i++) {
                $tmp = explode(",", $users[$i]["dn"]);
                $tmp = explode("=", $tmp[0]);
                $tmp = $tmp[1];
                $nonadmins[$count] = $tmp;
                $nonadminslong[$count] = $users[$i]["dn"];
                $count++;
            }

            if (isset($nonadminslong)) {
            $this->smarty->assign("nonadmins",$nonadmins);
            $this->smarty->assign("nonadminslong",$nonadminslong);
            }
        } else {
            $this->smarty->assign("mode","modify");
            $this->smarty->assign("domain",$this->ldap->getDomain($domain));
            
            $admins = $this->ldap->listGroupusers($domain);
            $tmpusers = $this->ldap->listSystemusers("domain");
            $mailusers = $this->ldap->listUsers($domain);

            $users = array();

            foreach ($tmpusers as $tmpuser) {
                if ($tmpuser["dn"] != "") {
                    $user["dn"] = $tmpuser["dn"];
                    $user["cn"] = $tmpuser["cn"][0];
                    $user["sn"] = $tmpuser["sn"][0];
                    array_push ($users, $user);
                }
            }

            foreach ($mailusers as $mailuser) {
                if ($mailuser["dn"] != "") {
                    $user["dn"] = $mailuser["dn"];
                    $user["cn"] = $mailuser["cn"][0];
                    $user["sn"] = $mailuser["sn"][0];
                    array_push ($users, $user);
                }
            }

            $count=0;

            if (isset($admins[0])) {
                $admins[0]["cn"] = array();
                $admins[0]["sn"] = array();

                foreach ($users as $user) {
                    $isinarray = 0;
                    for ($c=0; $c < $admins[0]["member"]["count"]; $c++) {
                        if ($user["dn"] == $admins[0]["member"][$c])
                        {
                            $isinarray=1;
                            array_push($admins[0]["cn"], $user["cn"]);
                            array_push($admins[0]["sn"], $user["sn"]);
                        }
                    }
                    
                    if ($isinarray == 0) {
                        $tmp = explode(",", $user["dn"]);
                        $tmp = explode("=", $tmp[0]);
                        $tmp = $tmp[1];
                        $nonadmins[$count] = $tmp;
                        $nonadminslong[$count] = $user["dn"];
                        $nonadminscn[$count] = $user["cn"];
                        $nonadminssn[$count] = $user["sn"];
                        $count++;
                    }
                }

                for ($i=0; $i < $admins[0]["member"]["count"]; $i++) {
                    $tmp = explode(",", $admins[0]["member"][$i]);
                    $tmp = explode("=", $tmp[0]);
                    $tmp = $tmp[1];
                    $tmpadmins[$i] = $tmp;
                    $tmpadminslong[$i] = $admins[0]["member"][$i];
                    $tmpadminscn[$i] = $admins[0]["cn"][$i];
                    $tmpadminssn[$i] = $admins[0]["sn"][$i];
                }
            }

            if (isset($tmpadminslong)) {
                $this->smarty->assign("admins", $tmpadmins);
                $this->smarty->assign("adminslong", $tmpadminslong);
                $this->smarty->assign("adminscn", $tmpadminscn);
                $this->smarty->assign("adminssn", $tmpadminssn);
            }
            
            if (isset($nonadminslong)) {
                $this->smarty->assign("nonadmins",$nonadmins);
                $this->smarty->assign("nonadminslong",$nonadminslong);
                $this->smarty->assign("nonadminscn", $nonadminscn);
                $this->smarty->assign("nonadminssn", $nonadminssn);
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
