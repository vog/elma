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
 * This module is used for listing available domains
 */

class content_domains_list extends module_base {

    /**
     * Constructor of this class
     *
     */
    function content_domains_list() {
        parent::module_base();
    }


    /**
     * This method is called after the constructor by the main page
     */
    function proceed() {

        $my_domains = array();

        $domains = $this->ldap->listDomains();
        for ( $i = 0; $i < $domains["count"]; $i++ ) {
            $domain['dc'] = $domains[$i]["dc"][0]; 
            $domain['mailstatus'] = $domains[$i]["mailstatus"][0];
            $domain['userslink'] = $_SERVER['PHP_SELF']."?module=users_list&amp;domain=".$domain['dc'];
            $domain['deletelink'] = $_SERVER['PHP_SELF']."?module=domain_delete&amp;domain=".$domain['dc'];
            $domain['editlink'] = $_SERVER['PHP_SELF']."?module=domain_edit&amp;domain=".$domain['dc']; 
            array_push($my_domains,$domain);
        }
        $this->smarty->assign("link_newdomain",$_SERVER['PHP_SELF']."?module=domain_edit&amp;domain=new");
        $this->smarty->assign('domains',$my_domains);   
	}

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() {
        $_content = $this->smarty->fetch('content_domains_list.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
