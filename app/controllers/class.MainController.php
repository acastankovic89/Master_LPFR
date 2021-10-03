<?php


class MainController extends Controller {

  public $user;

  public function __construct() {
    parent::__construct();

    $auth = new OAuth2Wrapper();
    $this->user = $auth->getUserFromSession();
  }


  protected function setLanguageChangeUrl($params) {

    switch ($params['page_type']) {
      case 'article':
        $data = Dispatcher::instance()->dispatch('articles', 'fetchByLanguageGroupIdAndLanguageId', array('lang_group_id' => $params['lang_group_id'], 'lang_id' => $params['lang_id']));
        return $data->url;
      case 'category':
        $data = Dispatcher::instance()->dispatch('categories', 'fetchByLanguageGroupIdAndLanguageId', array('lang_group_id' => $params['lang_group_id'], 'lang_id' => $params['lang_id']));
        return $data->url;
      case '':
        $url = $this->setStaticRouteUrl($params);
        return $url;
      default:
        return Conf::get('url');
    }
  }

  protected function setStaticRouteUrl($params) {

    if (!@exists($params['route'])) return Conf::get('url');

    $matchedRoute = $this->findCurrentRoute($params);

    if (!@exists($matchedRoute)) return Conf::get('url');

    return $this->setStaticRouteUrlByLangAlias($matchedRoute, $params);
  }

  protected function findCurrentRoute($params) {
    $matchedRoute = array();
    foreach ($this->routes as $route) {
      if ((string)$route['route'] === (string)$params['route']) {
        $matchedRoute = $route;
      }
    }

    return $matchedRoute;
  }

  protected function setStaticRouteUrlByLangAlias($matchedRoute, $params) {

    $url = Conf::get('url') . '/';

    foreach ($this->routes as $route) {

      if ($route['action'] === $matchedRoute['action']) {

        if (@exists($route['langAlias']) && $route['langAlias'] === $params['langAlias']) {
          $url .= $route['route'];
        }
      }
    }

    return $url;
  }

  protected function googleRecaptchaChecked() {

    if (!isset($_POST['g-recaptcha-response']) || (string)$_POST['g-recaptcha-response'] === '' || !$_POST['g-recaptcha-response']) return false;

    $secretKey = Conf::get('recaptcha_secret_key');
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    $captcha = $_POST['g-recaptcha-response'];

    $captchaUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $captcha . '&remoteip=' . $remoteIp;

    $captchaResponse = file_get_contents($captchaUrl);

    $response = json_decode($captchaResponse);

    return $response->success;
  }

}
?>