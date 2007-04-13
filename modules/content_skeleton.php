<?php
/**
* Content Module Template
* 
* Use this content module as a template for new modules
*
* @author John Doe
*
*/

class content_template extends module_base
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

    }

    /**
     * construct and return menu for page template
     *
     */

    function getMenu() 
    {
        $menu[]['link'] = $_SERVER['PHP_SELF'] . '?module=template';
        $menu[count($menu)-1]['title'] = 'Template';
        return $menu;
    }

    /**
     * This method returns any content that should be echoed by the
     * main page.
     *
   * @return string
   */
    function getContent() 
    {
        $_content = $this->smarty->fetch('content_template.tpl');
       return $_content;
    }
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
