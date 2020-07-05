<?php

namespace Tasteez\Controllers;

use Tasteez\Models\User;

class Contact extends Controller {

  function __invoke($request, $response) {
    $user = new User($this->container->db);
    return $this->container->view->render($response, 'contact.twig', [
      "loggedIn" => $user->isLoggedIn()
    ]);
  }

}
