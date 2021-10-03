<?php

class ArticlesView extends MainView implements PagesViewInterface {

  public $pageName;
  private $breadcrumbs;
  public $article;
  private $comments;

  public function __construct($data) {
    parent::__construct();

    if (@exists($data)) {

      if (@exists($data->breadcrumbs)) {
        $this->breadcrumbs = $data->breadcrumbs;
      }

      if (@exists($data->article)) {
        $this->article = $data->article;
        $this->pageName = ucfirst($this->article->title);
      }

      if (@exists($data->comments)) {
        $this->comments = $data->comments;
      }
    }
  }


  // meta title tag
  public function displayMetaTitle() {
    $this->renderMetaTitle($this->pageName, $this->article);
  }


  // meta description, keywords and og tags
  public function displayAdditionalMetaTags() {
    $this->displayGenericAdditionalMetaTags($this->article);
  }


  public function displayPage() {

    $this->renderLangGroupIdHiddenField($this->article);

    echo '<div class="container">';
      $this->renderBreadcrumbs($this->breadcrumbs);
      $this->renderPageMainImage($this->article->mainImage, $this->article->title);
      $this->renderPageTitle($this->article);
      $this->renderPageSubtitle($this->article);
      //$this->renderEventDate();
      $this->renderPageContent($this->article);
      $this->displayGallery($this->article->gallery);
      $this->renderArticleComments();
    echo '</div>';
  }


  public function renderEventDate() {

    $date = @exists($this->article->eventDate) && $this->article->eventDate != '0000-00-00 00:00:00' ? $this->article->eventDate : $this->article->cdate;
    $formattedDate = Util::formatDate($date);

    echo '<div class="article-date">' . Trans::get('Released') . ': <span class="date">' . $formattedDate . '</span></div>';
  }


  public function renderArticleComments() {

    if (@exists($this->article->allowComments) && (int)$this->article->allowComments === 1) {

      if (@exists($this->comments)) {

        $this->renderCommentsSection($this->comments, $this->article);
      }

      $this->renderCommentsForm($this->article->id, Conf::get('comment_type_id')['article']);
    }
  }


  private function isArticle($id) {
    return (int)$this->article->id === (int)$id || (int)$this->article->langGroupId === (int)$id;
  }


  private function isParent($id) {
    return (int)$this->article->parentId === (int)$id || (int)$this->article->parentLangGroupId === (int)$id;
  }


  private function isCategoriesParent($id) {
    return (int)$this->article->categories_parent_id === (int)$id || (int)$this->article->categories_parent_lang_group_id === (int)$id;
  }
}

?>