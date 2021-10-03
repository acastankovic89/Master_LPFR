<?php


class NewsletterHelper extends Service {

  private $model;

  public function __construct() {

    $model = Newsletter::Instance();
    if ($model instanceof Newsletter) {
      $this->model = $model;
    }
  }


  /************************************ ACTIONS ************************************/

  public function validateSignup($params) {

    if (!@exists($params)) {
      return Errors::EMPTY_FIELDS;
    }

    if (!@exists($params['email'])) {
      return Errors::EMAIL_REQUIRED;
    }

    if (!Util::validateEmail($params['email'])) {
      return Errors::INVALID_EMAIL;
    }

    if($this->exists($params['email'])) {
      return Errors::EMAIL_EXISTS;
    }

    return Errors::OK;
  }

  private function exists($params) {

    $data = $this->model->getByEmail($params);

    return @exists($data) && (bool)$data !== false;
  }

  public function setDownloadData($data, $params = null) {

    $heading = array('Id', 'Email');
    $body = array();

    if (@exists($data)) {

      foreach ($data as $item) {


        $itemArray = array(
          'id' => $item->id,
          'email' => $item->email
        );

        array_push($body, $itemArray);
      }
    }

    return (object)array('body' => $body, 'heading' => $heading);
  }
}

?>