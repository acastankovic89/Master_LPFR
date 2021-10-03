<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once('config/init.php');
Dispatcher::instance()->dispatchUri();
?>
