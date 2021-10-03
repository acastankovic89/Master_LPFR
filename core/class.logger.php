<?php


class Logger {

  // log error
  public static function putError($e) {

    if (is_a($e, 'Exception')) {

      file_put_contents(Conf::get('log'), "\n" . date('Y-m-d H:i:s') . ' - ' . $e->getCode() . ' - ' . $e->getFile() . ' - ' . $e->getLine() . ' - ' . $e->getMessage(), FILE_APPEND);
    }
    else {
      file_put_contents(Conf::get('log'), "\n" . date('Y-m-d H:i:s') . ' - ' . $e, FILE_APPEND);
    }

    if (Conf::get('display_error')) {
      echo $e->getMessage() . "<br/>";
      die('Debug error');
    }
  }
    
  //log any string
  public static function put($string) {

    file_put_contents(Conf::get('log'), "\n" . date('Y-m-d H:i:s') . ' - ' . $string, FILE_APPEND);
  }

  //log db queries ect.
  public static function putTrace($string) {

    file_put_contents(Conf::get('log_trace'), "\n" . date('Y-m-d H:i:s') . ' - ' . $string, FILE_APPEND);
  }


}

?>