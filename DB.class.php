<?php

/**
 * Класс DB - version 1.0. "Обертка" для работы с SQL-базами данных через класс PDO (в стиле ООП)
 * ----------------------------------------------------------------------------------------------
 *
 *  версия 0.1  -   повторение "обертки" для PDO от http://phpfaq.ru/pdo/pdo_wrapper
 *  версия 0.2  -   добавлены методы: connect()
 *
 */

class DB extends PDO
{
    protected static $instance = null;

    protected static $db_host;
    protected static $db_name;
    protected static $db_user;
    protected static $db_pass;
    protected static $db_char;
    protected static $db_drv;
    protected static $db_Dsn;
    protected static $db_opt;

    protected static $sql_curr = '';
    protected static $sql_oper = false;
    protected static $sql_err  = false;

    protected static $SQL_KEYWORDS = [
                        //
                        // операторы работы с базами/таблицами: create, drop, alter, rename, truncate,
                        //                                      use, describe, load data
                        'create'        => [0],
                        'drop'          => [0],
                        'alter'         => [0],
                        'rename'        => [0],
                        'truncate'      => [0],
                        'use'           => [0],
                        'describe'      => [0],
                        'load_data'     => [0],
                        //
                        // Операторы работы с записями
                        //
                        'select'            => [0],
                        'do'                => [0],
                        'handler'           => [0],
                        'insert'            => [0],
                        'update'            => [0],
                        'delete'            => [0],
                        'replace'           => [0],
                        'begin'             => [0],
                        'commit'            => [0],
                        'rollback'          => [0],
                        'lock'              => [0],
                        'unlock'            => [0],
                        'set_transaction'   => [0],
                        //
                        // Параметры и модификаторы 1-го уровня
                        //
                        'database'      => [1],
                        'table'         => [1],
                        'index'         => [1],
                        'if_exists'     => [1],
                        'if_not_exists' => [1],
                        //
                        // Параметры и модификаторы 2-го уровня
                        //
                        'like'                  => [2],
                        'where'                 => [2],
                        'group_by'              => [2],
                        'having'                => [2],
                        'order_by'              => [2],
                        'limit'                 => [2],
                        'procedure'             => [2],
                        'for_update'            => [2],
                        'lock_in_share_mode'    => [2],

    ];

    public function __construct($name, $user, $pass, $host = 'localhost', $char = 'utf8', $driver = 'mysql')
    {
        if (self::$instance === null) {
            self::connect($name, $user, $pass, $host, $char, $driver);
            parent::__construct(self::$db_Dsn, self::$db_user, self::$db_pass);
            self::$instance = $this;
            var_dump($this);
        } else {
            // при создании еще одного объекта уничтожаем его и ничего больше не делаем
            unset($this);
        }
    }

    public function __clone()
    {
    }

    public function connect($name, $user, $pass, $host = 'localhost', $char = 'utf8', $driver = 'mysql')
    {
        self::$db_name = $name;
        self::$db_user = $user;
        self::$db_pass = $pass;
        self::$db_host = $host;
        self::$db_char = $char;
        self::$db_drv  = $driver;
        self::$db_Dsn  = self::$db_drv . ':host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_char;
        self::$db_opt  = [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => TRUE,
        ];
    }

    public static function addKeyword($keyword, $arguments)
    {
        self::$sql_curr .= strtoupper($keyword) . ' ';
        foreach ($arguments as $arg) self::$sql_curr .= $arg . ' ';
    }

    public function __call($name, $arguments)
    {
        $keyword = self::$SQL_KEYWORDS[strtolower($name)];
        if (isset($keyword)) {
            if ($keyword[0] == 0) {
                self::$sql_oper = true;
                self::$sql_err = false;
                self::addKeyword($name, $arguments);
            } else if (($keyword[0] != 0) && (self::$sql_oper)) {
                self::addKeyword($name, $arguments);
            } else {
                self::$sql_err = true;
            }
            return self::$instance;
        } else {
            return call_user_func_array([self::instance(), $name], $arguments);
        }
    }

    /*
     * Блок функций, совместимых с версией класса DB от http://phpfaq.ru/pdo/pdo_wrapper
     */

    public static function instance()
    {
        if (!self::$instance) {
            new DB(self::$db_name, self::$db_user, self::$db_pass);
            var_dump(self::$instance);
        }
        return self::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $keyword = self::$SQL_KEYWORDS[strtolower($name)];
        if (isset($keyword)) {
            if ($keyword[0] == 0) {
                self::$sql_oper = true;
                self::$sql_err = false;
                self::addKeyword($name, $arguments);
            } else {
                self::$sql_err = true;
            }
            return self::$instance;
        } else {
            return call_user_func_array([self::instance(), $name], $arguments);
        }
    }

    public static function run($sql = '', $args = [])
    {
        if (empty($sql)) {
            $sql = self::$sql_curr;
        }
        var_dump(self::instance());
        echo '*** Запуск запроса: ' . $sql . ' *** ';
        $stmt = self::instance()->prepare($sql);
        var_dump($stmt->execute($args));
        self::$sql_curr = '';
        self::$sql_oper = false;
        return $stmt;
    }

}

