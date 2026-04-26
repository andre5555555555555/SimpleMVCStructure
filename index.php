
<?php
session_start();

require_once "controllers/ItemController.php";
require_once "controllers/AuthController.php";
require_once "controllers/BuyerListController.php";

$itemController = new ItemController();
$authController = new AuthController();
$buyerListController = new BuyerListController();

if(isset($_GET["url"])){
    $url = explode("/", $_GET["url"]);

    switch($url[0]){
        case "chair":
            if(isset($url[1]) && is_numeric($url[1])){
                $itemController->show($url[1]);
            } else {
                $itemController->notFound();
            }
        break;

        case "search":
            $itemController->search($_GET["query"]);
        break;

        case "add":
            $itemController->add();
        break;

        case "insert":
            $itemController->insert();
        break;

        case "edit":
            $itemController->edit($url[1]);
        break;

        case "update":
            $itemController->update($url[1]);
        break;

        case "delete":
            $itemController->delete();
        break;
        
        case "shop":
        $itemController->shop();
        break;

        case "my-list":
            $buyerListController->myList();
        break;

        case "add-to-list":
            $buyerListController->addToList();
        break;

        case "remove-from-list":
            $buyerListController->removeFromList();
        break;

        case "login":
            $authController->login();
        break;

        case "authenticate":
            $authController->authenticate();
        break;

        case "register":
            $authController->register();
        break;

        case "storeUser":
            $authController->storeUser();
        break;

        case "logout":
            $authController->logout();
        break;

        case "no-access":
            $itemController->noAccess();
        break;

        case "not-found":
            $itemController->notFound();
        break;
        
        default:
            $itemController->notFound();
    }
}else{
    $itemController->index();
}
