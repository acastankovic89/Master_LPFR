<?php


class MediaController extends Controller {

  private $mediaModel;
  private $service;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('media');

    $mediaModel = Media::Instance();
    if ($mediaModel instanceof Media) {
      $this->mediaModel = $mediaModel;
    }

    $service = MediaService::Instance();
    if ($service instanceof MediaService) {
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

  public function fetchWithFilters() {

    $params = Security::Instance()->purifyAll($this->params());

    $columns = array(
      array('columnName' => 'name'),
      array('columnName' => 'file_name'),
      array('columnName' => 'mime')
    );

    $response = new stdClass();
    $response->total = $this->mediaModel->getTotalItems($params, $columns);
    $response->items = $this->mediaModel->getItemsWithFilters($params, $columns);

    $this->view->respond($response);
    return $response;
  }

  public function uploadMedia() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $data = $this->service->upload();

    $this->view->respond($data);
    return $data;
  }

  public function deleteMedia() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = Security::Instance()->purifyAll($this->params());

    $this->service->delete($params['id']);

    $this->view->respond((object)[
      'status' => MediaErrors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }

}

?>