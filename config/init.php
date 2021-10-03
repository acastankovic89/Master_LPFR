<?php

    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      require_once(str_replace("config", "", __DIR__) . "core\class.config.php"); // Windows
    } else {
      require_once(str_replace("config", "", __DIR__) . "core/class.config.php");
    }

    // config file
    require_once('params/params.php');

    // system config
    require_once('params/sys/params.php');

    // init core autoloaders
    require_once Conf::get('root').'/config/autoloaders.php';


    /********* libraries *********/
        
    require Conf::get('root') . '/vendor/autoload.php';

    require Conf::get('root').'/core/libs/functions.php';

?>