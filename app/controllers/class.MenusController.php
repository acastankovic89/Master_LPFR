<?php


class MenusController extends Controller {

  private $menuRequiredFields = array('name');
  private $menuItemsRequiredFields = array('name', 'type');
  private $service;
  private $menusModel;
  private $menuItemsModel;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('menus');

    $service = MenusService::Instance();
    if ($service instanceof MenusService) {
      $this->service = $service;
    }

    $menusModel = Menus::Instance();
    if ($menusModel instanceof Menus) {
      $this->menusModel = $menusModel;
    }

    $menuItemsModel = MenuItems::Instance();
    if ($menuItemsModel instanceof MenuItems) {
      $this->menuItemsModel = $menuItemsModel;
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


  /*** menus ***/

  public function insertMenu() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->menuRequiredFields)) {

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


  public function updateMenu() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->menuRequiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $this->menusModel->update($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is created')
    ]);
  }

  public function deleteMenu() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->menusModel->delete($params['id']);
    $this->menuItemsModel->resetMenuId($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted'),
    ]);
  }


  /*** menu items ***/

  public function insertMenuItem() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->menuItemsRequiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $params = $this->service->setValues($params);
    $this->menuItemsModel->insert($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is created')
    ]);
  }


  public function updateMenuItem() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->menuItemsRequiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $params = $this->service->setValues($params);
    $this->menuItemsModel->update($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is updated')
    ]);
  }


  public function deleteMenuItem() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->menuItemsModel->delete($params['id']);
    $this->menuItemsModel->resetParentId($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }

  public function updateItemsPosition() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->menuItemsModel->updateParentId($params['id'], $params['parent_id']);
    $this->menuItemsModel->updatePositions($params['id'], $params['position']);

    $item = $this->menuItemsModel->getOne($params['id']);

    $this->view->respond($item);
    return $item;
  }

  /************************* READ OPERATIONS  *************************/

  public function fetchWithFilters() {

    $params = $this->getPurifiedParams();

    $response = new stdClass();
    $response->total = $this->menusModel->getTotal($params);
    $response->items = $this->menusModel->getWithFilters($params);

    $this->view->respond($response);
    return $response;
  }

  public function fetchOne() {

    $params = $this->getPurifiedParams();

    if(Languages::enabled() && @exists($params['lang_id'])) {

      $item = $this->menusModel->getByLangGroupIdAndLangId($params['id'], $params['lang_id']);

    } else {

      $item = $this->menusModel->getOne($params['id']);
    }

    $this->apiRespond($item);
    return $item;
  }

  public function fetchOneWithItems() {

    $params = $this->getPurifiedParams();

    $menu = Dispatcher::instance()->dispatch('menus', 'fetchOne', array('id' => $params['id'], 'lang_id' => $params['lang_id']));

    $allItems = $this->menuItemsModel->getByMenuId($menu->id);

    if(!@exists($params['menu_id'])) {
      $params['menu_id'] = $params['id'];
    }

    $itemsForTable = new stdClass();
    $itemsForTable->total = $this->menuItemsModel->getTotal($params);
    $itemsForTable->items = $this->menuItemsModel->getWithFilters($params);

    $categories = Dispatcher::instance()->dispatch('categories', 'fetchAllByLangId', array('lang_id' => $menu->lang_id));
    $articles = Dispatcher::instance()->dispatch('articles', 'fetchAllByLangId', array('lang_id' => $menu->lang_id));

    if(@exists($itemsForTable->items) && (bool)$itemsForTable->items !== false) {

      foreach ($itemsForTable->items as $item) {
        $item->target = $this->loadTarget($item, $categories, $articles);
      }

      foreach ($allItems as $aItem) {
        $aItem->target = $this->loadTarget($aItem, $categories, $articles);
      }
    }

    $response = new stdClass();
    $response->menu = $menu;
    $response->allItems = $allItems;
    $response->itemsTree = Util::formTree($allItems);;
    $response->itemsForTable = $itemsForTable;
    $response->categories = $categories;
    $response->articles = $articles;

    $this->apiRespond($response);
    return $response;
  }

  public function fetchGroup() {

    $params = $this->getPurifiedParams();

    $langGroupItems = $this->service->getGroup($params['id']);

    $langGroupId = null;
    $items = array();

    foreach ($langGroupItems as $item) {
      if($item->id !== 0) {
        $langGroupId = $this->service->setLanguageGroupId($item);
      }
      array_push($items, $item);
    }

    $response = new stdClass();
    $response->items = $items;
    $response->langGroupId = $langGroupId;

    $this->apiRespond($response);
    return $response;
  }

  public function fetchItemsByMenuId() {

    $params = $this->getPurifiedParams();
    $items = $this->menuItemsModel->getByMenuId($params['menu_id']);

    $this->apiRespond($items);
    return $items;
  }

  public function fetchTree() {

    $params = $this->getPurifiedParams();
    $items = $this->menuItemsModel->getByMenuIdAndLangId($params['menu_id'], $params['lang_id']);

    $categories = Dispatcher::instance()->dispatch('categories', 'fetchAllByLangId', array('lang_id' => $params['lang_id']));
    $articles = Dispatcher::instance()->dispatch('articles', 'fetchAllByLangId', array('lang_id' => $params['lang_id']));

    foreach ($items as $item) {
      $item->target = $this->loadTarget($item, $categories, $articles);
    }

    $data = Util::formTree($items);

    $this->apiRespond($data);
    return $data;
  }

  /************************************ OTHER ************************************/


  public function loadTarget($item, $categories, $articles) {

    switch ($item->type) {

      case MenuItemTypes::ARTICLE:
        return $this->service->buildArticleUrl($categories, $articles, $item->target_id);
      case MenuItemTypes::CATEGORY:
        return $this->service->buildCategoryUrl($categories, $item->target_id);
      case MenuItemTypes::EXTERNAL_LINK:
        return $item->url;
      case MenuItemTypes::SEPARATOR:
        return '-';
      default:
        return '-';
    }
  }

}
?>