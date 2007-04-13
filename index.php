<?php
session_start();

require("includes/config.inc.php");
include("includes/smarty.inc.php");
require("includes/gettext.inc.php");
require("includes/ldap_functions.inc.php");
require("includes/my_functions.inc.php");
require("includes/crypt.inc.php");

if (isset($_POST["module"])) 
    $module = $_POST["module"];
else if (isset($_GET["module"])) 
    $module = $_GET["module"];
else $module = "";

if (!isset($_SESSION["login"])) {
    session_destroy();
    $smarty->display("header.tpl");
    $smarty->display("login.tpl");
    $smarty->display("footer.tpl");
} else {
    require('modules/modules.class.php');
    $content_module = &modules::factory($module);
    $content_module->smarty = $smarty;
    $content_module->proceed();

    $smarty->assign('username',$_SESSION['username']);

    $content = $content_module->getContent();

    $smarty->display("header.tpl");
    $smarty->display("navigation.tpl");
    echo $content;
    $smarty->display("footer.tpl");
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
