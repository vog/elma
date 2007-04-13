<?php

session_start();
session_destroy();
Header('Location: index.php');
exit;

// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
