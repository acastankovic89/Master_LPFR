<?php

class Helper {

  public function __construct() {
  }


  public static function validateRequiredFields($params, $requiredFields) {

    if (!is_array($params)) $params = (array)$params;

    $sameNumberOfParams = true;
    foreach ($requiredFields as $key) {
      if (!array_key_exists($key, $params)) {
        $sameNumberOfParams = false;
      }
    }

    if (!$sameNumberOfParams) return false;


    $validated = true;
    foreach ($params as $key => $value) {

      foreach ($requiredFields as $field) {

        if ($field == $key) {

          if ($value == '') {

            $validated = false;
          }
        }
      }
    }

    return $validated;
  }

}
?>