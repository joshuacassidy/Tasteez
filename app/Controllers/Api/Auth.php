<?php

namespace Tasteez\Controllers\Api;

class Auth
{

  protected $container;

  function __construct($container)
  {
    $this->container = $container;
  }

  public function login($request, $response) {
    $data = json_decode($request->getBody());
    $auth = new \Tasteez\Models\Auth($this->container->db);

    $username = $data->username;
    $email = $data->email;
    $password = $data->password;
    if($auth->signIn($username, $email, $password)) {
        return $response->withJson(array("message"=>"successfully logged in", "status-code" => "200", "args" => $longopts), 200);

    } else {
        return $response->withJson(array("message"=>"Invalid credentials", "status-code" => "400"), 400);
    }
  }

  public function logout($request, $response) {
    $auth = new \Tasteez\Models\Auth($this->container->db);
    $auth->logout();
    return $response->withJson(array("message" => "Successfully logged out!"));
  }

  public function register($request, $response) {
    $data = json_decode($request->getBody());
    $auth = new \Tasteez\Models\Auth($this->container->db);

    $username = $data->username;
    $email = $data->email;
    $password = $data->password;
    $confirmPassword = $data->confirm_password;
    if($auth->signUp($username, $email, $password, $confirmPassword)) {
        return $response->withJson(array("message"=>"user created", "status-code" => "200"), 200);
    } else {
        return $response->withJson(array("message"=>"user already exists", "status-code" => "400"), 400);
    }
  }


}
