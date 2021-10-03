<?php


class Encryption {

  public static function encode($string) {

    return hash(Conf::get('enc_type'), $string . Conf::get('enc_hash'));
  }

  public static function generateStamp() {

    return sha1(rand(1000, 9999) . time() . rand(1000, 9999));
  }

  public static function generateStampWithString($string, $length = null, $start = null) {

    if (@exists($length)) {

      $s = 0;
      if (@exists($start) && $start <= 40) $s = $start;

      return substr(sha1(mt_rand(10000, 99999) . time() . $string), $s, $length);
    }

    return sha1(mt_rand(10000, 99999) . time() . $string);
  }

}
?>