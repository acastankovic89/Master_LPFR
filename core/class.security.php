<?php


class Security {

    public static $purifier = null;
  
    private static $instance;
  
    private function __construct( ) {    
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifierConfig->set('Core.Encoding', 'UTF-8'); // replace with your encoding
        $purifierConfig->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
        self::$purifier = $purifier = new HTMLPurifier($purifierConfig);
    }  
  
    //singleton construct
    public static function Instance() {

        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    public function purifier () {
        return self::$purifier; 
    }

    public function purifyAll ($result) {
        foreach ($result as $key => $item) {
            $result[$key] = self::$purifier->purify($result[$key]);
        }
        return $result; 
    }

    public function purifyOne ($result) {
      return self::$purifier->purify($result);
    }
}
?>