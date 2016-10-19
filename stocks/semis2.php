<?php
/**
 * Created by PhpStorm.
 * User: galex
 * Date: 02.08.16
 * Time: 18:36
 */

header('Content-Type: text/html; charset=utf-8');

define('BR', '<br/>');

include_once 'DB.class.php';


DB::connect('testDB', 'DB_user', 'dbql');

// $dbh = DB::instance(); var_dump($dbh); echo BR;

// $dbh = DB::run("DROP TABLE IF EXISTS pdowrapper"); var_dump($dbh); echo BR;

// $dbh = DB::run("CREATE TABLE pdowrapper (id int auto_increment primary key, name varchar(255))"); var_dump($dbh); echo BR;


/*
new DB('testDB', 'DB_user', 'dbql');

// $stmt = DB::drop()->table('pdowrapper')->run();

// echo BR; var_dump($stmt); echo BR;

$stmt = DB::create()->table('IF NOT EXISTS', 'pdowrapper (id int auto_increment, definition varchar(255))')->run();

// $stmt = DB::insert()->into('pdowrapper (definition)')->values('aaa')->run();

*/

$stmt = DB::create()->table('pdowrapper2 id int auto_increment, definition varchar(255))')->run;

echo BR; var_dump($stmt); echo BR;

