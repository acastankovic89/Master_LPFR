<?php

class Model {

  protected static $instances;

  protected $dbh;
  protected $table;

  protected $tableDescription = array();
  protected $tableConstraints = array();

  protected $error;

  protected $useCache = false;
  protected $cachedData = array();

  protected $data = null;

  public function __construct(PDO $dbh = null) {
    if ($dbh != null) $this->dbh = $dbh;
    else $this->dbh = DB::Connect();
  }


  public static function Instance() {

    $class = get_called_class();

    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class;
    }
    return self::$instances[$class];
  }

  public function setTable($table) {
    $this->table = $table;
  }

  public function getTable() {
    return $this->table;
  }

  public function getDBH() {
    return $this->dbh;
  }

  public function setError($error) {
    $this->error = $error;
  }


  public function getError() {
    return $this->error;
  }

  public function useCache($ind) {
    $this->useCache = $ind;
  }

  public function lastInsertId() {
    return $this->dbh->lastInsertId();
  }

  public function required($data, $requirements) {

    $ok = true;
    foreach ($requirements as $requirement) {
      $found = false;
      foreach ($data as $key => $item) {
        if ($key == $requirement) $found = true;
      }
      if (!$found) {
        $ok = false;
        $this->setError($requirement . ' is required');
        break;
      }
    }
    return $ok;
  }

  //load table or a row
  public function load($id = null) {

    if (isset($id)) {
      $stm = $this->execute('SELECT * FROM ' . $this->table . ' WHERE `id` = :id;', array('id' => $id));
      $this->data = $this->fetch($stm, PDO::FETCH_OBJ);
      return $this->data;
    }
    else {
      $stm = $this->execute('SELECT * FROM ' . $this->table . ';');
      $this->data = $this->fetchAll($stm, PDO::FETCH_OBJ);
      return $this->data;
    }
  }

  //load last insert row
  public function loadLastInsert() {
    return self::load($this->lastInsertId());
  }

  //load table using array filter and order
  public function loadFilter($filter, $sort = null) {

    $query = 'SELECT * FROM ' . $this->table . ' WHERE ';
    foreach ($filter as $key => $value) {
      $query .= ' ' . $key . ' =: ' . $key . ' AND';
    }
    $query = rtrim($query, 'AND');

    if (isset($sort)) $query = $query . ' ORDER BY ' . $sort;

    return $this->exafeAll($query, $filter, PDO::FETCH_OBJ);
  }

  //load one row using array filter and order
  public function loadOneFilter($filter) {

    $query = 'SELECT * FROM ' . $this->table . ' WHERE ';
    foreach ($filter as $key => $value) {
      $query .= ' ' . $key . ' =: ' . $key . ' AND';
    }
    $query = rtrim($query, 'AND');

    return $this->exafe($query, $filter, PDO::FETCH_OBJ);
  }

  //load table filter
  public function existFilter($filter) {

    try {
      $query = 'SELECT COUNT(*) as num FROM ' . $this->table . ' WHERE ';
      foreach ($filter as $key => $value) {
        $query .= ' ' . $key . ' =: ' . $key . ' AND';
      }
      $query = rtrim($query, 'AND');

      $stm = $this->dbh->prepare($query);
      $stm->execute($filter);
      $this->data = $stm->fetch();
      if ($this->data['num'] > 0) return true;
      else return false;
    } catch (PDOException $e) {//echo $e->getMessage();
      $this->HandleDBError($e);
      return false;
    }
  }

  public function deleteFilter($filter) {

    try {
      $query = 'DELETE FROM ' . $this->table . ' WHERE ';
      foreach ($filter as $key => $value) {
        $query .= ' ' . $key . ' =: ' . $key . ' AND';
      }
      $query = rtrim($query, 'AND');

      $stm = $this->dbh->prepare($query);
      $stm->execute($filter);
      return true;
    } catch (PDOException $e) {
      echo $e->getMessage();
      $this->HandleDBError($e);
      return false;
    }
  }

  //init table description
  public function initTable($table = null) {

    if (isset($table)) $this->table = $table;

    try {

      $stm = $this->dbh->prepare('desc `' . $this->table . '`');
      $stm->execute();
    } catch (Exception $e) {
      $this->HandleDBError($e);
    }

    $ii = 0;
    while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
      $this->tableDescription[$row['Field']]['type'] = $this->GetType($row['Type']);
      $this->tableDescription[$row['Field']]['name'] = $row['Field'];
      $this->tableDescription[$row['Field']]['null'] = $row['Null'];
      $this->tableDescription[$row['Field']]['key'] = $row['Key'];
      $this->tableDescription[$row['Field']]['def'] = $row['Default'];
      $this->tableDescription[$row['Field']]['extra'] = $row['Extra'];
      $this->tableDescription[$row['Field']]['password'] = false;
      $this->tableDescription[$row['Field']]['size'] = $this->GetSize($row['Type']);
      $ii++;
    }

    $this->LoadForeignKeyConstrains();
  }

  private function getType($type) {

    if (!strpos($type, '(')) {
      return $type;
    }
    else {
      return substr($type, 0, strpos($type, '('));
    }
  }

  private function getSize($type) {

    if (!strpos($type, '(')) {
      return null;
    }
    else {
      return substr($type, strpos($type, '(') + 1, strpos($type, ')') - strpos($type, '(') - 1);
    }
  }

  public function getTableDescription() {
    return $this->tableDescription;
  }

  //load foreign key dependencies
  public function loadForeignKeyConstrains() {

    try {
      $stm = $this->dbh->prepare('SELECT k.COLUMN_NAME, i.TABLE_NAME, i.CONSTRAINT_TYPE, i.CONSTRAINT_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME 
                                        FROM information_schema.TABLE_CONSTRAINTS i 
                                        LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME 
                                        WHERE i.CONSTRAINT_TYPE = "FOREIGN KEY" 
                                        AND i.TABLE_SCHEMA = DATABASE()
                                        AND i.TABLE_NAME = "' . $this->table . '"');
      $stm->execute();
    } catch (PDOException $e) {
      $this->HandleDBError($e);
    }


    $ii = 0;
    while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
      $this->tableConstraints[$row['CONSTRAINT_NAME']]['type'] = $row['CONSTRAINT_TYPE'];
      $this->tableConstraints[$row['CONSTRAINT_NAME']]['column'] = $row['COLUMN_NAME'];
      $this->tableConstraints[$row['CONSTRAINT_NAME']]['refernce_column'] = $row['REFERENCED_COLUMN_NAME'];
      $this->tableConstraints[$row['CONSTRAINT_NAME']]['refernce_table'] = $row['REFERENCED_TABLE_NAME'];
      $this->tableDescription[$row['COLUMN_NAME']]['fk_name'] = $row['CONSTRAINT_NAME'];
      $ii++;
    }
  }

  public function loadForeignKeyTables() {

    foreach ($this->tableConstraints as $key => $value) {

      try {
        $stm = $this->dbh->prepare('SELECT * FROM ' . $this->tableConstraints[$key]['refernce_table'] . ';');
        $stm->execute();
      } catch (PDOException $e) {
        $this->HandleDBError($e);
      }

      $this->tableConstraints[$key]['data'] = $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    return $this->tableConstraints;
  }

  //build insert query
  public function buildInsertQuery($data) {

    $fieldsString = '(';
    $valuesString = '(';

    foreach ($this->tableDescription as $key => $field) {

      if ($field['name'] == 'id') {
        //do nothing for id
      }
      else if ($field['name'] == 'cdate') {
        $fieldsString .= 'cdate, ';
        $valuesString .= 'now(), ';
      }
      else if ($field['name'] == 'udate') {
        $fieldsString .= 'udate, ';
        $valuesString .= 'now(), ';
      }
      else {
        //data exist for this field
        if (isset($data[$field['name']])) {
          $fieldsString .= '`' . $field['name'] . '`' . ', ';
          $valuesString .= ':' . $field['name'] . ', ';
        }
      }
    }

    $fieldsString = rtrim($fieldsString, ', ');
    $fieldsString .= ')';
    $valuesString = rtrim($valuesString, ', ');
    $valuesString .= ')';

    $insertQuery = 'INSERT INTO ' . $this->table . ' ' . $fieldsString . ' VALUES ' . $valuesString . ';';

    return $insertQuery;
  }

  //build update query
  public function buildUpdateQuery($data, $filter = null) {

    $fieldsString = '';
    if ($filter == null) $whereString = ' WHERE `id` = :id';
    else {
      $whereString = ' WHERE ';
      $first = true;
      foreach ($filter as $key) {
        if (!$first) $whereString .= ' AND ';
        $first = false;
        $whereString .= $key . ' =:' . $key;
      }
    }

    foreach ($this->tableDescription as $key => $field) {

      $skip = false;

      if (isset($filter)) {
        foreach ($filter as $filterKey) {
          if ($key == $filterKey) $skip = true;
        }
      }

      if ($skip) {
      }
      else if ($field['name'] == 'id') {
        //$whereString = ' WHERE `id` = :id';
      }
      else if ($field['name'] == 'cdate') {
        //$fieldsString .= 'cdate, ';
        //$valuesString .= 'now(), ';
      }
      else if ($field['name'] == 'udate') {
        $fieldsString .= 'udate = now(), ';
      }
      else {
        //data exist for this field
        if (isset($data[$field['name']])) {
          $fieldsString .= '`' . $field['name'] . '`' . '=:' . $field['name'] . ', ';
        }
      }
    }

    $fieldsString = rtrim($fieldsString, ', ');

    $updateQuery = 'update ' . $this->table . ' set ' . $fieldsString . ' ' . $whereString . ';';

    return $updateQuery;
  }

  //build delete query
  public function buildDeleteQuery() {

    $whereString = ' WHERE `id` = :id';

    $deleteQuery = 'DELETE FROM ' . $this->table . $whereString . ';';

    return $deleteQuery;
  }

  //unset data not used
  public function unsetData($data) {

    foreach ($data as $key => $value) {

      if (!isset($this->tableDescription[$key])) {
        unset($data[$key]);
      }
      else if ($data[$key] === null) {
        unset($data[$key]);
      }
    }

    return $data;
  }

  //insert data
  public function insert($data) {

    if (is_object($data)) $data = (array)$data;

    if (count($this->tableDescription) == 0) $this->InitTable();

    $data = $this->unsetData($data);
    unset($data['id']);
    unset($data['cdate']);
    unset($data['udate']);
    $query = $this->buildInsertQuery($data);

    return $this->execute($query, $data);
  }

  //update data
  public function update($data, $filter = null) {

    if (is_object($data)) $data = (array)$data;

    if (count($this->tableDescription) == 0) $this->InitTable();

    $data = $this->unsetData($data);
    $query = $this->buildUpdateQuery($data, $filter);

    return $this->execute($query, $data);
  }

  //delete data by id
  public function delete($data) {

    $query = $this->buildDeleteQuery();

    if (gettype($data) !== 'array') {
      $id = $data;
      $data = array();
      $data['id'] = $id;
    }

    return $this->execute($query, array('id' => $data['id']));
  }

  public function LoadFull($id = null, $filter = null) {

    $joinCounter = 1;

    $selectFields = '';
    $leftJoins = '';
    foreach ($this->tableDescription as $key => $field) {

      $selectFields .= 'j.' . $key . ',';
      if (!isset($field['fk_name'])) {
        //$selectFields .= 'j.' . $key . ',';
      }
      else {
        foreach ($this->tableConstraints as $name => $data) {

          if ($name == $field['fk_name']) {
            $referenceTable = ' j' . $joinCounter;
            $leftJoins .= ' LEFT JOIN ' . $data['refernce_table'] . $referenceTable . ' on j.' . $data['column'] . ' = ' . $referenceTable . '.' . $data['refernce_column'];
            $selectFields .= $referenceTable . '.name as ' . $data['refernce_table'] . '_name' . ',';
          }

          $joinCounter++;
        }
      }
    }

    $selectFields = rtrim($selectFields, ',');
    $selectQuery = 'SELECT ' . $selectFields . ' FROM ' . $this->table . ' j ' . $leftJoins;

    //echo $selectQuery;
    if (isset($id)) {
      $stm = $this->execute($selectQuery, array('id' => $id));
      $this->data = $this->fetch($stm, PDO::FETCH_OBJ);
      return $this->data;
    }
    else {
      $stm = $this->execute($selectQuery);
      $this->data = $this->fetchAll($stm, PDO::FETCH_OBJ);
      return $this->data;
    }
  }

  //handle error
  protected function handleDBError(PDOException $e) {
    Logger::putError($e);
  }

  public function getData() {
    return $this->data;
  }

  public function getDataArray() {
    if (gettype($this->data) === 'object') return $this->data->fetchAll(PDO::FETCH_ASSOC);
  }

  public function setOrder($ids) {

    $rang = 1;
    foreach ($ids as $id) {

      try {
        $stm = $this->dbh->prepare('UPDATE ' . $this->table . ' SET `rang` = :rang WHERE `id` = :id');
        $stm->execute(array('rang' => $rang, 'id' => $id));
        $this->data = $stm;
      } catch (PDOException $e) {//echo $e->getMessage();
        $this->handleDBError($e);
        return false;
      }

      $rang++;
    }

    return true;
  }

  public function execute($sql, $data = null) {
    try {

      if (Conf::get('debug')) Logger::putTrace($sql);
      $stm = $this->dbh->prepare($sql);
      $stm->execute($data);
      return $stm;
    } catch (PDOException $e) {
      $this->HandleDBError($e);
      return false;
    }
  }

  public function fetchAll($stm, $type = null) {

    try {

      if (!isset($type)) $type = PDO::FETCH_OBJ;

      if (is_object($stm)) return $stm->fetchAll($type);
      else Logger::putError('Invalid query result');
    } catch (Exception $e) {
      $this->HandleDBError($e);
      return false;
    }
  }

  public function fetch($stm, $type = null) {

    try {

      if (!isset($type)) $type = PDO::FETCH_OBJ;

      if (is_object($stm)) return $stm->fetch($type);
      else Logger::putError('Invalid query result');
    } catch (Exception $e) {
      $this->HandleDBError($e);
      return false;
    }
  }

  //execute and fetch
  public function exafe($sql, $data = null, $type = null) {
    if (!isset($type)) $type = PDO::FETCH_OBJ;
    $stm = $this->execute($sql, $data);
    return $this->fetch($stm, $type);
  }

  //execute and fetch all
  public function exafeAll($sql, $data = null, $type = null) {
    if (!isset($type)) $type = PDO::FETCH_OBJ;
    $stm = $this->execute($sql, $data);
    return $this->fetchAll($stm, $type);
  }

  protected function setOrderByString($string, $alias = null) {

    $orderByArray = json_decode($string);

    if (@exists($orderByArray)) {

      $orderByString = 'ORDER BY';

      if (@exists($alias)) {
        $alias = trim($alias, '`');
        $alias = '`' . $alias . '`.';
      }
      else {
        $alias = '';
      }

      foreach ($orderByArray as $field => $direction) {

        $orderByString .= ' ' . $alias . '`' . $field . '`';

        if (@exists($direction)) {
          $orderByString .= ' ' . $direction;
        }

        $orderByString .= ', ';
      }
    }

    return ' ' . trim($orderByString, ', ');
  }


  public function getTotalItems($params = null, $columns = null, $selectString = null, $whereColumns = null) {

    if (!@exists($selectString)) {
      $selectString = 'SELECT COUNT(`id`) as `total` FROM `' . $this->table . '`';
    }

    $whereString = '';

    if (@exists($params)) {

      if (@exists($params['search'])) {

        if (@exists($columns)) {

          $whereString .= ' (';

          foreach ($columns as $key => $prop) {

            if ((int)$key !== 0) $whereString .= ' OR';

            if (@exists($prop['columnAlias'])) {

              $whereString .= '`' . $prop['columnAlias'] . '`.`' . $prop['columnName'] . '` LIKE :search';
            }
            else {
              $whereString .= '`' . $prop['columnName'] . '` LIKE :search';
            }
          }

          $whereString .= ' )';
        }
      }

      if (@exists($whereColumns)) {

        $whereColumnsString = '';

        foreach ($whereColumns as $col) {

          if (@exists($col['columnAlias'])) {
            $whereColumnsString .= '`' . $col['columnAlias'] . '`.';
          }

          $whereColumnsString .= '`' . $col['columnName'] . '` = :' . $col['columnName'] . ',';
        }

        $whereColumnsString = rtrim($whereColumnsString, ',');

        $whereString .= ' AND ' . $whereColumnsString;
      }
      else {

        if (@exists($params['menu_id'])) {
          $whereString .= ' AND `mi`.`menu_id` = :menu_id';
        }

        if (@exists($params['slider_id'])) {
          $whereString .= ' AND `si`.`slider_id` = :slider_id';
        }

        if (@exists($params['mime_type'])) {
          $whereString .= ' AND `mime_type` = :mime_type';
        }
      }
    }

    $whereString = trim($whereString, ' AND');

    if ((string)$whereString !== '') {
      $whereString = ' WHERE ' . $whereString;
    }

    $sql = $selectString . $whereString;

    $stm = $this->dbh->prepare($sql);

    if (@exists($params)) {

      if (@exists($params['search'])) {
        $stm->bindValue(':search', (string)'%' . $params['search'] . '%', PDO::PARAM_STR);
      }

      if (@exists($whereColumns)) {

        foreach ($whereColumns as $col) {

          $value = (string)$col['value'];
          $paramType = PDO::PARAM_STR;

          if ((string)$col['type'] === 'int') {
            $value = (int)$col['value'];
            $paramType = PDO::PARAM_INT;
          }

          $stm->bindValue($col['columnName'], $value, $paramType);
        }
      }
      else {

        if (@exists($params['menu_id'])) {
          $stm->bindValue(':menu_id', (int)$params['menu_id'], PDO::PARAM_INT);
        }

        if (@exists($params['slider_id'])) {
          $stm->bindValue(':slider_id', (int)$params['slider_id'], PDO::PARAM_INT);
        }

        if (@exists($params['mime_type'])) {
          $stm->bindValue(':mime_type', (string)$params['mime_type'], PDO::PARAM_STR);
        }
      }
    }

    $stm->execute();

    $result = $stm->fetch(PDO::FETCH_OBJ);

    return (int)$result->total;
  }


  public function getItemsWithFilters($params = null, $columns = null, $selectString = null, $whereColumns = null) {

    $limit = @exists($params['items_per_page']) ? $params['items_per_page'] : Conf::get('items_per_page')['admin_table'];
    $page = @exists($params['page']) ? $params['page'] : 1;
    $offset = @exists($page) && $page > 0 ? ($page - 1) * $limit : 0;

    $whereString = '';
    $orderByString = '';

    if (@exists($params)) {

      if (@exists($params['search'])) {

        if (@exists($columns)) {

          $whereString .= ' AND (';

          foreach ($columns as $key => $prop) {

            if ((int)$key !== 0) $whereString .= ' OR';

            if (@exists($prop['columnAlias'])) {

              $whereString .= '`' . $prop['columnAlias'] . '`.`' . $prop['columnName'] . '` LIKE :search';
            }
            else {
              $whereString .= '`' . $prop['columnName'] . '` LIKE :search';
            }
          }

          $whereString .= ' )';
        }
      }

      if (@exists($whereColumns)) {

        $whereColumnsString = '';

        foreach ($whereColumns as $col) {

          if (@exists($col['columnAlias'])) {
            $whereColumnsString .= '`' . $col['columnAlias'] . '`.';
          }

          $whereColumnsString .= '`' . $col['columnName'] . '` = :' . $col['columnName'] . ',';
        }

        $whereColumnsString = rtrim($whereColumnsString, ',');

        $whereString .= ' AND ' . $whereColumnsString;
      }
      else {

        if (@exists($params['menu_id'])) {
          $whereString .= ' AND `mi`.`menu_id` = :menu_id';
        }

        if (@exists($params['slider_id'])) {
          $whereString .= ' AND `si`.`slider_id` = :slider_id';
        }

        if (@exists($params['mime_type'])) {
          $whereString .= ' AND `mime_type` = :mime_type';
        }
      }

      if (@exists($params['order_by'])) {
        $orderByString .= ' ORDER BY ' . $params['order_by'];
      }

      if (@exists($params['order_direction'])) {
        $orderByString .= ' ' . $params['order_direction'];
      }
    }

    $whereString = trim($whereString, ' AND');

    if ((string)$whereString !== '') {
      $whereString = ' WHERE ' . $whereString;
    }

    if (!@exists($selectString)) {
      $selectString = 'SELECT * FROM `' . $this->table . '` ';
    }

    $sql = $selectString . $whereString . $orderByString . ' LIMIT :offset, :limit';

    $stm = $this->dbh->prepare($sql);

    if (@exists($params)) {

      if (@exists($params['search'])) {
        $stm->bindValue(':search', (string)'%' . $params['search'] . '%', PDO::PARAM_STR);
      }

      if (@exists($whereColumns)) {

        foreach ($whereColumns as $col) {

          $value = (string)$col['value'];
          $paramType = PDO::PARAM_STR;

          if ((string)$col['type'] === 'int') {
            $value = (int)$col['value'];
            $paramType = PDO::PARAM_INT;
          }

          $stm->bindValue($col['columnName'], $value, $paramType);
        }
      }
      else {

        if (@exists($params['menu_id'])) {
          $stm->bindValue(':menu_id', (int)$params['menu_id'], PDO::PARAM_INT);
        }

        if (@exists($params['slider_id'])) {
          $stm->bindValue(':slider_id', (int)$params['slider_id'], PDO::PARAM_INT);
        }

        if (@exists($params['mime_type'])) {
          $stm->bindValue(':mime_type', (string)$params['mime_type'], PDO::PARAM_STR);
        }
      }
    }

    $stm->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stm->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stm->execute();

    return $stm->fetchAll(PDO::FETCH_OBJ);
  }

}
?>