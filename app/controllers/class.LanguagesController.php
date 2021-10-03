<?php


class LanguagesController extends Controller {

  private $languagesModel;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('languages');

    $languagesModel = Languages::Instance();
    if ($languagesModel instanceof Languages) {
      $this->languagesModel = $languagesModel;
    }

    Trans::initTranslations();
  }

  public function fetchOne() {

    $id = trim(Security::Instance()->purifier()->purify($this->params('id')));

    $data = $this->languagesModel->getOne($id);

    $this->view->respond($data);
    return $data;
  }

  public function fetchAll() {

    $data = $this->languagesModel->getAll();

    $this->view->respond($data);
    return $data;
  }

  public function fetchByAlias() {

    $alias = trim(Security::Instance()->purifier()->purify($this->params('alias')));

    $data = $this->languagesModel->getByAlias($alias);

    $this->view->respond($data);
    return $data;
  }

  public function setLanguage() {

    $id = trim(Security::Instance()->purifier()->purify($this->params('id')));

    $language = $this->languagesModel->getOne($id);

    Trans::setLanguage($language);

    $this->view->respond(null, null, Request::JSON_REQUEST);
  }

}
?>