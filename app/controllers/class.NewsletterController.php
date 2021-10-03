<?php


class NewsletterController extends Controller {

  private $helper;
  private $newsletterModel;

  public function __construct() {
    parent::__construct();
    $this->model->setTable('newsletter');

    $helper = NewsletterHelper::Instance();
    if ($helper instanceof NewsletterHelper) {
      $this->helper = $helper;
    }

    $newsletterModel = Newsletter::Instance();
    if ($newsletterModel instanceof Newsletter) {
      $this->newsletterModel = $newsletterModel;
    }

    $scopes = array(
      CrudOperations::FETCH_ONE => Scopes::$ADMIN,
      CrudOperations::FETCH_ALL => Scopes::$ADMIN,
      CrudOperations::INSERT => Scopes::$ADMIN,
      CrudOperations::UPDATE => Scopes::$ADMIN,
      CrudOperations::DELETE => Scopes::$ADMIN,
      CrudOperations::FETCH_MINE => Scopes::$USER,
    );
    $this->setScopes($scopes);

    Trans::initTranslations();
  }

  /************************* CREATE, UPDATE, DELETE OPERATIONS  *************************/


  public function newsletterSignup() {

    $params = $this->getPurifiedParams();

    $validationResponse = $this->helper->validateSignup($params);

    // validate params
    if (!$validationResponse == Errors::OK) {
      $response = Errors::getResponseStatus($validationResponse);
      $this->view->respond((object)[
        'status' => $response->status,
        'message' => $response->message
      ]);
      return;
    }

    $this->newsletterModel->insert($params);

    $emailsService = new EmailsService();
    $emailToCustomerData = $emailsService->newsletterCustomerData($params);

    $mailer = new Mailer();

    // send email to customer
    if ($mailer->sendMail($emailToCustomerData)) {

      $emailToAdminData = $emailsService->newsletterAdminData($params);

      // send email to admin
      $mailer->sendMail($emailToAdminData);
    }
    else {

      $this->view->respond((object) array(
        'status' => Errors::EMAIL_NOT_SENT,
        'message' => Trans::get('There was an error sending E-mail')
      ));
      return;
    }

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('You have successfully subscribed to the newsletter, check your mail and spam folder.')
    ]);
    return;
  }



  // signup from site
  public function signup() {

    $params = $this->getPurifiedParams();

    $validationResponse = $this->helper->validateSignup($params);

    // validate params
    if (!$validationResponse == Errors::OK) {
      return Errors::getResponseStatus($validationResponse);
    }

    $this->newsletterModel->insert($params);

    return Errors::getResponseStatus(Errors::OK);
  }


  /************************* READ OPERATIONS  *************************/

  public function fetchWithFilters() {

    $params = Security::Instance()->purifyAll($this->params());

    $columns = array(
      array('columnName' => 'email')
    );

    $response = new stdClass();
    $response->total = $this->newsletterModel->getTotalItems($params, $columns);
    $response->items = $this->newsletterModel->getItemsWithFilters($params, $columns);

    $this->view->respond($response);
    return $response;
  }

  public function fetchOne() {

    $params = $this->getPurifiedParams();

    $data = $this->newsletterModel->getOne($params['id']);

    $this->view->respond($data);
    return $data;
  }

  public function fetchAll() {

    $data = $this->newsletterModel->getAll();

    $this->view->respond($data);
    return $data;
  }

  /************************************ OTHER ************************************/


  public function download() {

    if (!$this->crudAuthorized(CrudOperations::FETCH_ALL)) {
      $this->view->unauthorized();
      return null;
    }

    $data = Dispatcher::instance()->dispatch('newsletter', 'fetchAll', null);

    $downloadData = $this->helper->setDownloadData($data);

    $filename = Conf::get('site_name') . '_newsletter_list.xls';
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    Util::exportFile($downloadData->body, $downloadData->heading);
    exit;
  }

}

?>