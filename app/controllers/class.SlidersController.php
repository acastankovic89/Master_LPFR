<?php


class SlidersController extends Controller {

  private $sliderRequiredFields = array('name');
  private $sliderItemsRequiredFields = array('image');
  private $service;
  private $slidersModel;
  private $sliderItemsModel;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('sliders');

    $service = SlidersService::Instance();
    if ($service instanceof SlidersService) {
      $this->service = $service;
    }

    $slidersModel = Sliders::Instance();
    if ($slidersModel instanceof Sliders) {
      $this->slidersModel = $slidersModel;
    }

    $sliderItemsModel = SliderItems::Instance();
    if ($sliderItemsModel instanceof SliderItems) {
      $this->sliderItemsModel = $sliderItemsModel;
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


  /*** sliders ***/

  public function insertSlider() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->sliderRequiredFields)) {

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


  public function updateSlider() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->sliderRequiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $this->slidersModel->update($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is created')
    ]);
  }

  public function deleteSlider() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->slidersModel->delete($params['id']);
    $this->sliderItemsModel->resetSliderId($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }


  /*** slider items ***/

  public function insertSliderItem() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->sliderItemsRequiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $this->sliderItemsModel->insert($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is created'),
      'lastInsertId' => $this->sliderItemsModel->lastInsertId()
    ]);
  }


  public function updateSliderItem() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if (!Helper::validateRequiredFields($params, $this->sliderItemsRequiredFields)) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Required fields are empty')
      ]);

      return;
    }

    $this->sliderItemsModel->update($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is updated')
    ]);
  }


  public function deleteSliderItem() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->sliderItemsModel->delete($params['id']);

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

    $this->sliderItemsModel->updatePositions($params['id'], $params['position']);

    $item = $this->sliderItemsModel->getOne($params['id']);

    $this->view->respond($item);
    return $item;
  }

  /************************* READ OPERATIONS  *************************/

  /*** sliders ***/

  public function fetchWithFilters() {

    $params = $this->getPurifiedParams();

    $response = new stdClass();
    $response->total = $this->slidersModel->getTotal($params);
    $response->items = $this->slidersModel->getWithFilters($params);

    $this->view->respond($response);
    return $response;
  }

  public function fetchOne() {

    $params = $this->getPurifiedParams();

    if(Languages::enabled() && @exists($params['lang_id'])) {

      $item = $this->slidersModel->getByLangGroupIdAndLangId($params['id'], $params['lang_id']);

    } else {

      $item = $this->slidersModel->getOne($params['id']);
    }

    $this->apiRespond($item);
    return $item;
  }

  public function fetchOneWithItems() {

    $params = $this->getPurifiedParams();

    $slider = Dispatcher::instance()->dispatch('sliders', 'fetchOne', array('id' => $params['id'], 'lang_id' => $params['lang_id']));

    $allItems = $this->sliderItemsModel->getBySliderId($slider->id);

    if(!@exists($params['slider_id'])) {
      $params['slider_id'] = $params['id'];
    }

    $response = new stdClass();
    $response->slider = $slider;
    $response->allItems = $allItems;

    if(!@exists($params['basic_fetch']) || !$params['basic_fetch']) {

      $itemsForTable = new stdClass();
      $itemsForTable->total = $this->sliderItemsModel->getTotal($params);
      $itemsForTable->items = $this->sliderItemsModel->getWithFilters($params);

      $response->itemsTree = Util::formTree($allItems);;
      $response->itemsForTable = $itemsForTable;
    }

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


  /*** slider items ***/

  public function fetchItemsBySliderId() {

    $params = $this->getPurifiedParams();
    $items = $this->sliderItemsModel->getBySliderId($params['slider_id']);

    $this->apiRespond($items);
    return $items;
  }

  public function fetchOneItem() {

    $params = $this->getPurifiedParams();
    $item = $this->sliderItemsModel->getOne($params['id']);

    $data = $this->service->setItem($item);

    $this->apiRespond($data);
    return $data;
  }

  /************************************ OTHER ************************************/

}
?>