<?php
/**
 * Id4Ideas #equipo426
 * https://idforideas.com/
 */

declare(strict_types=1);

require_once "__config.inc.php" ;

set_exception_handler("errorHandler::handleException");

//header("Content-type: application/json; charset=UTF-8");


$parts = explode("/", $_SERVER["REQUEST_URI"]) ;

print_r($parts);
die("nana");

$module = $parts[1] ?? null ;
$id     = $parts[2] ?? null ;


$methodsAllowed = [
    "categorias" => [
        "withId" => ["GET", "PUT", "DELETE"] ,
        "nullId" => ["GET", "POST"] ,
    ],
    "productos" => [
        "withId" => ["GET", "PUT", "DELETE"] ,
        "nullId" => ["GET", "POST"] ,
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

$controller->processRequest($_SERVER["REQUEST_METHOD"], $module, $id);

