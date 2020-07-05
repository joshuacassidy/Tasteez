<?php

namespace Tasteez\Controllers\Api;

class Contact
{

  protected $container;

  function __construct($container)
  {
    $this->container = $container;
  }

  public function sendEmail($request, $response) {
    $contact = new \Tasteez\Models\Contact();
    $body = json_decode($request->getBody());
    $result = $contact->sendEmail($body->email, $body->name, $body->message);
    return $response->withJson($result, $result['status']);
  }

  public function clean($string) {
    return preg_replace('/[\;\(\)\<\>\/\*]/', '', $string); 
  }
}
