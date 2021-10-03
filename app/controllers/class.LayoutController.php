<?php

class LayoutController extends MainController {

  public function __construct() {
    parent::__construct();
    $this->view = new MainView;
  }

  public function header() {

    $this->view = new HeaderView();
    $this->view->setUser($this->user);
    $this->view->initController($this);
    $this->view->respond(null, 'templates/layout/header.php');
  }

  public function footer() {

    $this->view = new FooterView();
    $this->view->setUser($this->user);
    $this->view->initController($this);
    $this->view->respond(null, 'templates/layout/footer.php');
  }

  public function mainMenu() {

    $langId = Trans::getLanguageId();

    $data = Dispatcher::instance()->dispatch('menus', 'fetchTree', array('menu_id' => Conf::get('main_menu_id'), 'lang_id' => $langId));

    $this->view = new MenuView($data);
    $this->view->initController($this);
    $this->view->respond(null, 'templates/layout/menu.php');
  }

  public function homeSlider() {

    $langId = Trans::getLanguageId();

    $data = Dispatcher::instance()->dispatch('sliders', 'fetchOneWithItems', array('id' => Conf::get('home_slider_id'), 'lang_id' => $langId, 'basic_fetch' => true));

    $this->view = new SliderView($data);
    $this->view->initController($this);
    $this->view->respond(null, 'templates/layout/slider.php');
  }

  public function languages() {

    $data = Languages::getActive();

    $this->view->initController($this);
    $this->view->respond($data, 'templates/layout/languages.php');
  }

}
?>