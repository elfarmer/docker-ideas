<?php
/**
 * Id4Ideas #equipo426
 * https://idforideas.com/
 */

declare(strict_types=1);

require_once "__config.inc.php" ;

set_exception_handler("errorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");


$parts = explode("/", trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), " /")) ;
$module = $parts[0] ?? null ;
$id     = $parts[1] ?? null ;



$methodsAllowed = [
    "categorias" => [
        "withId" => ["GET"] ,
        "nullId" => ["GET"] ,
    ],
    "productos" => [
        "withId" => ["GET"] ,
        "nullId" => ["GET"] ,
    ],
];


if ( !isset( $methodsAllowed[$module] ) ) {
    http_response_code(404);
    exit;
}

if ( !in_array($_SERVER["REQUEST_METHOD"], $methodsAllowed[$module][(is_null($id)?"nullId":"withId")]) ) {
    http_response_code(405);
    header("Allow: ".implode(", ", $methodsAllowed[$module][(is_null($id)?"nullId":"withId")]));
    exit;
}



$controller = new controller();

$controller->processRequest($_SERVER["REQUEST_METHOD"], $module, $id, $_GET);

