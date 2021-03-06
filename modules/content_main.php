<?php
/**
 * @author Daniel Weuthen <daniel@weuthen-net.de> and Rudolph Bott <rbott@megabit.net>
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
 * Content Module Main
 * 
 * This module displays the main page
 */

class content_main extends module_base
{
    /**
     * Constructor of this class
     */
    function content_main() {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() {
        $this->smarty->assign("userCountOverall", $this->ldap->userCount());
        $this->smarty->assign("aliasCountOverall", $this->ldap->aliasCount());
        $this->smarty->assign("domainCount", $this->ldap->domainCount());
        $this->smarty->assign("userCountActive", $this->ldap->userCount(null, "TRUE"));
        $this->smarty->assign("aliasCountActive", $this->ldap->aliasCount(null, "TRUE"));
        $this->smarty->assign("domainCountActive", $this->ldap->domainCount("TRUE"));
        
    }

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() {
        $_content = $this->smarty->fetch('content_main.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
