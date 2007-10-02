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
require("includes/acl.inc.php");
require("includes/smarty.inc.php");
require("includes/gettext.inc.php");
require("includes/ldap_functions.inc.php");
require("includes/my_functions.inc.php");
require("includes/crypt.inc.php");
require("includes/eximfilter.inc.php");

if (isset($_POST["module"])) 
    $module = $_POST["module"];
else if (isset($_GET["module"])) 
    $module = $_GET["module"];
else $module = "main";

if (!isset($_SESSION["login"])) {
    session_destroy();
    if (isset($_GET["loginerror"])) $smarty->assign("loginerror",$_GET["loginerror"]);

    $languages = unserialize(AVAILABLE_LANGUAGES);
    $smarty->assign('language_ids', array_values($languages));
    $smarty->assign('language_names', array_keys($languages));
    $smarty->assign('default_language',DEFAULT_LANGUAGE);

    $smarty->display("header.tpl");
    $smarty->display("login.tpl");
    $smarty->display("footer.tpl");
} else {

    $acl = unserialize(ACL);

    // check if the userclass has access to the module
    if ( in_array($module,$acl[$_SESSION['userclass']]) ) {
        require('modules/modules.class.php');
        $content_module = &modules::factory($module);
        $content_module->smarty = $smarty;
        $content_module->proceed();
 
        $smarty->assign('username',$_SESSION['username']);
        $smarty->assign('userclass',$_SESSION['userclass']);
        $smarty->assign('acl',$acl[$_SESSION['userclass']]);
        $smarty->assign('get',$_GET);

        $content = $content_module->getContent();
    } else {
        $content = "no access";
    }

    $smarty->display("header.tpl");
    $smarty->display("banner.tpl");
    $smarty->display("navigation.tpl");
    echo $content;
    $smarty->display("footer.tpl");
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
