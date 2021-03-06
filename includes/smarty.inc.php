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

require(getcwd().'/vendor/smarty/libs/Smarty.class.php');
require(getcwd().'/vendor/smarty/libs/SmartyValidate.class.php');

$smarty = new Smarty;
$smarty->template_dir = getcwd().'/templates/'.TEMPLATE.'/';
$smarty->caching = false;
$smarty->php_handling = SMARTY_PHP_REMOVE;
$smarty->assign('template_path',"templates/".TEMPLATE);
$smarty->compile_dir = getcwd().'/var/templates_c';
$smarty->cache_dir = getcwd().'/var/cache';

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
