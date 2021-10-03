<?php


class CommentsServices extends Service {

  private $model;

  public function __construct() {

    $model = Comments::Instance();
    if ($model instanceof Comments) {
      $this->model = $model;
    }
  }


  public function loadItemsChildren($id) {

    $results = $this->model->getAll();

    $nodeTree = Util::formTree($results, $id);

    $childrenNodes = array();

    if (@exists($nodeTree[0]['children'])) {

      $children = $nodeTree[0]['children'];
      $childrenNodes = $this->findNodesChildren($children);
    }

    return $childrenNodes;
  }


  public function delete($id) {

    $children = $this->loadItemsChildren($id);

    if (@exists($children)) {

      foreach ($children as $child) {

        if (is_array($child)) $child = (object)$child;

        $this->model->delete($child->id);
      }
    }

    $this->model->delete($id);
  }


  public function publish($data) {

    // change published value
    $data['published'] = (int)$data['published'] === 1 ? 0 : 1;

    $this->model->update($data);

    return $this->model->getOne($data);
  }


  /************************************ OTHER ************************************/


  private function findNodesChildren($items, $nodes = null) {

    if (!isset($nodes)) {
      $nodes = array();
    }

    foreach ($items as $item) {

      array_push($nodes, $item);

      if (isset($item['children'])) {

        $nodes = $this->findNodesChildren($item['children'], $nodes);
      }
    }

    return $nodes;
  }
}

?>