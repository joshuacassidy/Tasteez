<?php

namespace Tasteez\Controllers;


class Account extends Controller
{
  private $user;

  
  public function getAccount($request, $response) {
    $this->user = new \Tasteez\Models\User($this->container->db);
    if ($this->user->isLoggedIn()) {
      $this->user = new \Tasteez\Models\User($this->container->db);
      $userId = $this->user->getID();
      return $this->container->view->render($response, 'account.twig', 
        [
          "loggedIn" => $this->user->isLoggedIn(), 
          "userId" => $userId
        ]
      );
    } else {
      return $response->withRedirect('/auth/login');
    }

    
  }
  

  public function postUpdateEmail($request, $response) { 
    $body = $request->getParsedBody();
    $oldEmail = $this->clean($body['oldEmail']);
    $email = $this->clean($body['email']);
    $oldEmail = trim($oldEmail);
    $email = trim($email);

    $this->user = new \Tasteez\Models\User($this->container->db);
    if ($this->user->isLoggedIn()) {
      $id = json_decode($_COOKIE['cookie'], true)['id'];
      $this->user->updateEmail($oldEmail, $email, $id);
      return $this->container->view->render($response, 'account.twig',
        [
          "loggedIn" => $this->user->isLoggedIn(), 
          "userId" => $userId, 
          "emailUpdated" => true
        ]
      );
    } else {
      return $this->container->view->render($response, 'account.twig', 
        [
          "loggedIn" => $this->user->isLoggedIn(), 
          "userId" => $userId, 
          "emailError" => true
        ]
      );
    }
  }
  
  public function postUpdatePassword($request, $response) { 
    $body = $request->getParsedBody();
    $oldPassword = $this->clean($body['oldPassword']);
    $password = $this->clean($body['password']);
    $oldPassword = trim($oldPassword);
    $password = trim($password);


    $this->user = new \Tasteez\Models\User($this->container->db);
    if ($this->user->isLoggedIn()) {
      $id = json_decode($_COOKIE['cookie'], true)['id'];
      $this->user->updatePassword($oldPassword, $password, $id);
      return $this->container->view->render($response, 'account.twig', 
        [
          "loggedIn" => $this->user->isLoggedIn(), 
          "userId" => $userId, 
          "passwordUpdated" => true
        ]
      );
    } else {
      return $this->container->view->render($response, 'account.twig', 
        [
          "loggedIn" => $this->user->isLoggedIn(), 
          "userId" => $userId, 
          "passwordError" => true
        ]
      );
    }
  }

  public function postDeleteAccount($request, $response, $args) { 
    $userId = $args['userId'];
    $this->user = new \Tasteez\Models\User($this->container->db);
    $auth = new \Tasteez\Models\Auth($this->container->db);
    if ($this->user->isLoggedIn() && $this->user->validateCookie() && json_decode($_COOKIE['cookie'], true)['id'] === $userId) {
      $this->user->deleteUser($userId);
      $auth->logout();
      return $response->withRedirect('/auth/login');
    } else {
      return $this->container->view->render($response, 'account.twig', 
        [
          "loggedIn" => $this->user->isLoggedIn(), 
          "userId" => $userId, 
          "deleteError" => true
        ]
      );
    }
  }

  public function clean($string) {
    return preg_replace('/[\;\(\)\<\>\/\*]/', '', $string); 
  }
}
