<?php

namespace Tasteez\Controllers\Api;

class Controller
{
  protected $container;
  protected $db;

  function __construct($container)
  {
    $this->container = $container;
    $this->db = $container->db;
  }

}
