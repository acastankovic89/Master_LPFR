<?php


  if (!function_exists('getallheaders')) {

    function getallheaders() {

      if (!is_array($_SERVER)) {
        return array();
      }

      $headers = array();
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }
      return $headers;
    }
  }


  function exists($data) {

    if (!isset($data)) return false;
    if (is_string($data) && $data === "") return false;
    if (is_object($data)) $data = (array)$data;
    if (is_array($data) && empty($data)) return false;
    return true;
  }

?>