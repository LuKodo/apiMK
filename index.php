<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Max-Age: 3600");

require_once __DIR__ . '/Config/FileManager.php';
require_once __DIR__ . '/Config/Simplo.php';
require_once __DIR__ . '/Config/Router.php';
require_once __DIR__ . '/Config/Database.php';
require_once __DIR__ . '/Helper/Encrypt.php';
require_once __DIR__ . '/Controller/User.php';
require_once __DIR__ . '/Controller/Headquarter.php';
require_once __DIR__ . '/Controller/Product.php';
require_once __DIR__ . '/Controller/Carousel.php';
require_once __DIR__ . '/Controller/Category.php';
require_once __DIR__ . '/Controller/Promotion.php';
require_once __DIR__ . '/Controller/File.php';

use API\Config\Router;
use API\Controller\Carousel;
use API\Controller\Category;
use API\Controller\FileController;
use API\Controller\Headquarter;
use API\Controller\Promotion;
use API\Controller\Product;
use API\Controller\User;

$router = new Router();

$router->post('#^/users$#', User::class, 'getOne');
$router->post('#^/login$#', User::class, 'login');

$router->get('#^/headquarters$#', Headquarter::class, 'getAll');
$router->post('#^/headquarter$#', Headquarter::class, 'getOne');

$router->post('#^/products$#', Product::class, 'getAll');

$router->get('#^/carousel$#', Carousel::class, 'getAll');
$router->delete('#^/carousel$#', Carousel::class, 'delete');
$router->post('#^/carousel$#', Carousel::class, 'upsert');
$router->get('#^/carousel/delete/(.+)$#', Carousel::class, 'delete');

$router->post('#^/promotion$#', Promotion::class, 'getAll');
$router->post('#^/promotion/upsert$#', Promotion::class, 'upsert');
$router->get('#^/promotion/delete/(.+)$#', Promotion::class, 'delete');

$router->get('#^/category$#', Category::class, 'getAll');
$router->post('#^/category$#', Category::class, 'findWhere');
$router->post('#^/category/upsert$#', Category::class, 'upsert');

$router->post('#^/upload/(.+)/(.+)$#', FileController::class, 'upload');
$router->get('#^/download/(.+)$#', FileController::class, 'download');
$router->get('#^/getFile/(.+)/(.+)$#', FileController::class, 'getUrl');

$request = $_SERVER['REQUEST_METHOD'];
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

echo $router->route($request, $path);