<?php

class ArticlesController extends Controller {

  private $requiredFields = array('title');
  private $articlesModel;
  private $service;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('articles');

    $articlesModel = Articles::Instance();
    if ($articlesModel instanceof Articles) {
      $this->articlesModel = $articlesModel;
    }

    $service = ArticlesService::Instance();
    if ($service instanceof ArticlesService) {
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

    Trans::initTranslations();
  }


  /************************* CREATE, UPDATE, DELETE OPERATIONS  *************************/

  public function insertArticle() {

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

  public function updateArticle() {

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

  public function deleteArticle() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->articlesModel->delete($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }


  /************************* READ OPERATIONS  *************************/

  public function fetchWithFilters() {

    $params = $this->getPurifiedParams();

    $response = new stdClass();
    $response->total = $this->articlesModel->getTotal($params);
    $response->items = $this->articlesModel->getWithFilters($params);

    $this->view->respond($response);
    return $response;
  }

  public function fetchOne() {

    $params = $this->getPurifiedParams();

    if(!Languages::enabled()) {

      $item = $this->articlesModel->getOne($params['id'], $params['fetchWithUnpublished']);
    }
    else{

      $langId = $this->service->setLangIdParam($params);

      $item = $this->articlesModel->getOneByLangId($params['id'], $langId, $params['fetchWithUnpublished']);
    }

    $data = $this->service->setItem($item, $params, true);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchAll() {

    $params = $this->getPurifiedParams();

    if(!Languages::enabled()) {
      $items = $this->articlesModel->getAll($params['fetchWithUnpublished']);
    }
    else{

      $langId = $this->service->setLangIdParam($params);

      $items = $this->articlesModel->getAllByLangId($langId, $params['fetchWithUnpublished']);
    }

    $data = $this->service->setItems($items, $params);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchByParentId() {

    $params = $this->getPurifiedParams();
    $items = $this->articlesModel->getByParentId($params['parent_id'], $params['fetchWithUnpublished']);

    $data = $this->service->setItems($items, $params, true);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchByAlias() {

    $params = $this->getPurifiedParams();

    $item = $this->articlesModel->getByAlias($params['alias'], $params['fetchWithUnpublished']);

    $data = $this->service->setItem($item, $params, true);

    $this->view->respond($data);
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
      $this->service->setItemProperties($item, $params);
      $this->service->setItemComments($item, Conf::get('comment_type_id')['article']);
      $article = new Articles();
      $article->map($item);
      array_push($items, $article);
    }

    $response = new stdClass();
    $response->items = $items;
    $response->langGroupId = $langGroupId;

    $this->apiRespond($response);
    return $response;
  }

  public function fetchAllByLangId() {

    $params = $this->getPurifiedParams();

    $langId = $this->service->setLangIdParam($params);

    $items = $this->articlesModel->getAllByLangId($langId, $params['fetchWithUnpublished']);

    $data = $this->service->setItems($items, $params, true);

    $this->apiRespond($data);
    return $data;
  }

  public function fetchByLanguageGroupIdAndLanguageId() {

    $params = $this->getPurifiedParams();

    $langId = $this->service->setLangId($params);

    $item = $this->articlesModel->getByLanguageGroupIdAndLanguageId($params['lang_group_id'], $langId);

    $data = $this->service->setItem($item, $params, true);

    $this->view->respond($data);
    return $data;
  }

  ///////////////////////////////////////////////////////////////////////////////

  public function fetchAllAPI() {

    $params = $this->getPurifiedParams();

    if(@exists($params['limit'])) {
      $params['items_per_page'] = $params['limit'];
    }

    $params['order_by'] = '`a`.`event_date`';
    $params['order_direction'] = 'DESC';

    $whereColumns = null;
    if(@exists($params['category_id'])) {
      $whereColumns = array(
        array('columnAlias' => 'a', 'columnName' => 'category_id', 'value' => $params['category_id'], 'type' => 'int')
      );
    }

    $items = $this->articlesModel->getWithFilters($params, $whereColumns);

    $data = $this->service->setItems($items, $params);

    $response = new stdClass();
    $response->total = $this->articlesModel->getTotal($params, $whereColumns);
    $response->items = $data;

    $this->apiRespond($response);
    return $response;
  }

}
?>