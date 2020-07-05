<?php

namespace Tasteez\Models;

class Category extends Model {

  public function getAll($offset = null, $limit = null) {
    $query = "SELECT id, category, image FROM categories";
    if (isset($offset) && isset($limit)) {
        $query .= " LIMIT ${offset}, ${limit}";
    }
    
    return $this->db->query($query)->fetchAll();
  }

}
