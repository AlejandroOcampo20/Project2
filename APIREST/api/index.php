<?php
    //configurar cabeceras
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    //metodo http permitido
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    include_once 'config/Database.php';
    include_once 'models/user.php';
    include_once 'controllers/UserController.php';

    $database = new Database();
    $db = $database->connect();

    if(!$db) {
        http_response_code(503);
        echo json_encode(array('message' => 'Error de conexión a la base de datos'));
        exit();
    }

    //obtenemos el metodo http
    $method = $_SERVER['REQUEST_METHOD'];

    $uri = [];
    if(isset($_GET['url'])) {
        $uri = explode('/', rtrim($_GET['url'], '/'));
    } 

    if(empty($uri) || $uri[0] !== 'users') {
        http_response_code(404);
        echo json_encode(array('message' => 'Recurso no encontrado'));
        exit();
    }

    $id = isset($uri[1]) ? (int)$uri[1] : null;

    $userModel = new User($db);
    $userController = new UserController($userModel);

    $userController->processRequest($method, $id);
