<?php

    header('Content-type: text/html; charset=utf-8');

    require_once('../libs/Autoloader.php');
    spl_autoload_register([new Autoloader(), 'loadClass']);

    Database::set('', 'root', '123456');
    $db = Database::getInstance();

    $data = $db->show('tables')->from('gcore')->query()->fetchAll();
    var_dump($db, $data);

    $data = $db->show('columns')->from('chatdata')->from('gcore')->query()->fetchAll();
    var_dump($db, $data);
