<?php

namespace Tasteez\Controllers;

class Controller
{
  protected $container;
  protected $db;

  function __construct($container) {
    $this->container = $container;
    $this->db = $container->db;
  }

}
