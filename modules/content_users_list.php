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
 * Domain Module
 *
 * This module is used to create a list of users for the given domain.
 */

class content_users_list extends module_base {

    /**
     * Constructor of this class
     */
    function content_users_list() {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() {

        $domain = $_GET["domain"];
        $this->smarty->assign('domain',$domain);

        /**
         * prepare users array for smarty output
         */
        $my_users = array();
        $users = $this->ldap->listUsers($domain);
        for ($i = 0; $i < $users["count"]; $i++) {
            $user['uid'] = $users[$i]["uid"][0]; 
            $user['mailstatus'] = $users[$i]["mailstatus"][0];
            $user['deletelink'] = $_SERVER['PHP_SELF']."?module=user_delete&amp;domain=".$domain."&amp;user=".$user['uid']."&amp;mode=delete";
            $user['editlink'] = $_SERVER['PHP_SELF']."?module=user_edit&amp;domain=".$domain."&amp;user=".$user['uid']; 
            array_push($my_users,$user);
        }
        $this->smarty->assign("link_newuser",$_SERVER['PHP_SELF']."?module=user_edit&amp;domain=".$domain."&amp;user=new");
        $this->smarty->assign('users',$my_users);

        // prepare aliases array for smarty output
        $my_aliases = array();
        $aliases = $this->ldap->listAliases($domain);
        for ($i = 0; $i < $aliases["count"]; $i++) {
            $alias['uid'] = $aliases[$i]["uid"][0]; 
            $alias['mailaliasedname'] = $aliases[$i]["mailaliasedname"];
            $alias['deletelink'] = $_SERVER['PHP_SELF']."?module=alias_delete&amp;domain=".$domain."&amp;alias=".$alias['uid'];
            $alias['editlink'] = $_SERVER['PHP_SELF']."?module=alias_edit&amp;domain=".$domain."&amp;alias=".$alias['uid']; 
            array_push($my_aliases,$alias);
        }
        $this->smarty->assign("link_newalias",$_SERVER['PHP_SELF']."?module=alias_edit&amp;domain=".$domain."&amp;alias=new");
        $this->smarty->assign('aliases',$my_aliases);
    }

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() {
        $_content = $this->smarty->fetch('content_users_list.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
