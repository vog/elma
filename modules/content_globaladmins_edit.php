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
 * content domain edit
 * 
 * This content module is used for creating the domain edit form and 
 * handling the submited data.
 */

class content_globaladmins_edit extends module_base
{

    /**
     * Constructor of this class
     */
    function content_globaladmins_edit() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        // working on the admingroup 
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
            
            $ldapadmins = $this->ldap->listGroupusers();
            
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
                $this->ldap->addGroupusers(null, $adminsadd);
            }
            if (isset($adminsdel)) {
                $this->ldap->delGroupusers(null, $adminsdel);
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

        $this->smarty->assign("mode","modify");
        
        $admins = $this->ldap->listGroupusers();
        $users = $this->ldap->listSystemusers();

        $count=0;

        for ($i=0; $i < $users["count"]; $i++) {
            $isinarray = 0;
            for ($c=0; $c < $admins[0]["member"]["count"]; $c++) {
                if ($users[$i]["dn"] == $admins[0]["member"][$c])
                {
                    $isinarray=1;
                }
            }
            
            if ($isinarray == 0) {
                $tmp = explode(",", $users[$i]["dn"]);
                $tmp = explode("=", $tmp[0]);
                $tmp = $tmp[1];
                $nonadmins[$count] = $tmp;
                $nonadminslong[$count] = $users[$i]["dn"];
                $count++;
            }
        }

        for ($i=0; $i < $admins[0]["member"]["count"]; $i++) {
            $tmp = explode(",", $admins[0]["member"][$i]);
            $tmp = explode("=", $tmp[0]);
            $tmp = $tmp[1];
            $tmpadmins[$i] = $tmp;
            $tmpadminslong[$i] = $admins[0]["member"][$i];
        }
        
        if (isset($tmpadminslong)) {
            $this->smarty->assign("admins", $tmpadmins);
            $this->smarty->assign("adminslong", $tmpadminslong);
        }
        
        if (isset($nonadminslong)) {
        $this->smarty->assign("nonadmins",$nonadmins);
        $this->smarty->assign("nonadminslong",$nonadminslong);
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
        $_content = $this->smarty->fetch('content_globaladmins_edit.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
