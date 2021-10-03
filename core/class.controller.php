<?php
//////////////////////////////////////////////////
// 
// Project: Norma Core
// Company: Normasoft
// Author: Milos Pavlovic
// Email: milos.pavlovic@normasoft.net 
// Date: Spt 21, 2015
// Controller
//
//////////////////////////////////////////////////

class Controller {

  protected $urlParams;
  protected $request;
  protected $view;
  protected $model;
  protected $user;
  protected $langAlias;
  protected $routes;
  protected $scopes;

  public function __construct() {

    $view = View::Instance();
    $this->view = $view instanceof View ? $view : new View();
    $this->view->setController($this);

    $model = Model::Instance();
    $this->model = $model instanceof Model ? $model : new Model();
  }

  public function setUrlParams($params) {
    $this->urlParams = $params;
  }

  public function setRequest($request) {

    $this->request = $request;
    $this->view->setRespondType($this->request->getRespondType());
  }

  public function getRequest() {
    return $this->request;
  }

  public function setLangAlias($langAlias) {
    $this->langAlias = $langAlias;
  }

  public function langAlias() {
    return $this->langAlias;
  }

  public function setRoutes($routes) {
    $this->routes = $routes;
  }

  public function setScopes($scopes) {
    $this->scopes = $scopes;
  }

  public function getScopes() {
    return $this->getScopes();
  }

  public function params($key = null) {

    $result = $this->request->get_params();

    if (isset($key)) return $result[$key];

    return $result;
  }

  public function post() {
    return $this->request->post;
  }

  public function run($action) {
    $result = $this->$action();
    return $result;
  }

  //basic crud
  public function fetch() {

    if ($this->crudAuthorized(CrudOperations::FETCH_ALL)) {

      $data = $this->model->load();
      $this->view->respond($data, null);
      return $data;
    }

    $this->view->unauthorized();
    return null;
  }

  public function fetchOne() {

    if ($this->crudAuthorized(CrudOperations::FETCH_ONE)) {

      $data = $this->model->load($this->params('id'));
      $this->view->respond($data, null);
      return $data;
    }

    $this->view->unauthorized();
    return null;
  }

  public function fetchMine() {

    $auth = new OAuth2Wrapper();
    $user = $auth->getUserFromSession();

    if ($this->crudAuthorized(CrudOperations::FETCH_MINE)) {

      $data = $this->model->loadFilter(array('user_id' => $user->id));
      $this->view->respond($data, null);
      return $data;
    }

    $this->view->unauthorized();
    return null;
  }

  public function insert() {

    if ($this->crudAuthorized(CrudOperations::INSERT)) {
      $this->model->insert($this->params());
      $data = $this->model->loadLastInsert();

      if ($data) $this->view->respond($data, null);
      else $this->view->respondError($this->model->getError(), null);

      return $data;
    }

    $this->view->unauthorized();
    return null;
  }

  public function update() {

    if ($this->crudAuthorized(CrudOperations::UPDATE)) {

      $this->model->update($this->params());
      $data = $this->model->load($this->params('id'));

      if ($data) $this->view->respond($data, null);
      else $this->view->respondError($this->model->getError(), null);

      return $data;
    }

    $this->view->unauthorized();
    return null;
  }

  public function delete() {

    if ($this->crudAuthorized(CrudOperations::DELETE)) {
      $result = $this->model->delete($this->params('id'));

      if ($result) $this->view->respond($result, null);
      else $this->view->respondError($this->model->getError(), null);

      return $result;
    }
    $this->view->unauthorized();
    return null;
  }

  public function required($data, $requirements) {

    return $this->model->required($data, $requirements);
  }

  protected function setLanguageByAlias($langAlias = null) {

    if (@exists($langAlias)) {
      Trans::setLanguageByAlias($langAlias);
    }
  }

  protected function crudAuthorized($operation) {
    if (!isset($this->scopes)) return true;
    if (isset($this->scopes[$operation])) {
      $auth = new OAuth2Wrapper();
      return $auth->allowed($this->scopes[$operation]);
    }
    return true;
  }

  protected function authorized($scope) {
    $auth = new OAuth2Wrapper();
    return $auth->allowed($scope);
  }

  protected function isAdmin() {

    $auth = new OAuth2Wrapper();
    $currentUser = $auth->getUserFromSession();

    if (@exists($currentUser)) {

      if ($currentUser->hasScope(Scopes::$ADMIN)) {
        return true;
      }
    }
    return false;
  }

  protected function apiRespond($data) {
    $response = new stdClass();
    $response->data = $data;
    $this->view->respond($response);
    return $response;
  }

  public function aliasDecoding($alias = null) {

    $params = $this->params();

    $aliasString = '';
    if (isset($alias)) {
      $aliasString = $alias;
    }
    else if (isset($params['alias'])) {
      $aliasString = $params['alias'];
    }

    $aliases = explode('/', $aliasString);

    $pageType = '';
    $parentId = 0;
    $category = null;
    $articles = null;
    $article = null;
    $subCategories = array();
    $breadcrumbs = array();

    //$categories = Categories::getCategories(Trans::getLanguageId());
    $categories = Categories::getAllCategories();
    foreach ($categories as $category) {
      $category->type = 'category';
    }

    foreach ($aliases as $alias) {

      $breadcrumbItem = new stdClass();
      $category = $this->findCategory($categories, $parentId, $alias);

      if ($category) {

        $parentId = $category->id;
        $breadcrumbItem->category = $category;

        if ($alias === end($aliases)) {
          $subCategoriesTreeArray = Util::formTree($categories, $category->id);
          $subCategories = $subCategoriesTreeArray[0];

          $articles = Dispatcher::instance()->dispatch('articles', 'fetchByParentId', array('parent_id' => $category->id, 'order_by' => json_encode(array('rang' => '', 'id' => ''))));
          $pageType = 'category';
        }
      }
      else {
        if ($alias === end($aliases)) {

          $article = Dispatcher::instance()->dispatch('articles', 'fetchByAlias', array('parent_id' => $parentId, 'alias' => $alias, 'shortcodes' => true));

          if (isset($article->id)) {
            $breadcrumbItem->article = $article;
            $pageType = 'article';
          }
        }
      }
      array_push($breadcrumbs, $breadcrumbItem);
    }

    $data = new stdClass();

    $data->categories = $categories;
    $data->category = $category;
    $data->subCategories = $subCategories;
    $data->articles = $articles;
    $data->article = $article;
    $data->breadcrumbs = $this->setBreadcrumbs($breadcrumbs);;
    $data->pageType = $pageType;

    return $data;
  }

  private function findCategory($categories, $parentId, $alias) {

    foreach ($categories as $category) {
      if ($category->parent_id == $parentId && $category->alias == $alias) return $category;
    }
    return false;
  }

  protected function setBreadcrumbs($generatedBreadcrumbs) {

    $breadcrumbs = array();

    $url = Conf::get('url');
    $name = '';

    foreach ($generatedBreadcrumbs as $item) {
      if(@exists($item->category)) {
        $url .= '/' . $item->category->alias;
        $name = $item->category->name;
      }
      else if(@exists($item->article)) {
        $url .= '/' . $item->article->alias;
        $name = $item->article->title;
      }

      array_push($breadcrumbs, array('url' => $url, 'name' => $name));
    }

    return $breadcrumbs;
  }

  public function getPurifiedParams() {

    $params = $this->params();
    $params = Security::Instance()->purifyAll($params);
    return $params;
  }
}


final class CrudOperations {
  const FETCH_ONE = 'fetchOne';
  const FETCH_ALL = 'fetchAll';
  const FETCH_MINE = 'fetchMine';
  const INSERT = 'insert';
  const UPDATE = 'update';
  const DELETE = 'delete';
}

?>