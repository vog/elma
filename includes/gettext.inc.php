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

if ( isset($_SESSION["language"]) ) {
    $set_language = $_SESSION["language"];
} else {
    $set_language = DEFAULT_LANGUAGE;	
}

setlocale(LC_ALL, $set_language);

// Set the text domain as 'messages'
$domain = 'messages';
bindtextdomain($domain, getcwd()."/templates/".TEMPLATE."/locale");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
