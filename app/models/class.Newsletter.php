<?php


class Newsletter extends Model {

  public function __construct() {
    parent::__construct();
    $this->setTable('newsletter');
  }


  /************************************ FETCH ************************************/


  public function getOne($id) {

    $sql = 'SELECT * FROM `newsletter` WHERE `email` = :id';

    return $this->exafe($sql, array('id' => $id));
  }


  public function getAll() {

    $sql = 'SELECT * FROM `newsletter`';

    return $this->exafeAll($sql);
  }


  public function getByEmail($email) {

    $sql = 'SELECT * FROM `newsletter` WHERE `email` = :email';

    return $this->exafe($sql, array('email' => $email));
  }
}

?>