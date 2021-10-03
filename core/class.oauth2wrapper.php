<?php

use OAuth2\Request\TestRequest;

class NormacorePdo extends OAuth2\Storage\Pdo {
  public function __construct($connection, $config = array()) {
    // todo if no internet connection fails without warning. 
    parent::__construct($connection, $config);
    $this->config['user_table'] = 'users';
  }

  // use a secure hashing algorithm when storing passwords. Override this for your application
  protected function hashPassword($password) {
    $enc_hash = Conf::get('enc_hash');
    $enc_type = Conf::get('enc_type');
    return hash($enc_type, $password . $enc_hash);
  }
}

class NormacoreRequest extends OAuth2\Request implements OAuth2\RequestInterface {
  public $query, $request, $server, $headers;

  public function __construct() {
    $this->query = $_GET;
    $this->request = $_POST;
    $this->server = $_SERVER;
    $this->headers = array();
  }

  public function query($name, $default = null) {
    return isset($this->query[$name]) ? $this->query[$name] : $default;
  }

  public function request($name, $default = null) {
    return isset($this->request[$name]) ? $this->request[$name] : $default;
  }

  public function server($name, $default = null) {
    return isset($this->server[$name]) ? $this->server[$name] : $default;
  }

  public function getAllQueryParameters() {
    return $this->query;
  }

  public function setQuery(array $query) {
    $this->query = $query;
  }

  public function setMethod($method) {
    $this->server['REQUEST_METHOD'] = $method;
  }

  public function setPost(array $params) {
    $this->setMethod('POST');
    $this->request = $params;
  }

  public static function createPost(array $params = array()) {
    $request = new self();
    $request->setPost($params);

    return $request;
  }
}

class OAuth2Wrapper {

  public static $ACCESS_TOKEN = 'ns_access_token';
  public static $REFRESH_TOKEN = 'ns_refresh_token';

  public $server;
  public $response;

  public $error;
  public $errorDescription;
  public $tokenType;
  public $accessToken;
  public $refreshToken;
  public $scope;
  public $expiresIn;

  public function __construct() {
    $this->init();
  }

  public function init() {
    // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
    $dsn = 'mysql:dbname=' . Conf::get('db_name') . ';host=' . Conf::get('db_hostname');
    $username = Conf::get('db_username');
    $password = Conf::get('db_password');

    $storage = new NormacorePdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

    // Pass a storage object or array of storage objects to the OAuth2 server class
    $config = array(
      'access_lifetime' => Conf::get('Oauth2_access_lifetime'),
      'always_issue_new_refresh_token' => true,
      'refresh_token_lifetime' => Conf::get('Oauth2_refresh_token_lifetime')
    );
    $this->server = new OAuth2\Server($storage, $config);

    // Add the "Client Credentials" grant type (it is the simplest of the grant types)
    // TODO: paremetrizovati sa Conf koji grant tipovi su uključeni
    $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage, $config));
    $this->server->addGrantType(new OAuth2\GrantType\UserCredentials($storage, $config));
    $this->server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, $config));

    // Add the "Authorization Code" grant type (this is where the oauth magic happens)
    $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
  }

  public function token() {
    $this->response = $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals());
    $parameters = $this->response->getParameters();
    $this->processResponseParameters($parameters);
    $user = new User();
    $user->loadByAccessToken($this->accessToken);
    if ($user->id == null) {
      $response = Errors::getResponseStatus(Errors::INVALID_LOGIN);
      return $response;
    }
    if (!$user->active) {
      $response = Errors::getResponseStatus(Errors::USER_NOT_ACTIVATED);
      return $response;
    }
    $response = new stdClass();
    $response->token_type = $this->tokenType;
    $response->access_token = $this->accessToken;
    $response->refresh_token = $this->refreshToken;
    $response->scope = $this->scope;
    $response->status = Errors::OK;
    return $response;
  }


  public function revoke($token = null) {

    if (isset($token)) {
      $request = NormacoreRequest::createPost(array(
        'token_type_hint' => 'access_token',
        'token' => $token
      ));
      $this->server->handleRevokeRequest($request, $response = new OAuth2\Response());
    } else {
      $this->response = $this->server->handleRevokeRequest(OAuth2\Request::createFromGlobals());
      $this->response->send();
    }
  }


  private function processResponseParameters($parameters) {

    $this->error = isset($parameters['error']) ? $parameters['error'] : null;
    $this->errorDescription = isset($parameters['error_description']) ? $parameters['error_description'] : null;

    $this->accessToken = isset($parameters['access_token']) ? $parameters['access_token'] : null;
    $this->expiresIn = isset($parameters['expires_in']) ? $parameters['expires_in'] : null;
    $this->tokenType = isset($parameters['token_type']) ? $parameters['token_type'] : null;
    $this->scope = isset($parameters['scope']) ? $parameters['scope'] : null;
    $this->refreshToken = isset($parameters['refresh_token']) ? $parameters['refresh_token'] : null;
  }

  public function allowed($scopeToCheck) {

    $token = $this->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    // if token is set in the session
    if (!isset($token)) {
      $user = $this->getUserFromSession();
      if (!isset($user)) return false;
      return $user->scopeExist($scopeToCheck);
    }
    if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals(), null, $scopeToCheck)) {
      // $this->response = $this->server->getResponse()->send(); die;
      return false;
    }
    return true;
  }

  public function getUser() {
    // try to get user from access token from header, if it does not exist try from session
    $token = $this->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    $user = new User();
    if (!isset($token)) {
      return $this->getUserFromSession();
    }
    $user->loadByAccessToken($token['access_token']);
    return $user;
  }


  public function getUserFromSession() {
    if (isset($_SESSION[self::$ACCESS_TOKEN])) {
      // load user by access token.             
      $user = new User();
      $user->loadByAccessToken($_SESSION[self::$ACCESS_TOKEN]);
      return $user;
    }
    return null;
  }

  public function getUserFromAccessToken() {

    $token = $this->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    $user = new User();
    $user->loadByAccessToken($token['access_token']);
    return $user;
  }
  

}

?>