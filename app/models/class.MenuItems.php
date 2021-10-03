<?php


final class MenuItemTypes {
  const ARTICLE = 1;
  const CATEGORY = 2;
  const EXTERNAL_LINK = 3;
  const SEPARATOR = 4;
}

class MenuItems extends Model {

  private $selectQueryString;
  private $selectQueryCountString;
  private $orderByString;

  public function __construct() {
    parent::__construct();
    $this->setTable('menu_items');
    $this->setQueryStrings();
  }


  /************************************ FETCH ************************************/

  public function getTotal($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 'mi', 'columnName' => 'name'),
      array('columnAlias' => 'mi2', 'columnName' => 'name')
    );

    return $this->getTotalItems($data, $columns, $this->selectQueryCountString, $whereColumns);
  }

  public function getWithFilters($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 'mi', 'columnName' => 'name'),
      array('columnAlias' => 'mi2', 'columnName' => 'name')
    );

    return $this->getItemsWithFilters($data, $columns, $this->selectQueryString, $whereColumns);
  }

  public function getOne($id) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `mi`.`id` = :id';
    return $this->exafe($sql, array('id' => $id));
  }


  public function getAll() {

    $sql = $this->selectQueryString;
    $sql .= $this->orderByString;
    return $this->exafeAll($sql);
  }


  public function getByMenuId($menuId) {

    $sql  = $this->selectQueryString;
    $sql .= ' WHERE `mi`.`menu_id` = :menu_id';
    $sql .= $this->orderByString;

    return $this->exafeAll($sql, array('menu_id' => $menuId));
  }


  public function getByMenuIdAndLangId($menuId, $langId) {

    $sql  = $this->selectQueryString;
    $sql .= ' WHERE (`m`.`id` = :menu_id OR `m`.`lang_group_id` = :menu_id) AND `m`.`lang_id` = :lang_id';
    $sql .= $this->orderByString;

    return $this->exafeAll($sql, array('menu_id' => $menuId, 'lang_id' => $langId));
  }


  /************************************ ACTIONS ************************************/


  public function resetParentId($parentId) {

    $sql = 'UPDATE `menu_items` SET `parent_id` = 0 WHERE `parent_id` = :parent_id;';
    $this->execute($sql, array('parent_id' => $parentId));
  }

  public function resetMenuId($menuId) {

    $sql = 'UPDATE `menu_items` SET `menu_id` = 0 WHERE `menu_id` = :menu_id;';
    $this->execute($sql, array('menu_id' => $menuId));
  }

  public function updateParentId($id, $parentId) {

    return $this->update(array('id' => $id, 'parent_id' => $parentId));
  }

  private function updatePositionByMenuId($id, $position, $menuId) {

    $sql = 'UPDATE `menu_items` SET `rang` = :rang WHERE `id` = :id AND `menu_id` = :menu_id';
    return $this->execute($sql, array('id' => $id, 'rang' => $position, 'menu_id' => $menuId));
  }

  private function updatePosition($id, $position) {

    $sql = 'UPDATE `menu_items` SET `rang` = :rang WHERE `id` = :id';
    return $this->execute($sql, array('id' => $id, 'rang' => $position));
  }

  public function updatePositions($id, $position) {

    $menu_item = $this->getOne($id);

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `mi`.`parent_id` = :parent_id AND `mi`.`id` != :id AND `m`.`id` = :menu_id';
    $sql .= $this->orderByString;

    $menuItems = $this->exafeAll($sql, array('id' => $id, 'parent_id' => $menu_item->parent_id, 'menu_id' => $menu_item->menu_id));

    $this->updatePosition($id, $position);

    $counter = 0;
    foreach ($menuItems as $item) {

      if ((int)$counter === (int)$position) {
        $counter++;
      }

      if ((int)$counter !== (int)$item->rang) {

        $this->updatePosition($item->id, $counter);
      }

      $counter++;
    }
  }

  /************************************ OTHER ************************************/


  private function setQueryStrings() {

    $this->selectQueryString = 'SELECT `mi`.*, 
                                `mi2`.`name` AS `parent_name`,
                                `m`.`name` AS `menu_name`, `m`.`lang_group_id` AS `menu_lang_group_id`, `m`.`lang_id` AS `menu_lang_id`
                                FROM `menu_items` AS `mi`
                                LEFT JOIN `menus` AS `m` ON `m`.`id` = `mi`.`menu_id`
                                LEFT JOIN `menu_items` AS `mi2` ON `mi2`.`id` = `mi`.`parent_id`';

    $this->selectQueryCountString = 'SELECT COUNT(`mi`.`id`) AS `total` FROM `menu_items` AS `mi` LEFT JOIN `menus` AS `m` ON `m`.`id` = `mi`.`menu_id`';

    $this->orderByString = ' ORDER BY `mi`.`rang`';
  }
}

?>