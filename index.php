<?php
/**
 * @author Daniel Weuthen <daniel@weutnen-net.de>
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
