<?php

function my_print_r ( $data ) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function my_print_debug ( $message ) {
    if ( DEBUG )
    {
        echo "DEBUG: $message<br>\n";
    }
}

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

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
