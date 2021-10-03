<?php


class AuthController extends MainController {

  public function __construct() {

    parent::__construct();
  }


  public function token() {

    session_unset();
    $auth = new OAuth2Wrapper();
    $response = $auth->token();

    $this->view->respond($response);
    exit;
  }

  // login keeps token in the session
  public function login() {

    $auth = new OAuth2Wrapper();
    $response = $auth->token();
    // only for application where access token is stored in the session.
    Util::sessionStart();
    session_unset();
    if (isset($response->access_token)) {
      $_SESSION[OAuth2Wrapper::$ACCESS_TOKEN] = $auth->accessToken;
      $_SESSION[OAuth2Wrapper::$REFRESH_TOKEN] = $auth->refreshToken;
    }
    $this->view->respond($response);
  }

  public function revoke() {
    $auth = new OAuth2Wrapper();
    $auth->revoke();
  }


  public function logout() {
    Util::sessionStart();
    $accessToken = $_SESSION[OAuth2Wrapper::$ACCESS_TOKEN];
    $refreshToken = $_SESSION[OAuth2Wrapper::$REFRESH_TOKEN];
    $auth = new OAuth2Wrapper();
    $auth->revoke($accessToken);
    $auth->revoke($refreshToken);
    unset($_SESSION[OAuth2Wrapper::$ACCESS_TOKEN]);
    unset($_SESSION[OAuth2Wrapper::$REFRESH_TOKEN]);
  }


  public function protectedExample() {

    // Handle a request to a resource and authenticate the access token
    $auth = new OAuth2Wrapper();
    if ($auth->allowed()) echo json_encode(array('success' => true, 'message' => 'You accessed my APIs!'));
    else echo $auth->response;
  }

}

?>