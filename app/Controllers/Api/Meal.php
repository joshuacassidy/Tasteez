<?php

namespace Tasteez\Controllers\Api;

use \Tasteez\Models\Meal as MealModel;
use Tasteez\Models\User;
use Tasteez\Models\Favourite;

class Meal extends Controller {
  protected $meal;
  protected $view;
  private $user;
  private $mealDetails;
  private $favourite;
  
  function __construct($container) {
    $this->container = $container;
    $this->meal = new MealModel($container->db);
    $this->view = $container->view;
    $this->user = new User($container->db);
    $this->favourite = new Favourite($container->db);
  }

  public function getMealDetails($request, $response, $args) {
    $meal = $this->meal->getMeal($args['id'], $this->user->getID());
    return $response->withJson($meal, 200);
  }


  public function likeMeal($request, $response, $args) {
    if (!$this->user->isLoggedIn()) {
      return $response->withJson(["Like failed!"], 403);
    }
    $mealID = $args['id'];

    $this->meal->likeMeal($args['id'], $this->user->getID());
    return $response->withJson(["Successully liked meal!"], 200);
  }

  public function dislikeMeal($request, $response, $args) {
    if (!$this->user->isLoggedIn()) {
      return $response->withJson(["Dislike failed!"], 403);
    }
    $mealID = $args['id'];

    $this->meal->dislikeMeal($args['id'], $this->user->getID());
    return $response->withJson(["Successfully dislike meal!"], 200);
  }

  public function favourites($request, $response, $args) {
    if ($this->user->isLoggedIn()) {
      $body = $request->getParsedBody();
      $mealID = $args['id'];
      $userID = $this->user->getID();
      
      if($this->favourite->getAll($userID) == null) {
        $this->favourite->addFavourite($userID, $mealID); 
      } else {
        $this->favourite->removeFavourite($userID, $mealID);
      }
      return $response->withJson(["Successfull!"], 200);
    }
    return $response->withJson(["Please log in"], 500);
  }

  public function addComment($request, $response, $args) {
    $meal = new \Tasteez\Models\Meal($this->container->db);
    $commentModel = new \Tasteez\Models\Comment($this->db);
    if ($this->user->isLoggedIn()) {
      $recipeId = $args['id'];
      $body = $request->getParsedBody();
      $c = $meal->addComment($body["comment"], $recipeId, $this->user->getID());
      return $response->withJson(["message" => "comment successfully added"
      ,"comments" => $c
    ], 200);
    } else {
      return $response->withJson(["message" => "user not logged in"], 403);
    }
  }

  public function deleteComment($request, $response, $args) {
    $meal = new \Tasteez\Models\Meal($this->container->db);
    if ($this->user->isLoggedIn()) {
      $recipeId = $args['id'];
      $userID = $this->user->getID();
      $body = $request->getParsedBody();
      $comment = $meal->deleteComment($body["comment_id"], $userID, $recipeId);
      return $response->withJson(["message" => "comment deleted", "comments" => $comment, "id" => $userID, "rid" => $recipeId, "cid" => $body["comment_id"]], 200);
    } else {
      return $response->withJson([], 403);
    }
  }


  

}
