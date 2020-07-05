<?php

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
  $db = $c['settings']['db'];
  $pdo = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'). ";charset=UTF8",getenv('DB_USER'),getenv('DB_PASS'));
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

  return $pdo;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../app/views', [
        '../app/views/cache' => '../cache'
    ]);
    return $view;
};
