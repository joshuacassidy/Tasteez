<?php

namespace Tasteez\Models;

require __DIR__ . '/../../vendor/autoload.php';

class Auth extends Model {

  public function __construct($db) {
      $this->db = $db ;
  }

  public function signUp($username, $email, $password, $confirmPassword) {
    $user = new User($this->db);

    if ($user->exists($username, $email)) {
        return false;
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $user->createNew($username, $email, $hash);
        return true;
    }
  }

  public function signIn($username, $email, $password) {
    $user = new User($this->db);
    $userDetails = $user->findByName($username, $email);

    if (!$user->exists($username, $email) || !(password_verify($password, $userDetails['password']))) {

        return false;
    } else {
        $token = array(
            "id" => $userDetails['id'],
            "username" => $userDetails['username'],
            "token" => password_hash($userDetails['id'] . getenv('SECRET_KEY'), PASSWORD_BCRYPT)
        );
        setcookie('cookie', json_encode($token), time()+3600, "/");
        return true;
    }
  }

  public function logout() {
    unset($_COOKIE['cookie']);
    setcookie('cookie', '', time()-3600, "/");
  }

}
