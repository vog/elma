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
        $this->smarty->assign("mailstorageservers",unserialize(MAILSTORAGESERVERS));

        // existing domain altert 
        if (isset($_POST["submit"])) {

            SmartyValidate::connect($this->smarty);

            if(SmartyValidate::is_valid($_POST)) {

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
                
                if (isset($_POST["mailstatus"])) {
                    $my_domain["mailstatus"] = "TRUE";
                } else {
                    $my_domain["mailstatus"] = "FALSE";
		}

                $eximFilterValues["maildomainalias"]["values"] = array( "STATUS" => "",
                                                              "TARGETDOMAIN" => $_POST["nlo_maildomainaliastarget"]);
                if (! isset($_POST["nlo_maildomainalias"])) {
                    $eximFilterValues["maildomainalias"]["values"]["STATUS"] = "#";
                }

		#FIXME!!
		if(($eximFilterValues["maildomainalias"]["values"]["STATUS"] == "") && ($eximFilterValues["maildomainalias"]["values"]["TARGETDOMAIN"] == "")) {
		    die("Please set a Targetdomain when enabling Domainaliasing!");
		}

                $my_domain["mailSieveFilter"] = createEximFilter( $eximFilterValues );

                $this->ldap->modifyDomain($my_domain);

                $admins_cur = $this->ldap->listAdminUsers($domain, TRUE);

                if (!isset($admins)) {
                    $admins = array();
                    //array_push ($admins, LDAP_ADMIN_DN);
                }

                /* create array of new admins */
                $adminsadd = array_values(array_diff($admins,$admins_cur));
                
                /* create array of removed admins */
                $adminsdel = array_values(array_diff($admins_cur,$admins));

                if ( count($adminsadd) > 0 ) {
                    $this->ldap->addAdminUsers($domain, $adminsadd);
                }
                if ( count($adminsdel) > 0 ) {
                    $this->ldap->deleteAdminUsers($domain, $adminsdel);
                }
                $submit_status = ldap_errno($this->ldap->cid);
                if ($submit_status == "0") {
                    $this->smarty->assign("submit_status",$submit_status);
                    $domain = $my_domain["dc"];
                } else {  // LDAP error occured
                    $this->smarty->assign("submit_status",ldap_err2str($submit_status));
                }
            } else { // input validation failed
                $this->smarty->assign($_POST);
            }
        } else { // form has not yet been submitted
            $this->smarty->assign("submit_status",-1);
            SmartyValidate::connect($this->smarty, true);
            SmartyValidate::register_validator('dc', 'dc', 'notEmpty');
        }    
        
        $my_domain = $this->ldap->getDomain($domain);
        $this->smarty->assign("domain",$my_domain);
        
        /* create a users/admin array from system- and mailusers 
         * and unset the count key from users/admins array
         * to have useful arrays for the smarty output.
         * serializing is neccessary to diff the multidimensional
         * arrays.
         */

        $systemusers = $this->ldap->listSystemUsers();
        if ( count($systemusers) == 0 ) $systemusers = array();
        unset($systemusers["count"]);

        $mailusers = $this->ldap->listUsers($domain);
        if ( count($mailusers) == 0 ) $mailusers = array();
        unset($mailusers["count"]);
        
        $nonadmins = array_merge($systemusers,$mailusers);
        if ( count($nonadmins) == 0 ) $nonadmins = array();
        unset($nonadmins["count"]);

        $admins = $this->ldap->listAdminUsers($domain);
        if ( count($admins) == 0 ) $admins = array();
        unset($admins["count"]);

        array_walk($nonadmins,'my_serialize');
        array_walk($admins,'my_serialize');
        
        $nonadmins = array_values(array_diff($nonadmins,$admins));

        array_walk($admins,'my_unserialize');
        array_walk($nonadmins,'my_unserialize');

        if (isset($admins)) {
            $this->smarty->assign("admins", $admins);
        }

        if (isset($nonadmins)) {
            $this->smarty->assign("nonadmins", $nonadmins);
        }
  
        /* parse Exim filter and assign values */
        $eximFilterValues = parseEximFilter($my_domain["mailsievefilter"][0]);
        $this->smarty->assign("spamfiltersettings",$eximFilterValues["spamfilter"]["values"]);
	$this->smarty->assign("maildomainaliassettings",$eximFilterValues["maildomainalias"]["values"]);

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
