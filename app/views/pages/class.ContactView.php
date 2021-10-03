<?php


class ContactView extends MainView implements PagesViewInterface {

  public $pageName;

  public function __construct() {
    parent::__construct();

    $this->pageName = ucfirst(Trans::get('Contact'));
  }

  // meta title tag
  public function displayMetaTitle() {
    $title = $this->pageName;
    $this->renderMetaTitle($title);
  }


  // meta description, keywords and og tags
  public function displayAdditionalMetaTags() {
    $this->displayStaticAdditionalMetaTags(array('title' => $this->pageName));
  }

}
?>