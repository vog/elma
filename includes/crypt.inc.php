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
 * class for easy text encryption and decryption my mycrypt
 */

class mycrypt {

    /**
     * the class constructor
     */

    function mycrypt () {
        $this->key = MYCRYPT_KEY;
        $this->cipher = MCRYPT_RIJNDAEL_256;
        $this->mode = MCRYPT_MODE_ECB;
        $iv_size = mcrypt_get_iv_size($this->cipher, $this->mode);
        $this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    }
 
    /**
     * encryption function
     */
    function encrypt ($text) {
        $return_text = mcrypt_encrypt($this->cipher, $this->key, $text, $this->mode, $this->iv);
        return $return_text;
    }
    
    /**
     * decryption function
     */
    function decrypt ($text) {
        $return_text_padded = mcrypt_decrypt($this->cipher, $this->key, $text, $this->mode, $this->iv);
        $return_text = rtrim($return_text_padded, "\x00");
        return $return_text;
    } 
}
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
