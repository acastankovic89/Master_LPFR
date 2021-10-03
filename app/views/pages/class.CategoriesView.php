<?php

class CategoriesView extends MainView implements PagesViewInterface {

  public $pageName;
  private $breadcrumbs;
  public $category;
  private $articles;
  private $subCategories;


  public function __construct($data) {
    parent::__construct();

    if (@exists($data)) {

      if (@exists($data->breadcrumbs)) {
        $this->breadcrumbs = $data->breadcrumbs;
      }

      if (@exists($data->category)) {
        $this->category = $data->category;
        $this->pageName = ucfirst($this->category->name);
      }

      if (@exists($data->articles)) {
        $this->articles = $data->articles;
      }

      if (@exists($data->subCategories) && @exists($data->subCategories['children'])) {
        $this->subCategories = $data->subCategories['children'];
      }
    }
  }


  // meta title tag
  public function displayMetaTitle() {
    $this->renderMetaTitle($this->pageName, $this->category);
  }


  // meta description, keywords and og tags
  public function displayAdditionalMetaTags() {
    $this->displayGenericAdditionalMetaTags($this->category);
  }


  public function displayBreadcrumbs() {
    $this->renderBreadcrumbs($this->breadcrumbs);
  }


  public function displayPage() {

    $this->renderLangGroupIdHiddenField($this->category);

    echo '<div class="container">';
      $this->renderBreadcrumbs($this->breadcrumbs);
      $this->renderPageMainImage($this->category->mainImage, $this->category->title);
      $this->renderPageTitle($this->category);
      $this->renderPageSubtitle($this->category);
      $this->renderPageContent($this->category);
      $this->renderSubCategories();
      $this->renderArticles();
      $this->displayGallery($this->category->gallery);
    echo '</div>';
  }


  public function renderSubCategories() {

    if (@exists($this->subCategories)) {

      echo '<div class="nc-row nc-cols-4 categories">';

      foreach ($this->subCategories as $category) {

        $this->renderCategory($category);
      }

      echo '</div>';
    }
  }


  public function renderArticles() {

    if (@exists($this->articles)) {

      echo '<div class="nc-row nc-cols-4 articles">';

      foreach ($this->articles as $article) {

        $this->renderArticle($article);
      }

      echo '</div>';
    }
  }


  private function isCategory($id) {
    return (int)$this->category->id === (int)$id || (int)$this->category->langGroupId === (int)$id;
  }


  private function isParent($id) {
    return (int)$this->category->parentId === (int)$id || (int)$this->category->parentLangGroupId === (int)$id;
  }
}

?>