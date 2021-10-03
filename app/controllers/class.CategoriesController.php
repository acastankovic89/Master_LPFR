<?php

class CategoriesController extends Controller {

  private $requiredFields = array('name');
  private $categoriesModel;
  private $service;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('categories');

    $categoriesModel = Categories::Instance();
    if ($categoriesModel instanceof Categories) {
      $this->categoriesModel = $categoriesModel;
    }

    $service = CategoriesService::Instance();
    if ($service instanceof CategoriesService) {
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

  public function insertCategory() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->params();
    foreach ($params as $key => $item) {
      if($key !== 'content') {
        $result[$key] = Security::Instance()->purifyOne($params[$key]);
      }
    }

    if (!Helper::validateRequiredFields($params, $this->requiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $id = $this->service->insert($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is created'),
      'lastInsertId' => $id
    ]);
  }

  public function updateCategory() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->params();
    foreach ($params as $key => $item) {
      if($key !== 'content') {
        $result[$key] = Security::Instance()->purifyOne($params[$key]);
      }
    }

    if (!Helper::validateRequiredFields($params, $this->requiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);
      return;
    }

    $this->service->update($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is updated')
    ]);
  }

  public function deleteCategory() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->categoriesModel->delete($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }


  /************************* READ OPERATIONS  *************************/

  public function fetchWithFilters() {

    $params = $this->getPurifiedParams();

    $response = new stdClass();
    $response->total = $this->categoriesModel->getTotal($params);
    $response->items = $this->categoriesModel->getWithFilters($params);

    $this->view->respond($response);
    return $response;
  }

  public function fetchOne() {

    $params = $this->getPurifiedParams();
    $item = $this->categoriesModel->getOne($params['id'], $params['fetchWithUnpublished']);

    $data = $this->service->setItem($item, $params, true);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchAll() {

    $params = $this->getPurifiedParams();
    $items = $this->categoriesModel->getAll($params['fetchWithUnpublished']);

    $data = $this->service->setItems($items, $params);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchByParentId() {

    $params = $this->getPurifiedParams();
    $items = $this->categoriesModel->getByParentId($params['parent_id'], $params['fetchWithUnpublished']);

    $data = $this->service->setItems($items, $params);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchAllByLangId() {

    $params = $this->getPurifiedParams();
    $items = $this->categoriesModel->getAllByLangId($params['lang_id'], $params['fetchWithUnpublished']);

    $data = $this->service->setItems($items, $params);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchOneGroup() {

    $params = $this->getPurifiedParams();

    $langGroupItems = $this->service->getGroup($params['id'], $params['fetchWithUnpublished']);

    $langGroupId = null;
    $items = array();

    foreach ($langGroupItems as $item) {
      if($item->id !== 0) {
        $langGroupId = $this->service->setLanguageGroupId($item);
      }
      $this->service->setItemProperties($item);
      $category = new Categories();
      $category->map($item);
      array_push($items, $category);
    }

    $response = new stdClass();
    $response->items = $items;
    $response->langGroupId = $langGroupId;

    $this->apiRespond($response);
    return $response;
  }

  public function fetchByLanguageGroupIdAndLanguageId() {

    $params = $this->getPurifiedParams();

    $langId = $this->service->setLangId($params);

    $item = $this->categoriesModel->getByLanguageGroupIdAndLanguageId($params['lang_group_id'], $langId);

    $data = $this->service->setItem($item, $params, true);

    $this->view->respond($data);
    return $data;
  }
}
?>