<?php

class ArticlesService extends Service {

  private $articlesModel;

  public function __construct() {
    $articlesModel = Articles::Instance();
    if ($articlesModel instanceof Articles) {
      $this->articlesModel = $articlesModel;
    }
  }

  public function insert($data) {

    $alias = Util::formatCleanUrl($data['title']);
    $aliases = $this->articlesModel->load();

    $data['alias'] = $this->setAlias($alias, $aliases);

    if (@exists($data['event_date'])) {
      $data['event_date'] = Util::formatDate($data['event_date'], 'Y-m-d');
    }

    if (@exists($data['publish_date'])) {
      $data['publish_date'] = Util::formatDate($data['publish_date'], 'Y-m-d');
    }

    $this->articlesModel->insert($data);

    $id = $this->articlesModel->lastInsertId();

    if(!@exists($data['lang_group_id'])) {

      $this->articlesModel->updateLangGroupId($id);
    }

    return $id;
  }

  public function update($data) {

    $alias = Util::formatCleanUrl($data['title']);
    $aliases = $this->articlesModel->load();

    $data['alias'] = $this->setAlias($alias, $aliases, $data['id']);

    if (@exists($data['event_date'])) {
      $data['event_date'] = Util::formatDate($data['event_date'], 'Y-m-d');
    }

    if (@exists($data['publish_date'])) {
      $data['publish_date'] = Util::formatDate($data['publish_date'], 'Y-m-d');
    }

    $this->articlesModel->update($data);
  }

  private function buildUrl($item) {

    $categories = Categories::getAllCategories();

    $url = '';
    if (isset($item->alias)) $url = $item->alias;

    if (isset($item->category_id)) {

      $categoryId = $item->category_id;
      foreach ($categories as $cat) {
        if ($cat->id == $categoryId) $category = $cat;
      }

      if (isset($category)) {

        if ($category->alias) $url = $category->alias . '/' . $url;

        while ($category->parent_id != 0) {

          foreach ($categories as $cat) {

            if ($cat->id == $category->parent_id) {

              $url = $cat->alias . '/' . $url;
              $category = $cat;
            }
          }
        }
      }
    }

    return Conf::get('url') . '/' . $url;
  }

  public function setItemProperties($item, $data = null) {

    if (@exists($item) && $item != false) {

      $item->url = $this->buildUrl($item);

      $item->gallery = $this->setGallery($item);

      if (@exists($data) && @exists($data['shortcodes'])) {
        $gs = GalleriesService::Instance();
        if ($gs instanceof GalleriesService) $galleriesServices = $gs;
        $item->content = $galleriesServices->parseGalleryShortCodes($item->content);
      }
    }

    return $item;
  }

  public function getGroup($id, $fetchWithUnpublished) {

    $langGroup = null;

    $item = $this->articlesModel->getOne($id, $fetchWithUnpublished);

    if(@exists($item) && (bool)$item !== false) {

      $langGroupId = $this->setLanguageGroupId($item);
      $langGroup = $this->articlesModel->getByLangGroupId($langGroupId, $fetchWithUnpublished);
    }

    return $this->setGroupItemsByLang($langGroup);
  }

  public function setItem($item, $params = null, $setItemProps = null) {

    if(@exists($item) && $item) {
      if(@exists($setItemProps) && $setItemProps) {
        $this->setItemProperties($item, $params);
      }
      $article = new Articles();
      $article->map($item);
      return $article;
    }

    return null;
  }

  public function setItems($items, $params = null, $setItemProps = null) {

    if(@exists($items) && $items) {

      $data = array();
      foreach ($items as $item) {
        if(@exists($setItemProps) && $setItemProps) {
          $this->setItemProperties($item, $params);
        }
        $article = new Articles();
        $article->map($item);
        array_push($data, $article);
      }

      return $data;
    }

    return null;
  }
}
?>