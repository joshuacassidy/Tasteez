<?php

namespace Tasteez\Controllers;

use \Tasteez\Models\Meal;
use Tasteez\Models\User;
use Tasteez\Models\Category;

class Meals extends Controller {
  protected $meal;
  protected $view;
  private $user;

  function __construct($container) {
    $this->container = $container;
    $this->meal = new Meal($container->db);
    $this->view = $container->view;
    $this->user = new User($container->db);
    $this->db = $container->db;
  }

  public function meal($request, $response, $args) {
    $userID = $this->user->getID();
    $mealDetails = $this->meal->getMeal($args['id'], $userID);

    return $this->view->render($response, 'meal.twig', [
      "meal" => $mealDetails,
      "comments" => $mealDetails["comments"],
      "loggedIn" => $this->user->isLoggedIn(),
    ]);
  }

  public function mostPopular($request, $response, $args) {
    $meals = $this->meal->popular();
    return $this->view->render($response, 'mostpopular.twig', [
      "mostPopularMeals" => $meals,
      "loggedIn" => $this->user->isLoggedIn()
    ]);
  }


  public function discover($request, $response) {
    return $this->view->render($response, 'discover.twig', [
      "loggedIn" => $this->user->isLoggedIn()
    ]);
  }

  public function favourites($request, $response) {

    if($this->user->isLoggedIn()) {
      $meals = $this->meal->getFavorites($this->user->getID());

      return $this->view->render($response, 'favourites.twig', [
        "meals" => $meals,
        "loggedIn" => $this->user->isLoggedIn()
      ]);

    } else {
      return $response->withRedirect('/auth/login');
    }

    $meals = $this->meal->getFavorites($this->user->getID());

  }

  public function recommended($request, $response) {

    if($this->user->isLoggedIn()) {
      $id = $this->user->getID();
      $meals = $this->meal->recommended($id);
      return $this->view->render($response, 'recommended.twig', [
        "recommended" => $meals,
        "loggedIn" => $this->user->isLoggedIn()
      ]);

    } else {
      return $response->withRedirect('/auth/login');
    }

    
  }

  public function category($request, $response, $args) {
    $name = $args['name'];
    $categoriesModel = new Category($this->container->db);
    $categories = $categoriesModel->getAll();
    $meals = $this->meal->getMealsByCategory($name);
    return $this->view->render($response, 'category.twig', [
      "meals" => $meals, "category" => $name, "loggedIn" => $this->user->isLoggedIn()
    ]);
  }

  public function categories($request, $response, $args) {
    $categoriesModel = new Category($this->container->db);
    $categories = $categoriesModel->getAll();
    return $this->view->render($response, 'categories.twig', [
      "categories" => $categories,
      "loggedIn" => $this->user->isLoggedIn()
    ]);
  }

  public function search($request, $response) {
    if (isset($_GET["query"])) {
      $query = $request->getQueryParam("query");
      $query = preg_replace('/[\;\(\)\<\>\/\*]/', ' ', $query);
      $meals = $this->meal->search($query, null, null);
      return $this->view->render($response, 'search.twig', [
        "meals" => $meals,
        "loggedIn" => $this->user->isLoggedIn()
        ]);
    } else {
      return $this->view->render($response, 'search.twig', [
        "loggedIn" => $this->user->isLoggedIn()
        ]);
    }
  }


}
