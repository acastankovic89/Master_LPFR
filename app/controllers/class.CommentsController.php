<?php


class CommentsController extends Controller {

  private $service;
  private $commentsModel;

  public function __construct() {

    parent::__construct();
    $this->model->setTable('comments');

    $service = CommentsServices::Instance();
    if ($service instanceof CommentsServices) {
      $this->service = $service;
    }

    $commentsModel = Comments::Instance();
    if ($commentsModel instanceof Comments) {
      $this->commentsModel = $commentsModel;
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


  public function insertComment() {

    $params = $this->getPurifiedParams();

    if (!@exists($params['type_id'])) {
      $this->view->respond((object)[
        'status' => Errors::TYPE_MISSING,
        'message' => Trans::get('Type is missing')
      ]);
      return;
    }

    if (!Util::validateEmail($params['email'])) {

      $this->view->respond((object)[
        'status' => Errors::INVALID_EMAIL,
        'message' => Trans::get('Invalid E-mail address')
      ]);
      return;
    }

    $this->commentsModel->insert($params);

    if((int)$params['type_id'] === (int)Conf::get('comment_type_id')['article']) {

      $articles = new Articles();
      $params['item'] = $articles->getOne($params['target_id']);

    } else if((int)$params['type_id'] === (int)Conf::get('comment_type_id')['category']) {

      $categories = new Categories();
      $params['item'] = $categories->getOne($params['target_id']);
    }

    $emailsService = new EmailsService();
    $emailData = $emailsService->commentFormData($params);

    // send email to admin
    $mailer = new Mailer();
    $mailer->sendMail($emailData);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Comment added and waiting for approval')
    ]);
  }


  public function deleteComment() {

    if (!$this->crudAuthorized(CrudOperations::DELETE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $this->service->delete($params['id']);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => Trans::get('Item is deleted')
    ]);
  }


  public function publishComment() {

    if (!$this->crudAuthorized(CrudOperations::UPDATE)) {
      $this->view->unauthorized();
      return null;
    }

    $params = $this->getPurifiedParams();

    $data = $this->service->publish($params);

    $this->view->respond((object)[
      'status' => Errors::OK,
      'message' => (int)$data->published === 1 ? Trans::get('Comment is published') : Trans::get('Comment is unpublished'),
      'buttonText' => (int)$data->published === 1 ? Trans::get('Unpublish') : Trans::get('Publish'),
      'published' => $data->published
    ]);
  }


  /************************* READ OPERATIONS  *************************/

  public function fetchByTypeIdAndTargetId() {

    $params = $this->getPurifiedParams();

    $comments = $this->commentsModel->getByTypeIdAndTargetId($params['type_id'], $params['target_id'], $params['fetchWithUnpublished']);

    $data = Util::formTree($comments, 0);

    $this->apiRespond($data);
    return $data;
  }

}

?>