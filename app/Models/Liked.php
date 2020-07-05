<?php

namespace Tasteez\Models;

class Liked extends Model {

  public function getMealLikes($mealID, $userID = 0) {
    $totalLikes = 0;
    $totalDislikes = 0;
    $mealLiked = 0;
    $likes = $this->db->query("SELECT * FROM likes WHERE recipe_id = ${mealID}")->fetchAll();

    foreach ($likes as $like) {

      if ((int)$like['like_value'] == 1) {
        $totalLikes++;

        if ($userID) {
          if ($like['user_id'] == $userID) {
            $mealLiked = 1;
          }
        }

      } elseif ((int)$like['like_value'] == -1) {
        $totalDislikes++;

        if ($userID) {
          if ($like['user_id'] == $userID) {
            $mealLiked = -1;
          }
        }
      }
    }

    $likes["totalLikes"] = $totalLikes;
    $likes["totalDislikes"] = $totalDislikes;
    $likes['mealLiked'] = $mealLiked;
    return $likes;
  }

  public function getMealLikeValue($userID, $mealID) {
    $query = "SELECT like_value
      FROM likes
      WHERE user_id = ${userID} AND recipe_id = ${mealID}";

    $likes = $this->db->query($query)->fetchAll();

    if (count($likes)) {
      return $likes[0]['like_value'];
    }
  }

  public function userLikesMeal($userID, $mealID) {
    if ($userID) {
      $mealLikeValue = $this->getMealLikeValue($userID, $mealID);
      return $mealLikeValue == 1;
    }
  }

  public function userDislikesMeal($userID, $mealID) {
    if ($userID) {
      $mealLikeValue = $this->getMealLikeValue($userID, $mealID);
      return $mealLikeValue == -1;
    }
  }

  public function addLike($likeValue, $mealID, $userID) {
    if ($userID) {

      $dateAdded = date("Y-m-d h:i:s");

      $query = "INSERT INTO likes (like_value, date_added, recipe_id, user_id)
        VALUES(${likeValue}, '${dateAdded}', ${mealID}, ${userID})";

      return $this->db->query($query);
    }
  }

  public function removeLike($userID, $mealID) {
    if ($userID) {

      $query = "DELETE
        FROM likes
        WHERE user_id = ${userID} AND recipe_id = ${mealID}";

      return $this->db->query($query);

    }
  }

  public function updateLike($likeValue, $mealID, $userID) {
    if ($userID) {

      $query = "UPDATE likes
      SET like_value=${likeValue}
      WHERE user_id=${userID} AND recipe_id=${mealID}";

      return $this->db->query($query);
    }
  }
}
