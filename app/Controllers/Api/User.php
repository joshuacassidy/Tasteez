<?php

namespace Tasteez\Controllers\Api;

class User {

  protected $container;

  function __construct($container) {
    $this->container = $container;
  }

  public function updateEmail($request, $response) {
    $body = $request->getParsedBody();
    $oldEmail = $body['oldEmail'];
    $email = $body['email'];
    $user = new \Tasteez\Models\User($this->container->db);
    if ($user->isLoggedIn()) {
        $id = json_decode($_COOKIE['cookie'], true)['id'];
        return $response->withJson($user->updateEmail($oldEmail, $email, $id));
    } else {
        return $response->withJson(array("Message" => "User not logged in!"));
    }
  }

  public function updatePassword($request, $response) {
    $body = $request->getParsedBody();
    $oldPassword = $body['oldPassword'];
    $password = $body['password'];
    $user = new \Tasteez\Models\User($this->container->db);
    if ($user->isLoggedIn()) {
        $id = json_decode($_COOKIE['cookie'], true)['id'];
        $result = $user->updatePassword($oldPassword, $password, $id);
        return $response->withJson($result, $result["status"]);
    } else {
        return $response->withJson(array("Message" => "User not logged in!"), 401);
    }
   }

  public function deleteUser($request, $response, $args) {
    $userId = $args['userId'];
    $user = new \Tasteez\Models\User($this->container->db);
    if ($user->isLoggedIn() && $user->validateCookie() && json_decode($_COOKIE['cookie'], true)['id'] === $userId) {
      $user->deleteUser($userId);
      unset($_COOKIE['cookie']);
      return $response->withJson(array("Message" => "Successfully deleted user!"));
    } else {
      return $response->withJson(array("Message" => "User not logged in!"));
    }
  }

}
