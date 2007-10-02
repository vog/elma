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
 * content user delete
 * 
 * This content module is used to get a delete confirmation when deleting
 * a domain.
 */

class content_domain_delete extends module_base
{

    /**
     * Constructor of this class
     */
    function content_domain_delete() 
    {
        parent::module_base();
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() 
    {
        if ( isset($_POST["submit"]) ) {
            $domain =  $_POST["domain"];
            $this->ldap->deleteDomain($domain);
            $submit_status = ldap_errno($this->ldap->cid);
            if ($submit_status == "0") {
                $this->smarty->assign("submit_status",$submit_status);
                $this->smarty->assign("domain",$domain);
            } else {
                $this->smarty->assign("submit_status",ldap_err2str($submit_status));
            }
        } else {
            $domain =  $_GET["domain"];
            $this->smarty->assign("domain",$domain);
            $this->smarty->assign("submit_status",-1);
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
        $_content = $this->smarty->fetch('content_domain_delete.tpl');
        return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
