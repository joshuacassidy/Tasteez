<?php

namespace Tasteez\Controllers;
use \Tasteez\Models\User;
use \Tasteez\Models\Auth as AuthModel;

class Auth extends Controller
{

  protected $db;
  protected $view;
  protected $user;

  function __construct($container)
  {
    $this->db = $container->db;
    $this->view = $container->view;
    $this->user = new User($container->db);
  }

  public function getLogin($request, $response) {

    if ($this->user->isLoggedIn()) {
      return $response->withRedirect('/');
    }
    return $this->view->render($response, 'login.twig', [
      "loggedIn" => $this->user->isLoggedIn()
    ]);
  }

  public function getRegister($request, $response) {
    if ($this->user->isLoggedIn()) {
      return $response->withRedirect('/');
    }
    return $this->view->render($response, 'register.twig', [
      "loggedIn" => $this->user->isLoggedIn()
    ]);
  }

  public function postLogin($request, $response) {
    $user = $this->user;

    if ($user->isLoggedIn()) {
      return $response->withRedirect('/');
    }

    $data = $request->getParsedBody();
    $username = $this->clean($data['username']);
    $email = $this->clean($data['email']);
    $password = $this->clean($data['password']);

    $username = trim($username);
    $email = trim($email);
    $password = trim($password);

    $authModel = new AuthModel($this->db);
    if($authModel->signIn($email, $email, $password)) {
      return $response->withRedirect('/');
    } else {
      return $this->view->render($response, 'login.twig', [
              "error" => "Invalid login credentials",
              "loggedIn" => $this->user->isLoggedIn()
            ]);
    }
  }

  public function postRegister($request, $response) {
    if ($this->user->isLoggedIn()) {
      return $response->withRedirect('/');
    }

    $data = $request->getParsedBody();
    $errors = array();

    $username = $this->clean($data['username']);
    $email = $this->clean($data['email']);
    $password = $this->clean($data['password']);
    $confirmPassword = $this->clean($data['confirm_password']);

    $username = trim($username);
    $email = trim($email);
    $password =trim($password);
    $confirmPassword = trim($confirmPassword);

    $agreed = $data['agreed'];
   
    if ($username == "" || $email == "" || $password == "" || $confirmPassword == "" || !$agreed) {
      array_push($errors, "Some required fileds have been left blank");
    }

    if ($password !== $confirmPassword) {
      array_push($errors, "Passwords do not match");
    }

    if(!$agreed) {
      array_push($errors, "You must agree to the applications terms and conditions");
    }

    $user = new \Tasteez\Models\User($this->db);

    if ($user->exists($username, $email)) {
      array_push($errors, "User with that username or email already existsts");
    }

    if (count($errors) > 0) {
      return $this->view->render($response, 'register.twig', ["errors" => $errors]);

    } else {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $user->createNew($username, $email, $hash);
      return $this->view->render($response, 'register.twig', ["registered" => true]);
    }
   
  }

  public function logout($request, $response) {

    $authModel = new AuthModel($this->db);

    if ($this->user->isLoggedIn()) {
      $authModel->logout();
      return $response->withRedirect('/');
    }
    return $response->withRedirect('/auth/login');
  }

  public function clean($string) {
    return preg_replace('/[\;\(\)\<\>\/\*\=\"]/', '', $string); 
  }
}
