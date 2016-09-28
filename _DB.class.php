<?php

/**
 * Класс DB - version 1.0. "Обертка" для работы с SQL-базами данных через класс PDO (в стиле ООП)
 * ----------------------------------------------------------------------------------------------
 *
 *  версия 0.1  -   повторение "обертки" для PDO от http://phpfaq.ru/pdo/pdo_wrapper
 *  версия 0.2  -   добавлены методы: connect(), __construct(),
 *                  дополнены методы: run()
 *
 */

class DB extends PDO
{
    protected static $instance = null;

    protected static $db_host = 'localhost';
    protected static $db_name = 'database';
    protected static $db_user = 'root';
    protected static $db_pass = '';
    protected static $db_char = 'utf8';
    protected static $db_drv  = 'mysql';
    protected static $db_Dsn;
    protected static $db_opt = [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => TRUE,
                     ];

    public function __construct($name, $user, $pass, $host = 'localhost', $char = 'utf8', $driver = 'mysql')
    {
        if (self::$instance === null) {
            self::connect($name, $user, $pass, $host, $char, $driver);
            self::$db_Dsn  = self::$db_drv . ':host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_char;
            self::$instance = parent::__construct(self::$db_Dsn, self::$db_user, self::$db_pass);
        } else {
            // при создании еще одного объекта уничтожаем его и ничего больше не делаем
            unset($this);
        }
    }

    public function __clone()
    {
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([self::instance(), $name], $arguments);
    }

    public static function connect($name, $user, $pass, $host = 'localhost', $char = 'utf8', $driver = 'mysql')
    {
        self::$db_name = $name;
        self::$db_user = $user;
        self::$db_pass = $pass;
        self::$db_host = $host;
        self::$db_char = $char;
        self::$db_drv  = $driver;
    }

    /*
     * Блок функций, совместимых с версией класса DB от http://phpfaq.ru/pdo/pdo_wrapper
     */

    public static function instance()
    {
        return (!self::$instance)   ? self::$instance = new DB(self::$db_name, self::$db_user, self::$db_pass)
                                    : self::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::instance(), $name], $arguments);
    }

    public static function run($sql = '', $args = [])
    {
        $stmt = null;
        if (!empty($sql)) {
            // echo '*** Запуск запроса: ' . $sql . ' *** ';
            $stmt = self::instance()->prepare($sql);
            $stmt->execute($args);
        }
        return $stmt;
    }

}

