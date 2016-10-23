<?php

    header('Content-type: text/html; charset=utf-8');

    require_once('../libs/Autoloader.php');
    spl_autoload_register([new Autoloader(), 'loadClass']);

    Database::set('testDB', 'DB_user', '');
    $db = Database::getInstance();
    $cnt = $db->getRowCount();
    echo 'Открыли БД';
    var_dump($db, $cnt);

    $data = $db->query("SHOW TABLES")->fetchAll();
    $cnt = $db->getRowCount();
    echo 'Cписок таблиц';
    var_dump($db, $cnt, $data);

    $objects = $db->query("SHOW TABLES")->fetchObjectsAll();
    echo 'Cписок объектов таблиц';
    var_dump($db, $objects);
