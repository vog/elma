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

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
