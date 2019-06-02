<?php

class DBManager
{

    private static $connection = NULL;

    private static function getDBCredentials($env = 0)
    {
        $dbCredentials = [];
        if ($env === 1) {
            require 'config/prod.php';
        } else {
            require 'config/local.php';
        }
        // load credentials according to environment
        if (isset($dbCredentials) &&
            !empty($dbCredentials) &&
            is_array($dbCredentials)
        ) {
            return $dbCredentials;
        }
        return [];
    }

    /**
     * Get database connection
     * @return PDO instance
     */
    public static function getConnection()
    {
        if (static::$connection !== NULL &&
            static::$connection instanceof PDO
        ) {
            return static::$connection;
        }

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            ];
            $dbCredentials = static::getDBCredentials();
            static::$connection = new PDO(
                'mysql:host=' . $dbCredentials['host'] . ';dbname=' . $dbCredentials['database'], $dbCredentials['username'], $dbCredentials['password'], $options
            );
        } catch (PDOException $exception) {
            error_log('PDOException: ' . $exception->getMessage());
        }

        return static::$connection;
    }

    /**
     * Insert into table with values
     * @param array $params
     * @return boolean
     */
    public static function insert($params)
    {
        if (empty($params) ||
            empty($params['table']) ||
            empty($params['columnValuePairs'])
        ) {
            return false;
        }
        $table = $params['table'];
        $columns = implode(', ', array_keys($params['columnValuePairs']));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES (";
        $sqlParams = '';
        foreach ($params['columnValuePairs'] as $value) {
            if (empty($sqlParams)) {
                $sqlParams .= '?';
            } else {
                $sqlParams .= ', ?';
            }
        }
        $sql .= $sqlParams . ')';
        try {
            $statement = static::getConnection()->prepare($sql);
            $statement->execute(array_values($params['columnValuePairs']));
            return static::getConnection()->lastInsertId();
        } catch (Exception $exc) {
            error_log('DBManager->insert Exception: ' . $exc->getMessage());
            error_log($sql);
            error_log(print_r($params['columnValuePairs'], 1));
            return false;
        }
    }

    /**
     * Executes query and returns result in rows
     * @param string $query
     * @param params parameter array
     * @return type
     */
    public static function executeRawQuery($query, $params = [])
    {
        $statement = static::getConnection()->prepare($query);
        $statement->execute($params);
        return static::getResultRows($statement);
    }

    /**
     * Returns table result in PDOStatement as array
     * @param PDOStatement $statement
     * @return array
     */
    private static function getResultRows(&$statement)
    {
        $result = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $result[] = $row;
        }
        return $result;
    }
}
