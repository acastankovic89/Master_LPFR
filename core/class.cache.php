<?php


class Cache {

  static private $public = array();

  static function init() {

  }

  public static function get($key) {

    return isset(self::$public[$key]) ? self::$public[$key] : false;
  }

  public static function set($key, $value) {

    self::$public[$key] = $value;
  }

}
?>
