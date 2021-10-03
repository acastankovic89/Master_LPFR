<?php


class Comments extends Model {

  public function __construct() {
    parent::__construct();
    $this->setTable('comments');
  }


  /************************************ FETCH ************************************/

  public function getOne($data) {

    $sql = 'SELECT * FROM `comments` WHERE `id` = :id';

    return $this->exafe($sql, array('id' => $data['id']));
  }


  public function getAll() {

    $sql = 'SELECT * FROM `comments`';

    return $this->exafeAll($sql);
  }


  public function getByTypeIdAndTargetId($typeId, $targetId, $fetchWithUnpublished = null) {

    $sql = 'SELECT * FROM `comments` WHERE `type_id` = :type_id AND `target_id` = :target_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `published` = 1';
    }

    return $this->exafeAll($sql, array('type_id' => $typeId, 'target_id' => $targetId));
  }


  public function publish($id, $published) {

    $sql = 'UPDATE `comments` SET `published` = :published WHERE `id` = :id';
    return $this->execute($sql, array('published' => $published, 'id' => $id));
  }
}

?>