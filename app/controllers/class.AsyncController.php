<?php


class AsyncController extends MainController {

  public function __construct() {
    parent::__construct();
    $this->view = new MainView;
  }


  public function languagesSet() {

    $params = Util::trimFields(Security::Instance()->purifyAll($this->params()));

    $language = Dispatcher::instance()->dispatch('languages', 'fetchOne', array('id' => $params['lang_id']));

    $route = null;
    if (@exists($params['route'])) {

      $route = $params['route'];

      // check if anchor link
      if (strpos($params['route'], '#') != false) {

        $routeArray = explode('#', $params['route']);
        $route = $routeArray[0];
        $params['route'] = $routeArray[0];
      }
    }

    $params['langAlias'] = $language->alias;
    $params['page_type'] = $this->aliasDecoding($route)->pageType;

    Trans::setLanguage($language);

    $url = $this->setLanguageChangeUrl($params);

    if (!@exists($url)) {
      $url = Conf::get('url') . '/404';
    }

    $this->apiRespond($url);
    return $url;
  }


  public function sendEmail() {

    if (!Util::validateEmail($this->params('email'))) {

      $success = false;
      $message = Trans::get('Invalid E-mail address');

    } else {

      $params = $this->getPurifiedParams();

      $emailsService = new EmailsService();
      $emailData = $emailsService->contactFormData($params);

      $mailer = new Mailer();
      if ($mailer->sendMail($emailData)) {

        $success = true;
        $message = Trans::get('E-mail was successfully sent');

      } else {
        $success = false;
        $message = Trans::get('There was an error sending E-mail');
      }
    }

    $response = new stdClass();
    $response->success = $success;
    $response->message = $message;

    $this->view->respond($response);
  }

}
?>