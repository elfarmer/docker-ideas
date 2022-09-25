<?php
require_once "__constants.inc.php" ;

require_once "__config.inc.php" ;


$db = MyPDO::instance();



$sql = "SELECT * FROM users";
$users = $db->run( $sql )->fetchAll() ;

foreach ($users as $key => $eachUser) {
    echo "User: {$eachUser->username} - Pass: {$eachUser->password}<br>";
}

$users = [];



echo "<h1>Hello there, this is a PHP Apache container (and MySQL)</h1>";

//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = 'db';

// Database use name
$user = 'ideasUser';

//database user password
$pass = 'ideasPass';

// database name
$mydatabase = 'id4ideas3';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass, $mydatabase);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// select query
$sql = 'SELECT * FROM users';

if ($result = $conn->query($sql)) {
    while ($data = $result->fetch_object()) {
        $users[] = $data;
    }
}


foreach ($users as $user) {
    echo "<br>";
    echo $user->username . " " . $user->password;
    echo "<br>";
}