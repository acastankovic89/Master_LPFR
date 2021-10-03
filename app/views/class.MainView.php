<?php


interface PagesViewInterface {

  public function displayMetaTitle();
  public function displayAdditionalMetaTags();
}

class MainView extends View {

  public $user;
  public $pageName;

  public function __construct() {
    parent::__construct();
  }


  public function setUser($user) {
      if(@exists($user)) {
        $this->user = $user;
      }
  }

  /****************************************** BREADCRUMBS ******************************************/

  public function renderBreadcrumbs($breadcrumbs) {

    echo '<div class="breadcrumbs">';

      echo '<a href="' . Conf::get('url') . '"><i class="fa fa-home"></i></a>';

      $totalCrumbs = count($breadcrumbs);

      $counter = 1;
      foreach ($breadcrumbs as $breadcrumb) {

          if(is_array($breadcrumb)) $breadcrumb = (object) $breadcrumb;

          if ($counter < $totalCrumbs) {

            echo '<a href="' . $breadcrumb->url . '">' . $breadcrumb->name . '</a>';
          }
          else echo '<span class="crumb-current">' . $breadcrumb->name . '</span>';

        $counter++;
      }

    echo '</div>';
  }

  public function renderSimpleBreadcrumbs($pageName) {

    $pageName = ucfirst($pageName);

    echo '<div class="breadcrumbs">';
      echo '<a href="' . Conf::get('url') . '"><i class="fa fa-home"></i></a>';
      echo '<span class="crumb-current">' . $pageName . '</span>';
    echo '</div>';
  }


  /******************************************* META TAGS *******************************************/


  /**** SETTERS ****/

  public function setMetaTitle($pageName = null, $data = null) {

    $title = '';
    if (@exists($data) && @exists($data->metaTitle)) {
      $title = $data->metaTitle;
    }
    else {
      if (@exists($pageName)) {
        $title = $pageName;
      }
    }

    return @exists($title) ? $title : Conf::get('site_name');
  }

  public function setMetaKeywords($data) {
    return @exists($data) && @exists($data->metaKeywords) ? $data->metaKeywords : Conf::get('meta_tags')['keywords'];
  }

  public function setMetaDescription($data) {

    if (@exists($data) && @exists($data->metaDescription)) {
      $description = $data->metaDescription;
    }
    else {
      $description = '';
      if (@exists($data->title)) $description .= $data->title . ',';
      if (@exists($data->subtitle)) $description .= $data->subtitle . ',';
      if (@exists($data->name)) $description .= $data->name . ',';
      if (@exists($data->content)) $description .= strip_tags($data->content);
    }

    if ((string)$description === '') $description = Conf::get('meta_tags')['description'];

    $metaDescription = Util::truncateString($description, 160);

    return trim($metaDescription, ',');
  }


  /**** COMPONENTS RENDERING ****/

  public function renderMetaTitle($data = null, $pageName = null) {
    $title = $this->setMetaTitle($data, $pageName);
    ?><title><?php echo $title; ?></title><?php
  }

  public function renderMetaKeywords($data) {
    $keywords = $this->setMetaKeywords($data);
    ?><meta name="keywords" content='<?php echo $keywords; ?>'><?php
  }

  public function renderMetaDescription($data) {
    $description = $this->setMetaDescription($data);
    ?><meta name="description" content='<?php echo $description; ?>'><?php
  }

  public function renderMetaOgTags($title, $description, $image, $url) {
    echo '<meta property="og:title" content="' . $title . '" />';
    echo '<meta property="og:type" content="article">';
    echo '<meta property="og:image" content="' . $image . '" />';
    echo '<meta property="og:image:width" content="' . Conf::get('meta_tags')['og']['width'] . '">';
    echo '<meta property="og:image:height" content="' . Conf::get('meta_tags')['og']['height'] . '">';
    echo '<meta property="og:image:alt" content="' . $title . '" />';
    echo '<meta property="og:url" content="' . $url . '" />';
    echo '<meta property="og:site_name" content="' . Conf::get('meta_tags')['og']['site_name'] . '">';
    ?><meta property="og:description" content='<?php echo $description; ?>' /><?php
  }

  /**** DISPLAY ****/

  // data => ('title', 'description', 'keywords', 'url', 'image')
  public function displayStaticAdditionalMetaTags($data) {

    $title = Conf::get('meta_tags')['title'];
    $description = Conf::get('meta_tags')['description'];
    $keywords = Conf::get('meta_tags')['keywords'];
    $ogUrl = Conf::get('meta_tags')['og']['url'];
    $ogImage = Conf::get('meta_tags')['og']['image'];

    if (@exists($data)) {
      if (@exists($data['title'])) $title = $data['title'];
      if (@exists($data['description'])) $description = $data['description'];
      if (@exists($data['keywords'])) $keywords = $data['keywords'];
      if (@exists($data['url'])) $ogUrl = $data['url'];
      if (@exists($data['image'])) $ogImage = $data['image'];
    }

    ?><meta name="description" content='<?php echo $description; ?>'><?php
    ?><meta name="keywords" content='<?php echo $keywords; ?>'><?php
    $this->renderMetaOgTags($title, $description, $ogImage, $ogUrl);
  }

  public function displayGenericAdditionalMetaTags($data) {

    $title = Conf::get('meta_tags')['title'];
    $ogImage = Conf::get('meta_tags')['og']['image'];
    $ogUrl = Conf::get('meta_tags')['og']['url'];
    $description = $this->setMetaDescription($data);

    if (@exists($data)) {

      if (@exists($data->title)) {
        $title = $data->title;
      }
      else if (@exists($data->name)) {
        $title = $data->name;
      }

      if (Util::mediaImageExists($data->image)) {
        $ogImage = Conf::get('media_url') . '/' . $data->image;
      }

      if (@exists($data->url)) {
        $ogUrl = $data->url;
      }
    }

    $this->renderMetaDescription($data);
    $this->renderMetaKeywords($data);
    $this->renderMetaOgTags($title, $description, $ogImage, $ogUrl);
  }


  /***************************************** PAGE ELEMENTS *****************************************/


  protected function renderPageTitle($data) {

    if (@exists($data->title)) {
      $title = $data->title;
    }
    else if (@exists($data->name)) {
      $title = $data->name;
    }

    echo '<h1 class="page-title">' . $title . '</h1>';
  }

  protected function renderPageSubtitle($data) {

    if (@exists($data->subtitle)) {
      echo '<h3 class="page-subtitle">' . $data->subtitle . '</h3>';
    }
  }

  protected function renderPageContent($data) {

    if (@exists($data->content)) {
      echo '<div class="page-content">' . $data->content . '</div>';
    }
  }

  protected function renderPageMainImage($image, $name) {

    if (Util::mediaImageExists($image)) {

      echo '<div class="main-image">';
        echo '<img src="' . Conf::get('media_url') . '/' . $image . '" alt="' . $name . '" />';
      echo '</div>';
    }
  }

  protected function renderLangGroupIdHiddenField($data) {

    $langGroupId = @exists($data->langGroupId) ? $data->langGroupId : $data->id;

    echo '<input type="hidden" id="langGroupId" value="' . $langGroupId . '" />';
  }


  /**************************************** MEDIA, GALLERIES ****************************************/

  public function setGalleryItemProperties($item) {

    $image = '';
    $fancyBoxUrl = null;

    if((string)$item->type === 'media') {
      $image .= '<img src="' . $item->url . '" alt="' . $item->value . '">';
      $fancyBoxUrl = $item->url;
    }
    else if((string)$item->type === 'youtube_video') {
      $image .= '<img src="' . $item->imageUrl . '" alt="' . $item->value . '">';
      $image .= '<img src="' . Conf::get('url') . '/css/img/icon-youtube.png" class="youtube-icon element-center" alt="youtube icon">';
      $fancyBoxUrl = $item->watchUrl;
    }

    return (object) array('image' => $image, 'fancyBoxUrl' => $fancyBoxUrl);
  }


  public function renderGallery($gallery) {

    $html = '';

    if(@exists($gallery)) {

      $html .= '<div class="gallery nc-row nc-cols-5">';

        foreach ($gallery as $item) {

          $galleryProps = $this->setGalleryItemProperties($item);

          $description = isset($item->description) ? $item->description : '';

          $html .= '<div class="nc-col">';
            $html .= '<div class="item">';
              $html .= '<a href="' . $galleryProps->fancyBoxUrl . '" data-fancybox="gallery" data-caption="' . $description . '">';
                $html .= '<div class="image-wrapper">';
                  $html .= $galleryProps->image;
                $html .= '</div>';
              $html .= '</a>';
            $html .= '</div>';
          $html .= '</div>';
        }

      $html .= '</div>';
    }

    return $html;
  }

  protected function displayGallery($gallery) {
    echo $this->renderGallery($gallery);
  }

  public function renderAvatarImage($data) {

    if(Util::mediaImageExists($data->image)) {
      echo '<img src="' . Conf::get('media_url') . '/' . $data->image . '" alt="' . $data->image . '" class="avatar-image" />';
    }
    else {
      echo '<i class="fas fa-user"></i>';
    }
  }


  /***************************************** SYS ITEMS ******************************************/

  public function renderCategory($category) {

    if (@exists($category)) {

      if(is_array($category)) $category = (object)$category;

      $image = Util::setMediaImageUrl($category->introImage);

      echo '<div class="nc-col item">';

        echo '<a href="' . $category->url . '">';
          echo '<div class="image-wrapper">';
            echo '<img src="' . $image . '" alt="' . $category->title . '">';
          echo '</div>';
        echo '</a>';

        echo '<div class="content">';
          if (@exists($category->title)) echo '<h4 class="title">' . $category->title . '</h4>';
          if (@exists($category->introText)) echo '<p class="intro-text">' . $category->introText . '</p>';
          echo '<a href="' . $category->url . '" class="more">' . Trans::get('Read more') . '</a>';
        echo '</div>';

      echo '</div>';
    }
  }

  public function renderArticle($article) {

    if (@exists($article)) {

      if(is_array($article)) $article = (object)$article;

      $image = Util::setMediaImageUrl($article->introImage);

      echo '<div class="nc-col item">';

        echo '<a href="' . $article->url . '">';
          echo '<div class="image-wrapper">';
            echo '<img src="' . $image . '" alt="' . $article->title . '">';
          echo '</div>';
        echo '</a>';

        echo '<div class="content">';
          if (@exists($article->title)) echo '<h4 class="title">' . $article->title . '</h4>';
          if (@exists($article->introText)) echo '<p class="intro-text">' . $article->introText . '</p>';
          echo '<a href="' . $article->url . '" class="more">' . Trans::get('Read more') . '</a>';
        echo '</div>';

      echo '</div>';
    }
  }


  /******************************************* SEARCH ********************************************/


  public function displaySearchForm() {

    echo '<div class="page-overlay" id="searchOverlay">';

      echo '<button type="button" id="closeSearchForm"><i class="fa fa-times"></i></button>';

        echo '<div class="search-form-wrapper clearfix element-center">';

          echo '<h3>' . Trans::get('Search') . '</h3>';

          echo '<form method="post" action="' . Conf::get('url') . '/' . Trans::get('search-results') . '" id="searchForm">';

            echo '<input type="text" name="search" class="field" />';
            echo '<button type="submit"><i class="fa fa-search"></i></button>';

          echo '</form>';

      echo '</div>';

    echo '</div>';
  }


  /****************************************** LANGUAGES ******************************************/

  public function displayLanguages($languages) {

    if (Languages::enabled()) {

      echo '<div class="languages">';
      foreach ($languages as $lang) {
          $langName = '';
          if ($lang->aliasNameTranslated === 'sr'  ) {
              $langName = 'Latinica';
          }else if( $lang->aliasNameTranslated === 'ср'){
              $langName = 'Латиница';
          }else if( $lang->aliasNameTranslated === 'ср ћир'){
              $langName = 'Ћирилица';
          }elseif ($lang->aliasNameTranslated === 'sr ćir') {
              $langName = 'Ćirilica';
          }
        $activeClass = (int)$lang->id === (int)Trans::getLanguageId() ? ' active' : '';

        echo '<a href="#" class="set-language' . $activeClass . '" data-id="' . $lang->id . '">' . $langName . '</a>';
        echo '<span>|</span>';
      }
      echo '</div>';
    }
  }

  /******************************************* COMMENTS ********************************************/

  public function renderCommentsSection($comments, $item) {

    if (@exists($comments)) {

      echo '<div class="comments" id="pageComments">';
        $this->renderComments($comments, $item);
      echo '</div>';
    }
  }


  public function renderComments($comments, $item, $child = null) {

    if (@exists($comments)) {

      foreach ($comments as $comment) {

        $childClass = @exists($child) ? ' child' : '';

        echo '<div class="comment clearfix' . $childClass . '">';

          echo '<div class="avatar"><i class="fa fa-user"></i></div>';

          echo '<div class="content">';
            echo '<span>' . $comment['name'] . '</span>';
            echo '<span class="date">' . Util::formatDate($comment['cdate'], 'd.m.Y. | h:i') . '</span>';
            echo '<div class="message">' . nl2br($comment['message']) . '</div>';
          echo '</div>';

          echo '<div class="clearfix"></div>';

          echo '<div class="comment-reply-wrapper">';
            echo '<button type="button" class="comment-reply" data-target_id="' . $item->id . '" data-parent_id="' . $comment['id'] . '" data-type_id="' . Conf::get('comment_type_id')['article'] . '">' . Trans::get('Reply') . '</button>';
          echo '</div>';

          if (@exists($comment['children']) && count($comment['children']) > 0) {
            $this->renderComments($comment['children'], $item, true);
          }

        echo '</div>';
      }
    }
  }


  public function renderCommentsForm($id, $type) {

    echo '<div class="comments-form-wrapper" id="pageCommentForm">';

      echo '<form class="add-comment-form" data-type="comment">';

        echo Form::field('hidden', 'target_id', $id, array('className' => 'comment-target_id'));
        echo Form::field('hidden', 'parent_id', 0, array('className' => 'comment-parent_id'));
        echo Form::field('hidden', 'type_id', $type, array('className' => 'comment-type_id'));

        echo '<h3>' . Trans::get('Leave a comment') . ':</h3>';
        echo '<p>' . Trans::get('Your email address will not be published') . '</p>';

        echo Form::label(Trans::get('Name'));
        echo Form::field('text', 'name', '', array('className' => 'comment-name', 'required' => true));

        echo Form::label(Trans::get('E-mail'));
        echo Form::field('email', 'email', '', array('className' => 'comment-comment-email', 'required' => true));

        echo Form::label(Trans::get('Comment'));
        echo Form::textarea('message', '', array('className' => 'comment-message', 'required' => true));

        echo '<div class="post-button-wrapper">';
          echo '<button type="submit">' . Trans::get('Post comment') . '</button>';
        echo '</div>';

      echo '</form>';

    echo '</div>';
  }


  /***************************************** INITIALS ICON *****************************************/

  public function setInitials($string, $initialsCount = null) {

    if(!exists($string)) {
      return '';
    }

    if(!@exists($initialsCount)) {
      $initialsCount = 1;
    }

    $stringParts = explode(" ", $string);
    $partsCount = count($stringParts);

    if((int)$initialsCount === 1) {
      return substr($stringParts[0], 0, 1);
    }

    if((int)$initialsCount > 1) {

      if((int)$partsCount === 1) {
        return substr($stringParts[0], 0, 1);
      }
      else if((int)$partsCount > 1) {
        return substr($stringParts[0], 0, 1) . substr($stringParts[1], 0, 1);
      }
    }
  }

  public function renderInitialsIcon($string, $initialsCount = null) {

    $initials = $this->setInitials($string, $initialsCount);

    echo '<div class="initialsIcon">' . $initials . '</div>';
  }


  /******************************************* OTHER ********************************************/

}
?>