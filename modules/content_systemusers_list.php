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
 * Content Module Systmusers
 * 
 * This content module is used to list system users
 */

class content_systemusers_list extends module_base
{

    /**
     * Constructor of this class
     */
    function content_systemusers_list() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        $my_users = array();

        $users = $this->ldap->listSystemusers();

        for ($i=0; $i < $users["count"]; $i++) {

            $user['uid'] = $users[$i]["uid"][0];

            $user['lname'] = $users[$i]['cn'][0];
            $user['fname'] = $users[$i]['sn'][0];

            $user['deletelink'] = $_SERVER['PHP_SELF']."?module=systemuser_delete&amp;user=".$user['uid'];
            $user['editlink'] = $_SERVER['PHP_SELF']."?module=systemuser_edit&amp;user=".$user['uid']; 
            array_push($my_users,$user);
        }

        $this->smarty->assign("link_newsystemuser",$_SERVER['PHP_SELF']."?module=systemuser_edit&amp;user=new");
        $this->smarty->assign('systemusers',$my_users);   
    }


    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() 
    {
        $_content = $this->smarty->fetch('content_systemusers_list.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
