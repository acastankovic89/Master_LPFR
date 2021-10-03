<?php

class UsersController extends Controller {

  public $userService;
  public $userHelper;
  public $user;

  public function __construct() {
    parent::__construct();

    $this->model->setTable('users');
    $this->userService = new UsersService();
    $this->userHelper = new UserHelper();
    $this->view = new View();
    $this->user = new User();

    $scopes = array(
      CrudOperations::FETCH_ONE => Scopes::$ADMIN,
      CrudOperations::FETCH_ALL => Scopes::$ADMIN,
      CrudOperations::INSERT => Scopes::$ADMIN,
      CrudOperations::UPDATE => Scopes::$ADMIN,
      CrudOperations::DELETE => Scopes::$ADMIN,
      CrudOperations::FETCH_MINE => Scopes::$USER
    );
    $this->setScopes($scopes);
  }

  public function insertUser() {

    if (!$this->crudAuthorized(CrudOperations::INSERT)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if(!@exists($params['email'])) {
      $this->view->respond((object) [
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Email address is required')
      ]);
      return;
    }

    if(!Util::validateEmail($params['email'])) {
      $this->view->respond((object) [
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('Invalid Email address')
      ]);
      return;
    }

    if(!@exists($params['first_name']) || !@exists($params['last_name'])) {

      $this->view->respond((object)[
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('First and last name required')
      ]);
      return;
    }

    if((int)$params['id'] === 0) {

      if(!@exists($params['password']) || !@exists($params['repeated_password'])) {

        $this->view->respond((object)[
          'status' => Errors::PASSWORD_AND_REPEATED_PASSWORD_REQUIRED,
          'message' => Trans::get('Password and repeated password required')
        ]);
        return;
      }

      if(strlen($params['password']) < 6) {

        $this->view->respond((object)[
          'status' => Errors::INVALID_PASSWORD,
          'message' => Trans::get('Password must contain min 6 characters')
        ]);
        return;
      }

      if((string)$params['password'] !== (string)$params['repeated_password']) {

        $this->view->respond((object)[
          'status' => Errors::NOT_MATCHING_PASSWORDS,
          'message' => Trans::get('Password and repeated must be the same')
        ]);
        return;
      }
    }

    $params['username'] = $params['email'];

    $params['scope'] = 'user';
    if(@exists($params['role'])) {
      if((int)$params['role'] === (int)UserRoleIds::ADMIN) {
        $params['scope'] = 'users user admin logs log';
      }
    }

    $user = new User();
    $user->map($params);

    if($user->exists()) {
      $this->view->respond((object) [
        'status' => Errors::EMAIL_EXISTS,
        'message' => Trans::get('Email address exists')
      ]);
      return;
    }

    $user->insert($user);

    $this->view->respond((object) [
      'status' => Errors::OK,
      'message' => Trans::get('Item is created'),
      'lastInsertId' => $this->model->lastInsertId()
    ]);
  }

  public function updateUser() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    if(!@exists($params['first_name']) || !@exists($params['last_name'])) {

      $this->view->respond((object) [
        'status' => Errors::EMPTY_FIELDS,
        'message' => Trans::get('First and last name required')
      ]);
      return;
    }

    $params['scope'] = 'user';
    if(@exists($params['role'])) {
      if((int)$params['role'] === (int)UserRoleIds::ADMIN) {
        $params['scope'] = 'users user admin logs log';
      }
    }

    $this->user->update($params);

    $this->view->respond((object) [
      'status' => Errors::OK,
      'message' => Trans::get('Item is updated'),
      'params' => $params
    ]);

  }

  public function deleteUser() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->model->delete($params['id']);

    $this->view->respond((object) [
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted'),
    ]);
  }

  public function fetchOne() {

    if (!$this->authorized(UserRoles::USER)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();
    $user = $this->user->loadAndMap($params['id']);

    $this->apiRespond($user);
    return $user;
  }

  public function fetchAll() {

    if (!$this->authorized(UserRoles::USER)) {
      $this->view->unauthorized();
      return null;
    }

    $rawUsers = $this->user->load();

    $users = array();
    foreach ($rawUsers as $rawUser) {
      $user = new User();
      $user->map($rawUser);

      array_push($users, $user);
    }

    $this->apiRespond($users);
    return $users;
  }

  public function register() {

    $params = $this->getPurifiedParams();
    $user = new User();
    $user->map($params);

    $result = $this->userService->register($user, $params);

    $response = Errors::getResponseStatus($result);

    if ($result == Errors::OK) {
      $response->user = $user;
    }

    $this->view->respond($response, null, Request::JSON_REQUEST);
    return $response;
  }

  public function activate() {

    $params = $this->getPurifiedParams();
    $user = new User();
    $user->map($params);

    $result = $this->userService->activate($user);

    $response = Errors::getResponseStatus($result);

    $this->view->respond($response, null, Request::JSON_REQUEST);
    return $response;
  }

  public function sendResetPassword() {

    $params = $this->getPurifiedParams();
    $user = new User();
    $user->map($params);

    $result = $this->userService->sendResetPassword($user);

    $response = Errors::getResponseStatus($result);

    $this->view->respond($response, null, Request::JSON_REQUEST);
    return $response;
  }

  public function resetPassword() {

    $params = $this->getPurifiedParams();
    $user = new User();
    $user->map($params);

    $result = $this->userService->resetPassword($user, $params);

    $response = Errors::getResponseStatus($result);

    $this->view->respond($response, null, Request::JSON_REQUEST);
    return $response;
  }

  public function fetchWithFilters() {

    $params = $this->getPurifiedParams();

    $columns = array(
      array('columnName' => 'first_name'),
      array('columnName' => 'last_name'),
      array('columnName' => 'email'),
      array('columnName' => 'address')
    );

    $response = new stdClass();
    $response->total = $this->user->getTotalItems($params, $columns);
    $response->items = $this->user->getItemsWithFilters($params, $columns);

    return $response;
  }

  public function changePassword() {
       
    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }
    
   $auth = new OAuth2Wrapper();
   $user = $auth->getUser();

   $params = $this->getPurifiedParams();

   $result = $this->userService->setNewPassword($user, $params);

   $response = Errors::getResponseStatus($result);

   $this->view->respond($response);
}
}

?>