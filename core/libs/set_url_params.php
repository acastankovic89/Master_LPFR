<?php

   $protocol = 'http';
   if(isset($_SERVER['HTTPS'])) {
      if ($_SERVER['HTTPS'] == "on") {
         $protocol = 'https';
      }
   }

   $url = trim($protocol . '://' . $_SERVER['HTTP_HOST'], "/");
   $host = str_ireplace('www', '', parse_url($url, PHP_URL_HOST));
   $tld = strstr($host, '.');

   $base = Conf::get('base');
   if(isset($base) && (string) $base !== '') {
      $url .= '/' . $base;
   }

   Conf::set('url', $url);
   Conf::set('tld', $tld);
   Conf::set('admin_url', $url . '/admin');


   $sessionPrefix = Conf::get('session_prefix');

   if(isset($sessionPrefix) && (string) $sessionPrefix !== '') {
      Conf::set('session_prefix', 'normacore-' . $sessionPrefix);
   }


?>