<?php
namespace Tasteez\Models;

class Model {
  public $db;

  function __construct($db) {
    $this->db = $db;
  }

  public function getTableName() {
    $className = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
    return strtolower($className) . "s";
  }

  public function findAll($offset = null, $limit = null) {
    $query = "SELECT * FROM " . $this->getTableName();

    if (isset($offset) && isset($limit)) {
        $query .= " LIMIT ${offset}, ${limit}";
    }

    return $this->db->query($query)->fetchAll();
  }

  public function findById($id) {
    $query = "SELECT * FROM ".$this->getTableName()." WHERE id=".$id;

    return $this->db->query($query)->fetchAll()[0];
  }

  public function query($query) {
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
  }


}
