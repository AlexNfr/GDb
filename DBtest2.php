<?php

// Тестовый файл применения класса DB

header('Content-Type: text/html; charset=utf-8');

define('BR', '<br/>');

include_once 'DB.class.php';


$dbh = new DB('testDB', 'DB_user', '');

$stmt = $dbh->run("DROP TABLE IF EXISTS pdodbtest"); var_dump($stmt); echo BR;

$stmt = $dbh->run("CREATE TABLE pdodbtest (id int auto_increment primary key, name varchar(255))"); var_dump($stmt); echo BR;

$stmt = $dbh->run("INSERT INTO pdodbtest (id, name) VALUES (DEFAULT, 'Name 1')"); var_dump($stmt); echo BR;
$stmt = $dbh->run("INSERT INTO pdodbtest (id, name) VALUES (DEFAULT, 'Name 2')"); var_dump($stmt); echo BR;

$stmt = $dbh->run("SELECT * FROM pdodbtest"); var_dump($stmt); echo BR;

$data = $stmt->fetchAll(); var_dump($data); echo BR;
