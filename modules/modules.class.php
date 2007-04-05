<?php
/**
 * module factory
 *
 * This class contains a factory to call modules. This makes
 * calling modules by index.php much easier.
 *
 * $Id:: $
 * $LastChangedBy:: $
 * $LastChangedDate:: $
 * $LastChangedRevision:: $
 *
 */

class modules {
    /**
     * factory-method
     *
     * @param string type
     * @return object class on success, error message on error
     *
     */
    function &factory($type = "main") {  
        if (!file_exists(APPROOT."/modules/content_${type}.php")) {   
            $type = "main";
        }
        $classname = "content_${type}";

        include(APPROOT."/modules/${classname}.php");

        if ( !class_exists($classname) ) {   
	    echo "error!";
        }
        @$obj =& new $classname;

        return $obj;
    }
}

/**
 * Content Module Base Class
 * 
 * This is the base class for all content modules
 * All modules should be inherited from this class!
 *
 * @author Rudolph Bott
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
        $this->ldap->connect();
        $this->ldap->binddn = $crypt->decrypt($_SESSION["ldap_binddn"]);
        $this->ldap->bindpw = $crypt->decrypt($_SESSION["ldap_bindpass"]);
        $this->ldap->bind($this->ldap->binddn,$this->ldap->bindpw);
	}

    /**
     * This method is called after the constructor by the main page
     */
    function proceed() {
    
	}

    /**
     * return errors (if any)
     */
	function getErrors() {
		return $errors;
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
?>
