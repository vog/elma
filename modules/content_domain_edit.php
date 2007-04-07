<?php
/**
 * Content Domain Edit
 * 
 * This content module is used for creating the domain edit form.
 *
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 *
 */

class content_domain_edit extends module_base
{

	/**
     * Constructor of this class
     *
     */
	function content_template() 
	{

	}

    /**
     * This method is called after the constructor by the main page
     *
     */
	function proceed() 
	{
        if ( $_GET["domain"] == "new" ) {
        } else {
            $this->smarty->assign("domain",$_GET["domain"]);
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
		$_content = $this->smarty->fetch('content_domain_edit.tpl');
   	    return $_content;
  }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
