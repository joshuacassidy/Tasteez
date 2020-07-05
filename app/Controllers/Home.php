<?php

namespace Tasteez\Controllers;

use Tasteez\Models\Meal;
use Tasteez\Models\User;
use Tasteez\Models\Category;

class Home extends Controller {

  function __invoke($request, $response) {
    $meal = new Meal($this->container->db);
    $user = new User($this->container->db);
    $category = new Category($this->container->db);

    $mostPopularMeals = $meal->popular(0, 12);
    $categories = $category->getAll(0, 12);
    if($user->isLoggedIn()) {
      $id = $user->getID();
      $recommended = array_slice($meal->recommended($id), 0, 12); 
    }
    return $this->container->view->render($response, 'home.twig', [
      "mostPopularMeals" => $mostPopularMeals,
      "recommended" => $recommended,
      "categories" => $categories,
      "loggedIn" => $user->isLoggedIn()
    ]);
  }

}
