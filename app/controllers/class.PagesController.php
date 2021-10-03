<?php


class PagesController extends MainController {

  public function __construct() {
    parent::__construct();

    $this->view = new MainView;

    Trans::initTranslations();
  }


  public function aliasDecoding($alias = null) {

    $data = parent::aliasDecoding($alias);

    if ($data->pageType === 'category') {
      $this->categoryPage($data);
    }
    else if ($data->pageType === 'article') {
      $this->articlePage($data);
    }
    else $this->errorPage();

    return $data;
  }

  /******************************** CONTENT ********************************/


  public function categoryPage($data) {

    Trans::setLanguageById($data->category->lang_id);

    $this->view = new CategoriesView($data);
    $this->view->initController($this);
    $this->view->respond($data, 'templates/pages/category.php');
  }


  public function articlePage($data) {

    Trans::setLanguageById($data->article->lang_id);

    $comments = Dispatcher::instance()->dispatch('comments', 'fetchByTypeIdAndTargetId', array('type_id' => Conf::get('comment_type_id')['article'], 'target_id' => $data->article->id));

    if (@exists($comments)) {
      $data->comments = $comments;
    }

    $this->view = new ArticlesView($data);
    $this->view->initController($this);
    $this->view->respond($data, 'templates/pages/article.php');
  }


  /*************************** /END OF CONTENT ***************************/


  /***************************** USER PAGES *****************************/


    public function registrationPage() {

    $this->setLanguageByAlias($this->langAlias());

    $this->redirectIfUserLoggedIn();

    $this->view->initController($this);
    $this->view->respond(null, 'templates/pages/registration.php');
  }

  public function loginPage() {

    $this->setLanguageByAlias($this->langAlias());

    $this->redirectIfUserLoggedIn();

    $this->view->initController($this);
    $this->view->respond(null, 'templates/pages/login.php');
  }


  public function logoutPage() {

    $authController = new AuthController();
    $authController->logout();
    header('Location: ' . Conf::get('url'));
  }


  public function userActivationPage() {

    $this->setLanguageByAlias($this->langAlias());

    $token = $this->params('token');
    $data = Dispatcher::instance()->dispatch('users', 'activate', array('token' => $token));

    $this->view->initController($this);
    $this->view->respond($data, 'templates/pages/user_activation.php');
  }


  /************************* /END OF USER PAGES *************************/


  /**************************** STATIC PAGES ****************************/


  public function homePage() {

    $this->setLanguageByAlias($this->langAlias());

    $this->view = new HomeView();
    $this->view->setUser($this->user);
    $this->view->initController($this);
    $this->view->respond(null, 'templates/pages/home.php');
  }

  public function errorPage() {

    $this->setLanguageByAlias($this->langAlias());

    $this->view = new ErrorPageView();
    $this->view->initController($this);
    $this->view->respond(null, 'templates/pages/404.php');
  }

  public function contactPage() {

    $this->setLanguageByAlias($this->langAlias());

    $this->view = new ContactView();
    $this->view->initController($this);
    $this->view->respond(null, 'templates/pages/contact.php');
  }


  /************************ /END OF STATIC PAGES ************************/


  /******************************* OTHER *******************************/


  public function testPage() {

    $this->setLanguageByAlias($this->langAlias());

    $data = new stdClass();
    $this->view->respond($data, 'templates/pages/test.php');
  }


  public function redirectIfUserLoggedIn($location = '') {

    if (@exists($this->user)) {
      header('Location: ' . Conf::get('url') . '/' . $location);
      exit;
    }
  }
}

?>