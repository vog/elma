<?php
/**
 * Content Module Main
 * 
 * This module displays the main page
 *
 * @author Rudolph Bott <rbott@megabit.net>
 * @author Daniel Weuthen <daniel@weuthen-net.de>
 * @package elma
 */

class content_main extends module_base
{
    /**
     * Constructor of this class
     */
	function content_main() {
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
	    $_content = $this->smarty->fetch('content_main.tpl');
   	    return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
