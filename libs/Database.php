<?php
/**
 * Class Database - "Обертка" для работы с базами данных через класс PDO
 * ---------------------------------------------------------------------------
 *
 * @version 0.2.2016.10.22  - изменен подход использования класса (Singleton)
 *                            добавлен универсальный построитель запросов
 * @version 0.1.2016.10.19  - первоначальная версия
 * @author  AlexNfr
 */
class Database
{
    protected static $instance = null;  // экземпляр класса в режиме Singleton

    protected $connection = null;       // соединение с БД

    protected static $db_host;
    protected static $db_charset;
    protected static $db_driver;
    protected static $db_name;
    protected static $db_user;
    protected static $db_password;

    protected static $db_Dsn;
    protected static $db_options;
    protected static $db_errorMode;
    protected static $db_defaultFetchMode;
    protected static $db_emulatePrepares;

    protected $query = '';          // текущий запрос построителя
    protected $statement = null;    // текущий подготовленный запрос

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Задание параметров соединения с БД
     *
     * @param $name
     * @param $user
     * @param $password
     * @param array $params
     */
    public static function set($name, $user, $password, $params = [])

    {
        if (!self::$instance) {
            self::$db_host = (isset($params['host']) ? $params['host'] : 'localhost');
            self::$db_charset = (isset($params['charset']) ? $params['charset'] : 'utf8');
            self::$db_driver = (isset($params['driver']) ? $params['driver'] : 'mysql');

            self::$db_name = $name;
            self::$db_user = $user;
            self::$db_password = $password;

            self::$db_Dsn = self::$db_driver
                            . ":host=" . self::$db_host
                            . ";dbname=" . self::$db_name
                            . ";charset=" . self::$db_charset;
            self::$db_errorMode = (isset($params['errorMode']) ? $params['errorMode'] : PDO::ERRMODE_EXCEPTION);
            self::$db_defaultFetchMode = (isset($params['defaultFetchMode']) ? $params['defaultFetchMode'] : PDO::FETCH_ASSOC);
            self::$db_emulatePrepares = (isset($params['errmode']) ? $params['emulatePrepares'] : true);
            self::$db_options = [
                PDO::ATTR_ERRMODE => self::$db_errorMode,
                PDO::ATTR_DEFAULT_FETCH_MODE => self::$db_defaultFetchMode,
                PDO::ATTR_EMULATE_PREPARES => self::$db_emulatePrepares,
            ];
        }
        return (!self::$instance);
    }

    /**
     * @return Database
     */
    public static function getInstance()
    {
        return (self::$instance
                ? self::$instance
                : self::$instance = new static()
        );
    }

    /**
     * Получение текущего соединения
     *
     * @return $this->conn
     */
    public function getConnection()
    {
        return ($this->connection
                ? $this->connection
                : $this->connection = new PDO(self::$db_Dsn, self::$db_user, self::$db_password, self::$db_options)
        );
    }

    /**
     * Закрытие соединения с БД
     *
     * @return $this
     */
    public function destroy(){
        $this->connection = null;
        return ($this);
    }

    /**
     * Подготовка и выполнение запроса к БД
     *
     * @param $query
     * @return $this
     */
    public function query($query = null, $params = [])
    {
        $query = ($query ? : $this->query);
        $this->statement = $this->getConnection()->prepare($query);
        $this->statement->execute($params);
        $this->query = '';
        return ($this);
    }

    /**
     * Получение текущего запроса как объекта PDOStatement
     *
     * @return $this->statement
     */
    public function getStatement()
    {
        return ($this->statement);
    }

    /**
     * Получение текущего запроса как строки
     *
     * @return string or null
     */
    public function getQuery()
    {
        return ($this->statement ? $this->statement->queryString : null);
    }

    /**
     * Получение числа столбцов выполненного текущего запроса
     *
     * @return int or null
     */
    public function getColumnCount()
    {
        return ($this->statement ? $this->statement->columnCount() : null);
    }

    /**
     * Получение числа строк выполненного текущего запроса
     *
     * @return int or null
     */
    public function getRowCount()
    {
        return ($this->statement ? $this->statement->rowCount() : null);
    }

    /**
     * Получение одной записи выполненного текущего запроса
     *
     * @return mixed or null
     */
    public function fetch()
    {
        return ($this->statement ? $this->statement->fetch() : null);
    }

    /**
     * Получение одного столбца выполненного текущего запроса
     *
     * @return mixed or null
     */
    public function fetchColumn()
    {
        return ($this->statement ? $this->statement->fetchColumn() : null);
    }

    /**
     * Получение всех записей выполненного текущего запроса
     *
     * @return mixed or null
     */
    public function fetchAll()
    {
        return ($this->statement ? $this->statement->fetchAll() : null);
    }

    /**
     * Получение одной записи выполненного текущего запроса в виде объекта
     *
     * @param $class
     * @return mixed or null
     */
    public function fetchObject($class = 'stdClass')
    {
        return ($this->statement ? $this->statement->fetchObject($class) : null);
    }

    /**
     * Получение всех записей выполненного текущего запроса в виде массива объектов
     *
     * @param $class
     * @return array
     */
    public function fetchObjectsAll($class = 'stdClass')
    {
        if ($this->statement) {
            $objects = [];
            if ($cnt = $this->getRowCount()) {
                for ($i = 1; $i <= $cnt; $i++) {
                    $objects[] = $this->statement->fetchObject();
                }
            }
            return ($objects);
        } else {
            return (null);
        }
    }

    public function __call($name, $params)
    {
        $args = '';
        foreach ($params as $param) {
            if (is_string($param)) {
                $args .= ' ' . $param;
            } else if (is_array($param)) {
                $args .= ' ' . implode(', ', $param);
            }
        }
        $this->query .= $name . $args . ' ';
        return ($this);
    }

}