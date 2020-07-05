<?php

namespace Tasteez\Controllers;

use \Tasteez\Models\Meal as MealModel;
use Tasteez\Models\User;

class Meal extends Controller {
  protected $meal;
  protected $view;
  private $user;
  private $mealDetails;

  function __construct($container) {
    $this->container = $container;
    $this->meal = new MealModel($container->db);
    $this->view = $container->view;
    $this->user = new User($container->db);
  }

  public function getMealDetails($response, $request, $args) {
    return $this->meal->getMeal($args['id'], $this->user->getID());
  }

  public function meal($request, $response, $args) {

    return $this->view->render($response, 'meal.twig', [
      "meal" => $this->getMealDetails($request, $response, $args),
      "loggedIn" => $this->user->isLoggedIn(),
    ]);
  }


  public function addComment($request, $response, $args) {
    $meal = new \Tasteez\Models\Meal($this->container->db);

    if ($this->user->isLoggedIn()) {

      $body = $request->getParsedBody();
      $mealID = $body["meal_id"];
      $meal->addComment($body["comment"], $mealID, $this->user->getID());
      return $response->withRedirect("/meal/${mealID}");

    } else {
      return $response->withRedirect("/auth/login");
    }

  }

  public function deleteComment($request, $response, $args) {
    $meal = new \Tasteez\Models\Meal($this->container->db);

    if ($this->user->isLoggedIn()) {

      $body = $request->getParsedBody();
      $commentID = $body["comment_id"];
      $mealID = $body["meal_id"];
      $meal->deleteComment($commentID, $this->user->getID());
      return $response->withRedirect("/meal/${mealID}");

    } else {
      return $response->withRedirect("/auth/login");
    }
  }

  public function likeMeal($request, $response, $args) {
    if (!$this->user->isLoggedIn()) {
      return $response->withRedirect('/auth/login');
    }
    $mealID = $args['id'];

    $this->meal->likeMeal($args['id'], $this->user->getID());
    return $response->withRedirect("/meal/${mealID}");
  }

  public function dislikeMeal($request, $response, $args) {
    if (!$this->user->isLoggedIn()) {
      return $response->withRedirect('/auth/login');
    }
    $mealID = $args['id'];

    $this->meal->dislikeMeal($args['id'], $this->user->getID());
    return $response->withRedirect("/meal/${mealID}");
  }

  public function addToFavourites($request, $response, $args) {
    if (!$this->user->isLoggedIn()) {
      return $response->withRedirect('/auth/login');
    }

    $body = $request->getParsedBody();
    $mealID = $body['meal_id'];
    $this->meal->addToFavourites($this->user->getID(), $mealID);

    return $response->withRedirect("/meal/${mealID}");
  }

  public function removeFromFavourites($request, $response, $args) {
    if (!$this->user->isLoggedIn()) {
      return $response->withRedirect('/auth/login');
    }

    $body = $request->getParsedBody();
    $mealID = $body['meal_id'];
    $this->meal->removeFromFavourites($this->user->getID, $mealID);

    return $response->withRedirect("/meal/${mealID}");
  }

}
