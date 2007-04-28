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
        $valid_domain_data = FALSE;
        if (validate_data($domain["dc"],"domain")) $valid_domain_data = TRUE;
        return $valid_domain_data;
    }

    /**
     * validate given data against regex
     */
    function validate_data($string,$object) {
        $valid_data = FALSE;
        switch ($object) {
            case "domain": if (preg_match("/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/",$string)) $valid_data = TRUE;
                break;
        }
    return $valid_data;
}

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
