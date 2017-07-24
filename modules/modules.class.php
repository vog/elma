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
 * module factory
 *
 * This class contains a factory to call modules. This makes
 * calling modules by main.inc.php much easier.
 */

class modules {

    /**
     * factory-method
     *
     * @param string type
     * @return object class on success, error message on error
     */
    function &factory($type = "main") {  
        if (!file_exists(dirname(__FILE__)."/content_${type}.php")) {
            $type = "main";
        }
        $classname = "content_${type}";

        include(dirname(__FILE__)."/${classname}.php");

        if (!class_exists($classname)) {   
            echo "error!";
        }
        @$obj = new $classname;

        return $obj;
    }
}

/**
 * Content Module Base Class
 * 
 * This is the base class for all content modules
 * All modules should be inherited from this class!
 *
 *
 */

class module_base {
    var $smarty;
    var $ldap;
    var $output = "";
    var $errors = "";

    /**
     * Constructor of this class
     */
    function module_base() {
        $crypt = new mycrypt();
        $this->ldap = new ELMA(LDAP_HOSTNAME);
        
        if (! $this->ldap->connect()) {
           echo $this->ldap->last_error();
        }
        
        $this->ldap->binddn = $crypt->decrypt($_SESSION["ldap_binddn"]);
        $this->ldap->bindpw = $crypt->decrypt($_SESSION["ldap_bindpass"]);
        if (! $this->ldap->bind()) {
           echo $this->ldap->last_error();
        }
    }

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() {
    
    }

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
     * @return string
     */
    function getContent() {
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
