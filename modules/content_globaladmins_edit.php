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
 * content globaladmins edit
 * 
 * This content module is used for adding systemusers to the global admingroup.
 * 
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

            unset($my_domain["submit"]);
            
            $admins_cur = $this->ldap->listAdminUsers(null,"TRUE");
            
            if (!isset($admins)) {
                $admins = array();
            }

            /* create array of new admins */
            $adminsadd = array_values(array_diff($admins,$admins_cur));

            /* create array of removed admins */
            $adminsdel = array_values(array_diff($admins_cur,$admins));

            /* add admins to ldap if neccesary */
            if ( count($adminsadd) > 0 ) {
                $this->ldap->addAdminUsers(null, $adminsadd);
            }
            
            /* delete admins from ldap if neccesary */
            if ( count($adminsdel) > 0 ) {
                $this->ldap->deleteAdminUsers(null, $adminsdel);
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


        $systemusers = $this->ldap->listSystemUsers();
        if ( count($systemusers) == 0 ) $systemusers = array();
        unset($systemusers["count"]);

        $admins = $this->ldap->listAdminUsers();
        if ( count($admins) == 0 ) $admins = array();
        unset($admins["count"]);

        array_walk($systemusers,'my_serialize');
        array_walk($admins,'my_serialize');

        $nonadmins = array_values(array_diff($systemusers,$admins));

        array_walk($admins,'my_unserialize');
        array_walk($nonadmins,'my_unserialize');

        if (isset($admins)) {
            $this->smarty->assign("admins", $admins);
        }

        if (isset($nonadmins)) {
            $this->smarty->assign("nonadmins", $nonadmins);
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
