<?php


// all traffic should be redirected to home.php

class Dispatcher {

  protected $path;
  private static $instance;


  //singleton construct    
  private function __construct() {

  }

  public static function instance() {

    if (!isset(self::$instance)) {
      $c = __CLASS__;
      self::$instance = new $c;
    }

    return self::$instance;
  }

  //force dispatch
  public function dispatch($controller, $action, $params, $respondType = null) {

    $appName = 'NormacoreApplication';

    if (class_exists($appName)) {
      $app = new $appName;
      $app->setAppCall(true);
      $app->init($controller, $action, $params, null);
      // force respond
      if (isset($respondType)) {
        $app->setRespondType($respondType);
      }
      $result = $app->runAction();
    }
    else {
      Logger::putError('Application not found: ' . $appName);
      die('Application ' . $appName . ' has not been defined.');
    }
    return $result;
  }


  public function dispatchUri() {

    $this->parseUri();
    $this->findAndRun();
  }

  protected function setPath($path) {
    $this->path = $path;
  }

  protected function parseUri() {

    $path = trim(parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/');

    if (Conf::get('base') !== '') {

      if (strpos($path, Conf::get('base')) === 0) {
        $path = substr($path, strlen(Conf::get('base')));
      }
    }
    $path = ltrim($path, '/');
    $path = rtrim($path, '/');

    $this->setPath($path);
  }

  protected function findAndRun() {

    $actionFound = false;
    $app = null;
    $rootApp = null;

     $appName = 'NormacoreApplication';

    if (class_exists($appName)) {
      $app = new $appName;
      $path = $this->path;

      $rootApp = $app;
      $actionFound = $app->findAndInitAction($path);
    }
    else {
      Logger::putError('Application not found: ' . $appName);
      die('Application ' . $appName . ' has not been defined.');
    }

    if (!$actionFound) {

      $actionFound = $rootApp->findAndInitDefaultAction($path);
      if (!$actionFound) {
        Logger::putError('Invalid route: ' . $this->path);
        die('Invalid route: ' . $this->path);
      }
      else $rootApp->runAction();
    }
    else {
      //TODO: UAC check here
      //TODO: set request type            
      $app->runAction();
    }
  }

}
?>