<?php

namespace Tasteez\Models;

class Favourite extends Model {

  public function getAll($userID) {
    $stmt = $this->db->prepare("SELECT recipe_id, name, thumbnail
      FROM favorites
      INNER JOIN meals
      ON  meals.id = favorites.recipe_id
      WHERE user_id = :user_id");

    $stmt->execute(array(':user_id' => $userID));

    return $stmt->fetchAll();
  }

  public function addFavourite($userID, $mealID) {

    $stmt = $this->db->prepare("INSERT
      INTO favorites (user_id, recipe_id)
      VALUES (:user_id, :meal_id)");

    $user_id = filter_input(INPUT_GET, 'userID', FILTER_SANITIZE_NUMBER_INT);
    $meal_id = filter_input(INPUT_GET, 'mealID', FILTER_SANITIZE_NUMBER_INT);

    $stmt->execute(array(':user_id' => $userID,':meal_id' => $mealID));

    return $stmt;
  }

  public function removeFavourite($userID, $mealID) {
    $stmt = $this->db->prepare("DELETE
      FROM favorites
      WHERE recipe_id = :meal_id
      AND user_id = :user_id");

    $stmt->execute(array(':user_id' => $userID,':meal_id' => $mealID));

    return $stmt;
  }

  public function isFavourite($userID, $mealID) {
    $stmt = $this->db->prepare("SELECT *
      FROM favorites
      WHERE recipe_id = :meal_id
      AND user_id = :user_id");

    $stmt->execute(array(':user_id' => $userID,':meal_id' => $mealID));

    return count($stmt->fetchAll()) > 0;
  }

}
