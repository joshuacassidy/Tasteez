<?php

namespace Tasteez\Controllers;

use Tasteez\Models\User;

class PrivacyPolicy extends Controller {

  function __invoke($request, $response) {
    $user = new User($this->container->db);
    return $this->container->view->render($response, 'privacy-policy.twig', [
      "loggedIn" => $user->isLoggedIn()
    ]);
  }

}
