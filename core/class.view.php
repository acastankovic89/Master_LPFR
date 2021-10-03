<?php


class View {

  protected static $instances;

  protected $tableDescription = array();
  protected $table;
  protected $controller;
  protected $inputForm = array();

  protected $content = '';

  protected $respondType;

  function __construct() {
  }

  public static function Instance() {

    $class = get_called_class();

    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class;
    }
    return self::$instances[$class];
  }

  public function setRespondType($type) {
    $this->respondType = $type;
  }

  public function setController($controller) {
    $this->controller = $controller;
  }

  public function initController($controller) {
    $this->setController($controller);
    $this->setRespondType($controller->getRequest()->getRespondType());
  }

  public function respond($data, $template = null, $respondType = null) {

    if (isset($respondType)) $this->respondType = $respondType;

    if ($this->respondType == Request::JSON_REQUEST) {

      header('Content-Type: application/json');

      if (@exists(Conf::get('access_control_allowed_origins')) && Conf::get('access_control_allowed_origins') != false) {

        $headers = getallheaders();

        if (@exists($headers['Origin'])) {

          if (in_array($headers['Origin'], Conf::get('access_control_allowed_origins'))) {

            header('Access-Control-Allow-Origin: ' . $headers['Origin']);
          }
        }
      }

      $responseData = new stdClass();
      $responseData = $data;
      echo json_encode($responseData);
    }
    else if ($this->respondType == Request::HTML_REQUEST) {
      $this->loadTemplate($template, $data);
      $this->displayContent();
    }
  }

  public function unauthorized() {
    if ($this->respondType == Request::JSON_REQUEST) header("HTTP/1.1 401 Unauthorized");
    else header("Location: " . Conf::get("url"));
    die;
  }

  public function respondError($error, $template = null, $data = null) {

    if ($this->respondType == Request::JSON_REQUEST) {
      header('Content-Type: application/json');
      $responseData = new stdClass();
      $responseData->success = false;
      $responseData->errror = $error;
      echo json_encode($responseData);
    }
    else if ($this->respondType == Request::HTML_REQUEST) {
      $this->loadTemplate($template, $data);
      $this->displayContent();
    }
  }

  //load and display template
  public function displayTemplate($template, $data = null) {

    $fileName = $this->getRootLocation() . '/app/' . $template;

    if (strpos(strtolower($fileName), 'admin') != false) {
      $fileName = str_replace('app/', "", $fileName);
    }

    include $fileName;
  }

  //loads template called by $controller into
  public function loadTemplate($template, $data = null) {

    if ($template == null) throw new Exception('Template not defined. ');


    $templateName = $this->getRootLocation() . '/app/' . $template;
    if (strpos(strtolower($templateName), 'admin') != false) {
      $templateName = str_replace('app/', '', $templateName);
    }

    try {
      ob_start();
      if (file_exists($templateName)) {
        require $templateName;
      }
      else {
        throw new Exception('Template does not exist: ' . $templateName);
      }
      $this->content = ob_get_clean();
    } catch (Exception $e) {
      Logger::putError($e);
    }
    return $this->content;
  }

  public function displayContent() {
    echo $this->content;
  }

  public function getContent() {
    return $this->content;
  }

  protected function getRootLocation() {
    return Conf::get('root');
  }

  //set description
  public function setTableDescription($description) {
    $this->tableDescription = $description;
  }

  //table name
  public function setTable($table) {
    $this->table = $table;
  }

  //insert form builder
  public function buildInsertForm() {

    foreach ($this->tableDescription as $key => $field) {

      if ($field['type'] == 'varchar') {

        $this->inputForm[$field['name']] = "<input type='text' name='" . $field['name'] . "' maxlength='" . $field['size'] . "'/>";

        return $this->inputForm[$field['name']];
      }
    }
  }

}
?>
