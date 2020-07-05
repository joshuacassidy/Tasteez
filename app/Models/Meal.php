<?php
namespace Tasteez\Models;

use Tasteez\Models\Favourite;
use Tasteez\Models\Ingredients;
use Tasteez\Models\Liked;
use Tasteez\Models\Comment;

class Meal extends Model {

  public function getMeal($mealID, $userID) {
    $db = $this->db;

    $favouriteModel = new Favourite($db);
    $ingredientModel = new Ingredient($db);
    $likedModel = new Liked($db);
    $commentModel = new Comment($db);
    $meal = $this->findById($mealID);
    $meal["instructions"] = utf8_encode(preg_replace('/[\\\\*]/', '', $meal["instructions"]));
    $meal["instructions"] = utf8_encode(preg_replace('/[¬…“�Ââ©\;\(\)\<\>\/\*]/', ' ', $meal["instructions"]));
    
    $meal["ingredients"] = $ingredientModel->getMealIngredients($mealID);
    $meal["comments"] = $commentModel->getAll($mealID, $userID);
    $meal["isFavourite"] = $favouriteModel->isFavourite($userID, $mealID);
    $meal["likes"] = $likedModel->getMealLikes($mealID, $userID);
    $meal["userID"] = $userID;

    return $meal;
  }

  public function popular($offset = null, $limit = null) {
    $query = "SELECT *,
    CASE
    WHEN (SELECT sum(like_value) from likes WHERE recipe_id = meals.id) IS NULL THEN 0
    ELSE (SELECT sum(like_value) from likes WHERE recipe_id = meals.id)
    END AS LikesAmount FROM meals
    ORDER BY LikesAmount DESC";

    if (isset($offset) && isset($limit)) {
        $query .= " LIMIT ${offset}, ${limit}";
    }

    return $this->query($query);
  }


  public function search($searchTerm, $offset = null, $limit = null ) {
    $searchTerm = strtolower("'%".trim($searchTerm)."%'");
    $query = "SELECT * FROM meals WHERE name LIKE ${searchTerm}";

    if (isset($offset) && isset($limit)) {
        $query .= " LIMIT ${offset}, ${limit}";
    }
    return $this->db->query($query)->fetchAll();
  }

  public function getMealsByCategory($categoryName) {
    
    $query = "SELECT meals.id, thumbnail, name
      FROM meals
      INNER JOIN categories
      ON meals.category_id = categories.id
      WHERE meals.category_id = (SELECT id FROM categories WHERE categories.category = '${categoryName}')";

      if (isset($offset) && isset($limit)) {
          $query .= " LIMIT ${offset}, ${limit}";
      }

      return $this->db->query($query)->fetchAll();
  }

  
  public function likeMeal($mealID, $userID = 0) {
    if ($userID) {
      $liked = new Liked($this->db);

      if ($liked->userLikesMeal($userID, $mealID)) {
        $liked->removeLike($userID, $mealID);

      } else if ($liked->userDislikesMeal($userID, $mealID)) {
        $liked->updateLike(1, $mealID, $userID);

      } else {
        $liked->addLike(1, $mealID, $userID);
      }
    }
  }

  public function dislikeMeal($mealID, $userID) {
    if ($userID) {
      $liked = new Liked($this->db);

      if ($liked->userDislikesMeal($userID, $mealID)) {
        $liked->removeLike($userID, $mealID);

      } else if ($liked->userLikesMeal($userID, $mealID)) {
        $liked->updateLike(-1, $mealID, $userID);

      } else {
        $liked->addLike(-1, $mealID, $userID);

      }
    }
  }

  public function addToFavourites($userID, $mealID) {
    if ($userID) {
      $favourite = new Favourite($this->db);
      if ($favourite->isFavourite($userID, $mealID)) {
        $favourite->removeFavourite($userID, $mealID);
      } else {
        $favourite->addFavourite($userID, $mealID);
      }
    }
  }

  public function removeFromFavourites($userID, $mealID) {
    if ($userID) {
      $favourite = new Favourite($this->db);
      $favourite->removeFavourite($userID, $mealID);
    }
  }

  public function getFavorites($userID) {
    $query = "SELECT * FROM favorites INNER JOIN meals ON  meals.id = favorites.recipe_id WHERE user_id = :user_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':user_id', $userID);
    $stmt->execute();
    return $stmt->fetchAll();
  }



  public function addComment($comment, $mealID, $userID) {
    $commentModel = new Comment($this->db);
    $commentModel->addComment($comment, $mealID, $userID);
    return $commentModel->getAll($mealID, $userID);
  }

  public function deleteComment($commentID, $userID, $mealID) {
    $commentModel = new Comment($this->db);
    $commentModel->deleteComment($commentID, $userID);
    return $commentModel->getAll($mealID, $userID);
  }


  public function recommended($id) {
    $query = "SELECT likes.like_value, likes.user_id, ingredients.name, meal_ingredients.ingredient_id, meal_ingredients.meal_id
    FROM likes
    INNER JOIN meal_ingredients ON meal_ingredients.meal_id = likes.recipe_id 
    INNER JOIN ingredients ON meal_ingredients.ingredient_id = ingredients.id 
    WHERE likes.user_id = :id;";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $userLikedMeals = $stmt->fetchAll();
    $query = "SELECT meals.name as meal, ingredients.name as ingredient, meals.thumbnail as thumbnail, meals.id as id
    FROM meal_ingredients 
    INNER JOIN meals ON meals.id = meal_ingredients.meal_id
    INNER JOIN ingredients ON ingredients.id = meal_ingredients.ingredient_id 
    LEFT JOIN likes ON likes.recipe_id = meal_ingredients.meal_id
    where not exists (select *
      from likes
      where likes.recipe_id = meal_ingredients.meal_id and likes.user_id = :id
    );";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $uncleanedMeals = $stmt->fetchAll();
    $cleanedMeals = array();
    for ($i = 0; $i < sizeof($uncleanedMeals); $i++) {
        $meal = $uncleanedMeals[$i]["meal"];
        $thumbnail = $uncleanedMeals[$i]["thumbnail"];
        $id = $uncleanedMeals[$i]["id"];
        $cleanedMeals[$meal] = array("name"=> $meal, "ingredient"=> [], "thumbnail" => $thumbnail, "id" => $id);
    }
    for ($i = 0; $i < sizeof($uncleanedMeals); $i++) {
        $meal = $uncleanedMeals[$i]["meal"];
        $ingredient = $uncleanedMeals[$i]["ingredient"];
        array_push($cleanedMeals[$meal]["ingredient"], $ingredient);
    }
    
    $cleanedMeals = array_values($cleanedMeals);
    for ($i = 0; $i < sizeof($cleanedMeals); $i++) {
      $cleanedMeals[$i]['ingredient'] = array_unique($cleanedMeals[$i]['ingredient']);
      $cleanedMeals[$i]['ingredient'] = array_values($cleanedMeals[$i]['ingredient']);
    }
    $data = json_encode(array("meals" => $cleanedMeals, "liked" => $userLikedMeals));
    $request = curl_init('https://tasteez-recommender.herokuapp.com/');
    curl_setopt($request, CURLOPT_POSTFIELDS, $data);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $response = json_decode(curl_exec($request));
    curl_close($request);
    return $response;
  }

}
