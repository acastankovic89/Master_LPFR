<?php

class MenusService extends Service {

  private $menusModel;
  private $menuItemsModel;

  public function __construct() {

    $menusModel = Menus::Instance();
    if ($menusModel instanceof Menus) {
      $this->menusModel = $menusModel;
    }

    $menuItemsModel = MenuItems::Instance();
    if ($menuItemsModel instanceof MenuItems) {
      $this->menuItemsModel = $menuItemsModel;
    }
  }

  public function buildArticleUrl($categories, $articles, $id) {

    $alias = '';
    $article = null;
    foreach ($articles as $art) {
      if ($art->id == $id) $article = $art;
    }

    if (isset($article)) {

      $alias = $article->alias;
      $parentId = $article->category_id;

      $maxCount = 20;
      while ((int)$parentId !== 0 && $maxCount > 0) {
        $category = null;
        foreach ($categories as $cat) {
          if ((int)$cat->id === (int)$parentId) {
            $category = $cat;
          }
        }

        if (isset($category)) {
          $parentId = $category->parent_id;
          $alias = $category->alias . '/' . $alias;
        }

        $maxCount--;
      }
    }

    return Conf::get('url') . '/' . $alias;
  }


  public function buildCategoryUrl($categories, $id) {

    $alias = '';
    $category = null;
    $maxCount = 20;
    while ((int)$id !== 0 && $maxCount > 0) {
      foreach ($categories as $cat) {
        if ($cat->id == $id) {
          $category = $cat;
          $id = $category->parent_id;
          $alias = $category->alias . '/' . $alias;
        }
      }
      $maxCount--;
    }

    if ($maxCount == 0) Logger::put('Cat url build error, cat id ' . $id . ', alias ' . $alias);
    return Conf::get('url') . '/' . $alias;
  }


  public function setValues($data) {

    if($data['type'] == MenuItemTypes::ARTICLE || $data['type'] == MenuItemTypes::CATEGORY) {
      $data['url'] = '';
    } else {
      $data['target_id'] = 0;
    }

    return $data;
  }


  public function getGroup($id) {

    $langGroup = null;

    $item = $this->menusModel->getOne($id);

    if(@exists($item) && (bool)$item !== false) {

      $langGroupId = $this->setLanguageGroupId($item);
      $langGroup = $this->menusModel->getByLangGroupId($langGroupId);
    }

    return $this->setGroupItemsByLang($langGroup);
  }


  public function insert($data) {

    $this->menusModel->insert($data);

    $id = $this->menusModel->lastInsertId();

    if(!@exists($data['lang_group_id']) || (int)$data['lang_group_id'] === 0) {

      $this->menusModel->updateLangGroupId($id);
    }

    return $id;
  }

}
?>