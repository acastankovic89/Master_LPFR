<?php

abstract class ResourceTypes {
  const Url = 0;
  const API = 1;
  const UrlAndAPI = 2;
}

class Application {

  protected $controller;
  protected $action;
  protected $params;
  protected $request;
  protected $method;
  protected $name = '';
  protected $routes = array();
  protected $resources = array();
  protected $default;
  protected $appCall = false;
  protected $langAlias;

  public function init($controller, $action, $params, $method, $langAlias = null) {

    $this->setController($controller);
    $this->setAction($action);
    $this->setParams($params);
    $this->setMethod($method);
    if (isset($langAlias)) {
      $this->setLangAlias($langAlias);
    }
    $this->initRequest();

  }

  public function runAction() {

    $controller = new $this->controller;
    $action = $this->action;
    $controller->setUrlParams($this->params);
    $controller->setRequest($this->request);
    $controller->setLangAlias($this->langAlias);
    $controller->setRoutes($this->routes);
    if (Conf::get('debug')) Logger::putTrace($this->controller . ': ' . $action);
    $result = $controller->run($action);

    return $result;
  }

  public function findAndInitAction($path) {

    $path = rtrim($path, '/');

    $router = new Router ();
    $router->set($this->resources, $this->routes);
    $actionRoute = $router->match($path);

    if (isset($actionRoute)) {
      $langAlias = isset($actionRoute->langAlias) ? $actionRoute->langAlias : null;
      $this->init($actionRoute->controller, $actionRoute->action, $actionRoute->params, $actionRoute->method, $langAlias);
    }

    return isset($actionRoute);
  }

  public function findAndInitDefaultAction($path) {

    $path = rtrim($path, '/');

    $router = new Router ();
    $router->set($this->resources, array($this->default));
    $actionRoute = $router->match($path);

    if (isset($actionRoute)) {
      $langAlias = isset($actionRoute->langAlias) ? $actionRoute->langAlias : null;
      $this->init($actionRoute->controller, $actionRoute->action, $actionRoute->params, $actionRoute->method, $langAlias);
    }

    return isset($actionRoute);
  }

  public function setController($controller) {

    $controller = ucfirst($controller) . 'Controller';

    if (!class_exists($controller)) {
      Logger::putError('Controller not found: ' . $controller);
      die('Controller' . $controller . ' has not been defined.');
    }
    $this->controller = $controller;
  }

  public function setAction($action) {
    $reflector = new ReflectionClass($this->controller);
    if (!$reflector->hasMethod($action)) {
      Logger::putError('Action not found: ' . $action);
      die('Action ' . $action . ' has been not defined.');
    }
    $this->action = $action;
  }

  public function setParams($params) {
    $this->params = $params;
  }

  public function setLangAlias($langAlias) {
    $this->langAlias = $langAlias;
  }

  public function initRequest() {

    $request = new Request();
    $request->setAppCall($this->appCall);
    $request->init($this->params);

    $this->request = $request;
  }

  public function setRespondType($respondType) {
    $this->request->setRespondType($respondType);
  }

  public function setMethod($method) {
    $this->method = $method;
  }

  public function getMethod() {
    return $this->method;
  }

  public function getName() {
    return $this->name;
  }

  public function setAppCall($appCall) {
    $this->appCall = $appCall;
  }

}
?>