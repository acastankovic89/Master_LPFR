<?php

class GalleriesController extends Controller {

  private $requiredFields = array('name');
  private $galleriesModel;
  private $service;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('galleries');

    $galleriesModel = Galleries::Instance();
    if ($galleriesModel instanceof Galleries) {
      $this->galleriesModel = $galleriesModel;
    }

    $service = GalleriesService::Instance();
    if ($service instanceof GalleriesService) {
      $this->service = $service;
    }

    $scopes = array(
      CrudOperations::FETCH_ONE => null,
      CrudOperations::FETCH_ALL => null,
      CrudOperations::INSERT => Scopes::$ADMIN,
      CrudOperations::UPDATE => Scopes::$ADMIN,
      CrudOperations::DELETE => Scopes::$ADMIN,
      CrudOperations::FETCH_MINE => Scopes::$USER,
    );
    $this->setScopes($scopes);
  }

  /************************* CREATE, UPDATE, DELETE OPERATIONS  *************************/

  public function insertGallery() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->requiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $this->galleriesModel->insert($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is created'),
      'lastInsertId' => $this->galleriesModel->lastInsertId()
    ]);
  }

  public function updateGallery() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->requiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);
      return;
    }

    $this->galleriesModel->update($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is updated')
    ]);
  }

  public function deleteGallery() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->galleriesModel->delete($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }


  /************************* READ OPERATIONS  *************************/

  public function fetchWithFilters() {

    $params = Security::Instance()->purifyAll($this->params());

    $columns = array(
      array('columnName' => 'name')
    );

    $response = new stdClass();
    $response->total = $this->galleriesModel->getTotalItems($params, $columns);
    $response->items = $this->galleriesModel->getItemsWithFilters($params, $columns);

    $this->view->respond($response);
    return $response;
  }

  public function fetchOne() {

    $params = $this->getPurifiedParams();

    $item = $this->galleriesModel->getOne($params['id']);

    $this->apiRespond($item);
    return $item;
  }

  public function fetchAll() {

    $item = $this->galleriesModel->getAll();

    $this->apiRespond($item);
    return $item;
  }

}
?>