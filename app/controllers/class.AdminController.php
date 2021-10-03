<?php

class AdminController extends Controller {

  public function __construct() {
    parent::__construct();

    $this->view = new AdminView();

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


  /*************************************************************************
   *                                 LOGIN                                  *
   *************************************************************************/

  public function loginPage() {

    $this->view->initController($this);
    $this->view->resource = 'login';
    $this->view->respond(null, 'admin/templates/pages/login/login.php');
  }


  /*************************************************************************
   *                              DASHBOARD                                 *
   *************************************************************************/

  public function dashboardPage() {

    $this->view->initController($this);
    $this->view->resource = 'dashboard';
    $this->view->respond(null, 'admin/templates/pages/dashboard/dashboard.php');
  }


  /*************************************************************************
   *                                 USERS                                  *
   *************************************************************************/

  public function usersPage() {

    $data = Dispatcher::instance()->dispatch('users', 'fetchWithFilters', $this->params());

    $this->view = new UsersAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'users';
    $this->view->respond($data, 'admin/templates/pages/users/table.php');
  }


  public function userInsertPage() {

    $id = $this->params('id');

    if ($id == 0) {
      $data = new stdClass();
      $data->id = 0;
    }
    else {
      $data = Dispatcher::instance()->dispatch('users', 'fetchOne', array('id' => $id));
    }

    $this->view = new UsersAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'users';
    $this->view->respond($data, 'admin/templates/pages/users/insert.php');
  }


  /*************************************************************************
   *                                CATEGORIES                              *
   *************************************************************************/

  public function categoriesPage() {

    $data = Dispatcher::instance()->dispatch('categories', 'fetchWithFilters', $this->params());

    $this->view = new CategoriesAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'categories';
    $this->view->respond(null, 'admin/templates/pages/categories/table.php');
  }


  public function categoryInsertPage() {

    $id = $this->params('id');

    $data = Dispatcher::instance()->dispatch('categories', 'fetchOneGroup', array('id' => $id, 'fetchWithUnpublished' => true));
    $data->languages = Languages::getActive();

    $data->categoriesTree = array();
    foreach ($data->items as $item) {
      $categories = Dispatcher::instance()->dispatch('categories', 'fetchAllByLangId', array('lang_id' => $item->langId, 'fetchWithUnpublished' => true));
      $data->categoriesTree[$item->langId] = Util::formTree($categories);
    }

    $this->view = new CategoriesAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'categories';
    $this->view->respond($data, 'admin/templates/pages/categories/insert.php');
  }


  /*************************************************************************
   *                               ARTICLES                                 *
   *************************************************************************/

  public function articlesPage() {

    $data = Dispatcher::instance()->dispatch('articles', 'fetchWithFilters', $this->params());

    $this->view = new ArticlesAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'articles';
    $this->view->respond($data, 'admin/templates/pages/articles/table.php');
  }


  public function articleInsertPage() {

    $id = $this->params('id');

    $data = Dispatcher::instance()->dispatch('articles', 'fetchOneGroup', array('id' => $id, 'fetchWithUnpublished' => true));
    $data->languages = Languages::getActive();

    $data->categoriesTree = array();
    foreach ($data->items as $item) {
      $categories = Dispatcher::instance()->dispatch('categories', 'fetchAllByLangId', array('lang_id' => $item->langId, 'fetchWithUnpublished' => true));
      $data->categoriesTree[$item->langId] = Util::formTree($categories);
    }

    $data->galleries = Dispatcher::instance()->dispatch('galleries', 'fetchAll', null);

    $this->view = new ArticlesAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'articles';
    $this->view->respond($data, 'admin/templates/pages/articles/insert.php');
  }


  /*************************************************************************
   *                                  MENUS                                 *
   *************************************************************************/


  public function menusPage() {

    $data = Dispatcher::instance()->dispatch('menus', 'fetchWithFilters', $this->params());
    $data->languages = Languages::getActive();

    $this->view = new MenusAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'menus';
    $this->view->respond($data, 'admin/templates/pages/menus/table.php');
  }


  public function menuItemsPage() {

    $data = Dispatcher::instance()->dispatch('menus', 'fetchOneWithItems', array('id' => $this->params('id')));

    $this->view = new MenuItemsAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'menus';
    $this->view->respond($data, 'admin/templates/pages/menus/items_table.php');
  }


  /*************************************************************************
   *                                SLIDERS                                 *
   *************************************************************************/


  public function slidersPage() {

    $data = Dispatcher::instance()->dispatch('sliders', 'fetchWithFilters', $this->params());
    $data->languages = Languages::getActive();

    $this->view = new SlidersAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'sliders';
    $this->view->respond($data, 'admin/templates/pages/sliders/table.php');
  }


  public function sliderItemsPage() {

    $data = Dispatcher::instance()->dispatch('sliders', 'fetchOneWithItems', array('id' => $this->params('id')));

    $this->view = new SliderItemsAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'sliders';
    $this->view->respond($data, 'admin/templates/pages/sliders/items_table.php');
  }


  public function sliderItemInsertPage() {

    $id = $this->params('id');

    if ($id == 0) {
      $data = new stdClass();
      $data->id = 0;
      $data->sliderId = $this->params('slider_id');
    }
    else {
      $data = Dispatcher::instance()->dispatch('sliders', 'fetchOneItem', array('id' => $this->params('id')));
    }

    $data->langId = Trans::getLanguageId();

    $this->view = new SliderItemsAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'sliders';
    $this->view->respond($data, 'admin/templates/pages/sliders/insert.php');
  }

  /*************************************************************************
   *                              GALLERIES                                 *
   *************************************************************************/

  public function galleriesPage() {

    $data = Dispatcher::instance()->dispatch('galleries', 'fetchWithFilters', $this->params());

    $this->view = new GalleriesAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'galleries';
    $this->view->respond($data, 'admin/templates/pages/galleries/table.php');
  }

  public function galleryInsertPage() {

    $id = $this->params('id');

    if ($id == 0) {
      $data = new stdClass();
      $data->id = 0;
      $data->sliderId = $this->params('slider_id');
    }
    else {
      $data = Dispatcher::instance()->dispatch('galleries', 'fetchOne', array('id' => $this->params('id')));
    }

    $data->langId = Trans::getLanguageId();

    $this->view = new GalleriesAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'galleries';
    $this->view->respond($data, 'admin/templates/pages/galleries/insert.php');
  }

  /*************************************************************************
   *                                  MEDIA                                 *
   *************************************************************************/

  public function mediaPage() {

    $data = Dispatcher::instance()->dispatch('media', 'fetchWithFilters', $this->params());

    $this->view = new MediaAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'media';
    $this->view->respond($data, 'admin/templates/pages/media/table.php');
  }


  /*************************************************************************
   *                             NEWSLETTER                                 *
   *************************************************************************/

  public function newsletterPage() {

    $data = Dispatcher::instance()->dispatch('newsletter', 'fetchWithFilters', $this->params());

    $this->view = new NewsletterAdminView($data, $this->params());
    $this->view->initController($this);
    $this->view->resource = 'newsletter';
    $this->view->respond(null, 'admin/templates/pages/newsletter/table.php');
  }


  /*************************************************************************
   *                                 LAYOUT                                 *
   *************************************************************************/

  public function topbar() {

    $auth = new OAuth2Wrapper();
    $data = $auth->getUserFromSession();

    $this->view->initController($this);
    $this->view->respond($data, 'admin/templates/layout/topbar.php');
  }

  /*************************************************************************
   *                                 OTHER                                 *
   *************************************************************************/

  public function run($action) {

    if (!$this->isAdmin()) {
      $this->loginPage();
    }
    else {
      $this->view = new AdminView();
      parent::run($action);
    }
  }


}
?>