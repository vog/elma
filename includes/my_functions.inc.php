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

/**
 * wrapper around print_r function
 */
function my_print_r ( $data ) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

/**
 * function to output debug messages
 */
function my_print_debug ( $message ) {
    if ( DEBUG )
    {
        echo "DEBUG: $message<br>\n";
    }
}

/**
 * remove keys from array that match given pattern
 */
function remove_key_by_str($array,$pattern) {
  if( is_array($array) )
  {
    $keys = array_keys($array);
    foreach($keys as $key)
    {
      $length = strlen($pattern);
      if(substr($key,0,$length) == $pattern)
      {
        unset($array[$key]);
      }
    }
  }
  return $array;
}


/**
 * validate domain
 */
function validate_domain ($domain) {
    $valid_domain_data = array();
    if (! validate_data($domain["dc"],"dc")) $valid_domain_data["dc"] = FALSE;
    return $valid_domain_data;
}

/**
 * validate user
 */
function validate_user ($user) {
    $valid_user_data = array();
    if (! validate_data($user["uid"],"uid")) $valid_user_data["uid"] = FALSE;
    if (! validate_data($user["sn"],"sn")) $valid_user_data["sn"] = FALSE;
    if (! validate_data($user["cn"],"cn")) $valid_user_data["cn"] = FALSE;
    if (! validate_data($user["userpassword"],"password")) $valid_user_data["userpassword"] = FALSE;
    return $valid_user_data;
}

/**]
 * validate user
 */
function validate_alias ($alias) {
    $valid_alias_data = array();
    if (! validate_data($alias["uid"],"uid")) $valid_alias_data["uid"] = FALSE;
    for ($i = 0; $i < count($alias["mailaliasedname"]); $i++) {
        if (! validate_data(rtrim($alias["mailaliasedname"][$i]),"mailaliasedname")) $valid_alias_data["mailaliasedname"] = FALSE;
        if ((isset($valid_alias_data["mailaliasedname"])) && ($valid_alias_data["mailaliasedname"] == FALSE)) break;
    }
    return $valid_alias_data;
}

/**
 * validate given data against regex
 */
function validate_data($string,$object) {
    $valid_data = FALSE;
    switch ($object) {
        case "dc": if (preg_match("/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/",$string)) $valid_data = TRUE;
            break;
        case "uid": if (preg_match("/^[a-zA-Z0-9\-\.]{1,64}$/",$string)) $valid_data = TRUE;
            break;  
        case "sn": if (preg_match("/^[a-zA-Z0-9\-\.]{1,64}$/",$string)) $valid_data = TRUE;
            break;
        case "cn": if (preg_match("/^[a-zA-Z0-9\-\.]{1,64}$/",$string)) $valid_data = TRUE;
            break;
        case "password": if (!preg_match("/^$/",$string)) $valid_data = TRUE;
            break;
        case "mailaliasedname": if ((validate_data($string,"uid")) || (validate_data($string,"email"))) $valid_data = TRUE;
            break;
        case "email": if (preg_match("/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@(([0-9a-zA-Z])+([-\w]*[0-9a-zA-Z])*\.)+[a-zA-Z]{2,9})$/",$string)) $valid_data = TRUE;
            break;
    }
return $valid_data;
}

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
