<?php
/**
* Domain Module
*
* This module is used for domian maintenance, eg. list, add, etc.
*
* @author Daniel Weuthen
*
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
     *
     */
    function proceed() {
        $my_domains = array();

        $domains = $this->ldap->listDomains();
        for ( $i = 0; $i < $domains["count"]; $i++ ) {
            $domain['dc'] = $domains[$i]["dc"][0]; 
            $domain['mailstatus'] = $domains[$i]["mailstatus"][0];
            $domain['link'] = $_SERVER['PHP_SELF']."?module=domain_edit&domain=".$domain['dc'];       
            array_push($my_domains,$domain);
        }

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
?>
