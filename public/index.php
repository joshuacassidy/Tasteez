<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Leafo\ScssPhp\Compiler;
require '../vendor/autoload.php';
require '../config.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
$dotenv->load();
error_reporting(0);

$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();

require '../app/middleware.php';
require '../app/controllers.php';
require '../app/routes.php';

$app->run();

