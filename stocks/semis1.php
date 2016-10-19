<?php

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
