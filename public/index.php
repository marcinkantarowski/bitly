<?php
require '../vendor/autoload.php';

use App\Model\DbConnectionManager;
use App\Model\Link;

session_start();

// cdatabase config
$config = require __DIR__ . '/../config/config.php';

// the Application initialisation/entry point.
$routeAction = $_SERVER["REQUEST_URI"];
if (isset($_GET['action'])) {
    $routeAction = $_GET['action'];
}

// router
switch ($routeAction) {

    case '/':
    case 'index':
        $controllerName = 'LinkController';
        if(isset($_SESSION['short'])){
            $action = 'viewAction';
        } else {
            $action = 'indexAction';
        }
        break;

    case '/nolink':
        $controllerName = 'LinkController';
        $action = 'nolinkAction';
        break;

    case 'addlinksubmitted':
        $controllerName = 'LinkController';
        $action = 'addlinksubmittedAction';
        break;

    default:
        $controllerName = 'LinkController';
        $action = 'externalredirectAction';
        break;
}
$class = '\App\Controller\\' . $controllerName;

$db = new DbConnectionManager($config);
$dbConnection = null;
if ($db) {
    $dbConnection = $db->getConnection();
}
$link = new Link($dbConnection);

$controller = new $class($link);
$controller->{$action}($_REQUEST, $routeAction);