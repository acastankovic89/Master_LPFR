<?php


class Request {
   
  const JSON_REQUEST = 1;
  const HTML_REQUEST = 2;
  const APP_REQUEST = 3;
  const RETURN_REQUEST = 4;

  protected $respondType;
  protected $headers;
  protected $post;
  protected $get;
  protected $put;
  protected $delete;
  protected $params;
  protected $jsonParams;
  protected $appCall = false;
  protected $method;

  public function init($params) {

    $this->params = $params;

    if (!$this->appCall) {

      $this->headers = getallheaders();
      $this->method = strtolower($_SERVER['REQUEST_METHOD']);

      $acceptHeaders = null;
      if (isset($this->headers['Accept'])) {
        $acceptHeaders = $this->headers['Accept'];
      }
      else if (isset($this->headers['accept'])) {
        $acceptHeaders = $this->headers['accept'];
      }

      if (isset($acceptHeaders)) {

        switch ($acceptHeaders) {
          case 'application/json':
            $this->respondType = Request::JSON_REQUEST;
            break;
          default:
            $this->respondType = Request::HTML_REQUEST;
            break;
        }
      }
      else {
        // $this->respondType = Request::RETURN_REQUEST;
        $this->respondType = Request::HTML_REQUEST;
      }

      $content = file_get_contents('php://input');
      $this->post = $_POST;
      $this->get = $_GET;

      if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
        $this->delete = $this->parseRequestData($content);
      }
      else if (strtolower($_SERVER['REQUEST_METHOD']) == 'put') {
        $this->put = $this->parseRequestData($content);
      }

      if (@exists($this->headers['Content-Type']) && preg_match("/application\/json/i", $this->headers['Content-Type'])) {
        $this->jsonParams = json_decode($content, TRUE);
      }
    }
    else {
      $this->respondType = Request::APP_REQUEST;
    }
  }

  //we are expecting data in the following form data1=value1&data2=value2&...
  public function parseRequestData($dataString) {

    $result = array();
    if ($dataString === '') return $result;

    $dataArray = explode('&', $dataString);
    foreach ($dataArray as $data) {
      $dataPair = explode('=', $data);
      $result[$dataPair[0]] = urldecode($dataPair[1]);
    }

    return $result;
  }

  public function setRespondType($respondType) {
    $this->respondType = $respondType;
  }

  public function getRespondType() {
    return $this->respondType;
  }

  public function getHeaders() {
    return $this->headers;
  }

  public function __get($name) {
    if (is_callable(array($this, $m = "get_$name"))) {
      return $this->$m();
    }
    die("Doh $name not found.");
  }

  public function get_post() {
    return $this->post;
  }

  public function get_put() {
    return $this->put;
  }

  public function get_get() {
    return $this->get;
  }

  public function get_delete() {
    return $this->delete;
  }

  public function get_params() {

    $result = array();
    if (isset($this->get)) $result = $this->get;
    if (isset($this->post)) $result = array_merge($result, $this->post);
    if (isset($this->put)) $result = array_merge($result, $this->put);
    if (isset($this->delete)) $result = array_merge($result, $this->delete);
    if (isset($this->jsonParams)) $result = array_merge($result, $this->jsonParams);
    if (is_array($this->params)) $result = array_merge($this->params, $result);
    return $result;
  }

  public function setAppCall($appCall) {
    $this->appCall = $appCall;
  }

  public function getAppCall() {
    return $this->appCall;
  }

  public function getMethod() {
    return $this->method;
  }

}
?>