<?php


class Router {

  private $routes = array();
  private $resources = array();

  public function get() {
    return $this->routes;
  }

  public function set($resources, $routes) {
    $this->resources = $resources;
    $this->routes = $routes;
  }
    
  public function match($path) {

    $matchedAction = null;
    $matchedActions = array();

    $this->prepareRoutes();

    foreach ($this->routes as $route) {

      $matchedResult = new stdClass();
      $matchedResult->matched = true;

      if ($this->matchMethod($route['method'])) {
        $matchedResult = $this->matchRoute($route['route'], $path);
      }
      else {
        $matchedResult->matched = false;
      }

      if ($matchedResult->matched) {

        $actionRoute = new stdClass();
        $actionRoute->method = $_SERVER['REQUEST_METHOD'];
        $actionRoute->params = $matchedResult->params;
        $actionRoute->route = $route['route'];
        $actionRoute->controller = $route['controller'];
        $actionRoute->action = $route['action'];

        if (isset($route['langAlias'])) {
          $actionRoute->langAlias = $route['langAlias'];
        }

        array_push($matchedActions, $actionRoute);
      }
    }

    //if more than one action take one without parameters
    if (count($matchedActions) == 1) {

      $matchedAction = $matchedActions[0];
    }
    else if (count($matchedActions) > 0) {

      foreach ($matchedActions as $action) {

        if (count($action->params) == 0) {

          $matchedAction = $action;
        }
      }
      //if both have parameters take one that has no / in parameters           


      if (!isset($matchedAction)) {

        foreach ($matchedActions as $action) {

          if (count(explode('/', reset($action->params))) == 1) {

            $matchedAction = $action;
          }
        }
      }
    }

    return $matchedAction;
  }

  protected function matchMethod($methods) {

    if (strpos(strtolower($methods), strtolower($_SERVER['REQUEST_METHOD'])) !== false) {
      return true;
    }
    return false;
  }

  protected function matchRoute($route, $path) {

    $routeElements = explode('/', $route);
    $pathElements = explode('/', $path);

    $matched = true;
    $params = array();

    if (count($routeElements) > count($pathElements)) {
      $matched = false;
    }
    else if ($path === '' && $route !== '' && count($routeElements) > 0) {
      $matched = false;
    }
    else {

      for ($ii = 0; $ii < count($pathElements); $ii++) {

        if (isset($routeElements[$ii]) && $this->isRouteParameter($routeElements[$ii])) {

          if ($ii + 1 == count($routeElements) && count($routeElements) < count($pathElements)) {

            $params[$this->extractRouteParameterName($routeElements[$ii])] = $this->endPathParameter($pathElements, $ii);
            break;
          }
          else {

            $params[$this->extractRouteParameterName($routeElements[$ii])] = $pathElements[$ii];
          }
        }
        else {

          if (!isset($routeElements[$ii])) {
            $matched = false;
            break;
          }

          //if (isset($pathElements[$ii])) {
          if (strcmp($routeElements[$ii], $pathElements[$ii]) !== 0) {
            $matched = false;
            break;
          }
          //}
        }
      }
    }

    $result = new stdClass();
    $result->matched = $matched;
    $result->params = $params;

    return $result;
  }

  protected function isRouteParameter($string) {

    if (substr($string, 0, 1) === ':') {
      return true;
    }
    return false;
  }

  protected function extractRouteParameterName($string) {

    return str_replace(':', '', $string);
  }

  protected function endPathParameter($pathElements, $start) {

    $param = '';
    for ($ii = $start; $ii < count($pathElements); $ii++) {
      $param .= $pathElements[$ii] . '/';
    }
    $param = rtrim($param, '/');
    return $param;
  }

  //form routes from resource definitions and combine them with defined routes
  protected function prepareRoutes() {

    foreach ($this->resources as $resource) {

      $resourceType = $resource['type'];
      $resourceName = $resource['name'];
      $apiUrl = Conf::get('api') . '/';

      if ($resourceType == ResourceTypes::API) {
        $this->prepareRoute($apiUrl . $resourceName, $resourceName);
      }
      else if ($resourceType == ResourceTypes::Url) {
        $this->prepareRoute($resourceName, $resourceName);
      }
      else if ($resourceType == ResourceTypes::UrlAndAPI) {
        $this->prepareRoute($resourceName, $resourceName);
        $this->prepareRoute($apiUrl . $resourceName, $resourceName);
      }
    }
  }

  protected function prepareRoute($url, $resourceName) {

    $route = array('route' => $url, 'method' => 'get', 'controller' => $resourceName, 'action' => 'fetch');
    array_push($this->routes, $route);

    $route = array('route' => $url . '/:id', 'method' => 'get', 'controller' => $resourceName, 'action' => 'fetchOne');
    array_push($this->routes, $route);

    $route = array('route' => $url, 'method' => 'post', 'controller' => $resourceName, 'action' => 'insert');
    array_push($this->routes, $route);

    $route = array('route' => $url . '/:id', 'method' => 'put', 'controller' => $resourceName, 'action' => 'update');
    array_push($this->routes, $route);

    $route = array('route' => $url . '/:id', 'method' => 'delete', 'controller' => $resourceName, 'action' => 'delete');
    array_push($this->routes, $route);
  }
}

?>