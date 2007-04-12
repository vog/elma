<?php

class mycrypt {

    function mycrypt () {
	$this->key = MYCRYPT_KEY;
	$this->cipher = MCRYPT_RIJNDAEL_256;
        $this->mode = MCRYPT_MODE_ECB;
	$iv_size = mcrypt_get_iv_size($this->cipher, $this->mode);
	$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    }

    function encrypt ($text) {
        $return_text = mcrypt_encrypt($this->cipher, $this->key, $text, $this->mode, $this->iv);
        return($return_text);
    }

    function decrypt ($text) {
	$return_text =  mcrypt_decrypt($this->cipher, $this->key, $text, $this->mode, $this->iv);
        return($return_text);
    } 
}
?>
