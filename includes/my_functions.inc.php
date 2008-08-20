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
function my_print_r ( $data , $header="") {
    echo "<div align='left' style='border: 2px red solid; background-color: lightgray; padding: 10px; margin: 10px; font-size: 10'>";
    echo "<pre>";
    if ( strlen($header) > 0 ) echo "<h1>$header</h1>";
    print_r($data);
    echo "</pre>";
    echo "</div>";
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


function my_serialize(&$array,$position) { 
    $array = serialize($array); 
} 
  
function my_unserialize(&$array,$position) { 
    $array = unserialize($array); 
} 
 
function array_set_as_first($array,$element) {
    if(array_key_exists($element,$array)) {
        $extract[$element] = $array[$element];
        unset($array[$element]);

        $array = array_merge($extract,$array);

        return $array;
    } else {
        trigger_error("\$element not a key in \$array!",E_USER_WARNING);
        return $array;
    }
}

function my_generate_password() {
    return intval(rand(0,9)) . intval(rand(0,9)) . intval(rand(0,9)) . chr(intval(rand(0,26) + 65)) . chr(intval(rand(0,26) + 65)) . chr(intval(rand(0,26) + 65));
}

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
