<?php
/**
 * Class Database - "Обертка" для работы с базами данных через класс PDO
 * ---------------------------------------------------------------------
 *
 * @version 0.1.2016.10.19  - первоначальная версия
 * @author  AlexNfr
 */
class Database
{
    protected $connection = null;   // текущее соединение с БД

    protected $db_host;
    protected $db_charset;
    protected $db_driver;
    protected $db_name;
    protected $db_user;
    protected $db_password;

    protected $db_Dsn;
    protected $db_opt;

    protected $statement = null;    // текущий подготовленный запрос

    /**
     * Создание соединения с БД
     *
     * @param $name
     * @param $user
     * @param $password
     * @param string $host
     * @param string $charset
     * @param string $driver
     */
    public function __construct($name, $user, $password, $host = 'localhost', $charset = 'utf8', $driver = 'mysql')
    {
        $this->db_host = $host;
        $this->db_charset = $charset;
        $this->db_driver = $driver;
        $this->db_name = $name;
        $this->db_user = $user;
        $this->db_password = $password;

        $this->db_Dsn = "{$this->db_driver}:host={$this->db_host};dbname={$this->db_name};charset={$this->db_charset}";
        $this->db_opt  = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => TRUE,
        ];

        $this->connection = new PDO($this->db_Dsn, $this->db_user, $this->db_password, $this->db_opt);
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
     * Получение текущего соединения
     *
     * @return $this->conn
     */
    public function getConnection()
    {
        return ($this->connection);
    }

    /**
     * Подготовка и выполнение запроса к БД
     *
     * @param $query
     * @return $this
     */
    public function query($query, $params = [])
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
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

}