<?php
require("/usr/share/php/smarty/libs/Smarty.class.php");

$smarty = new Smarty;
$smarty->template_dir = getcwd().'/templates/'.TEMPLATE.'/';
$smarty->caching = false;
$smarty->php_handling = SMARTY_PHP_REMOVE;
$smarty->assign('_SELF',$_SERVER['PHP_SELF']);

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
