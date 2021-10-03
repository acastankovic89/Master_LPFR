<?php

class CategoriesService extends Service {

  private $categoriesModel;

  public function __construct() {
    $categoriesModel = Categories::Instance();
    if ($categoriesModel instanceof Categories) {
      $this->categoriesModel = $categoriesModel;
    }
  }

  public function insert($data) {

    $alias = Util::formatCleanUrl($data['name']);
    $aliases = $this->categoriesModel->load();

    $data['alias'] = $this->setAlias($alias, $aliases);

    $this->categoriesModel->insert($data);

    $id = $this->categoriesModel->lastInsertId();

    if(!@exists($data['lang_group_id'])) {

      $this->categoriesModel->updateLangGroupId($id);
    }

    return $id;
  }

  public function update($data) {

    $alias = Util::formatCleanUrl($data['name']);
    $aliases = $this->categoriesModel->load();

    $data['alias'] = $this->setAlias($alias, $aliases, $data['id']);

    $this->categoriesModel->update($data);
  }

  public function getGroup($id, $fetchWithUnpublished) {

    $langGroup = null;

    $item = $this->categoriesModel->getOne($id, $fetchWithUnpublished);

    if(@exists($item) && (bool)$item !== false) {

      $langGroupId = $this->setLanguageGroupId($item);

      $langGroup = $this->categoriesModel->getByLangGroupId($langGroupId, $fetchWithUnpublished);
    }

    return $this->setGroupItemsByLang($langGroup);
  }

  public static function buildUrl($category, $langId = null) {

    $categories = Categories::getAllCategories($langId);

    if (isset($category)) {

      $alias = $category->alias;
      $parentId = $category->parent_id;

      $maxCount = 20;
      while ($parentId != 0 && $maxCount > 0) {

        $category = null;
        foreach ($categories as $cat) {
          if ($cat->id == $parentId) {
            $category = $cat;
          }
        }

        if (isset($category)) {
          $parentId = $category->parent_id;
          $alias = $category->alias . '/' . $alias;
        }
        $maxCount--;
      }

      return Conf::get('url') . '/' . $alias;
    }

    return Conf::get('url');
  }

  public function setItemProperties($item, $data = null) {

    if (@exists($item) && $item != false) {

      $item->url = $this->buildUrl($item);

      $item->gallery = $this->setGallery($item);
    }

    return $item;
  }

  public function setItem($item, $params = null, $setItemProps = null) {

    if(@exists($item) && $item) {
      if(@exists($setItemProps) && $setItemProps) {
        $this->setItemProperties($item, $params);
      }
      $category = new Categories();
      $category->map($item);
      return $category;
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
        $category = new Categories();
        $category->map($item);
        array_push($data, $category);
      }

      return $data;
    }

    return null;
  }

}
?>