<?php

class DB
{
    protected $pdo = null;
    protected $stmt = null;

    public static function connect()
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
            $instance->initialize();
        }
        return $instance;
    }

    private function initialize()
    {
        $this->pdo = new PDO(DSN, USER, PASSWORD);
    }

    public function execute($sql, $params = null)
    {
        $this->executeWithoutResult($sql, $params);
        return $this->fetchAll();
    }

    public function executeWithoutResult($sql, $params = null)
    {
        $this->stmt = $this->pdo->prepare($sql);
        $flag = false;
        if (!$params) {
            $flag = $this->stmt->execute();
        } else {
            $flag = $this->stmt->execute($params);
        }
        if (!$flag) {
            $message = json_encode($this->stmt->errorInfo());
            Logger::getInstance()->error($message);
            throw new Exception($message);
        } else {
            Logger::getInstance()->info($sql. json_encode($params ?? []));
        }
    }

    public function rows($tableName, $params = [])
    {
        list($where, $params) = self::buildWhere($params);
        return $this->execute("SELECT * FROM $tableName $where", $params);
    }

    public function insert($tableName, $params)
    {
        $sql = static::buildInsertQuery($tableName, $params);
        $this->executeWithoutResult($sql, array_values($params));
    }

    public function replace($tableName, $params)
    {
        $sql = static::buildReplaceQuery($tableName, $params);
        $this->executeWithoutResult($sql, array_values($params));
    }

    public function update($tableName, $params, $where)
    {
        list($sql, $params) = self::buildUpdateQuery($tableName, $params, $where);
        $this->executeWithoutResult($sql, $params);
    }

    public function delete($tableName, $where)
    {
        list($sql, $params) = self::buildDeleteQuery($tableName, $where);
        $this->executeWithoutResult($sql, $params);
    }

    public function getTables()
    {
        $result = [];
        foreach($this->execute('show tables') as $row) {
            $result[] = $row['Tables_in_'. DB_NAME];
        }
        return $result;
    }

    public function begin()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollback()
    {
        $this->pdo->rollback();
    }

    private function fetch()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function fetchAll()
    {
        $result = [];
        while ($value = $this->fetch()) {
            $result[] = $value;
        }
        return $result;
    }

    public static function buildInsertQuery($tableName, $params)
    {
        $sql = "INSERT INTO $tableName";
        $sql .= ' ('. implode(',', array_map(function($column) { return "`$column`"; }, array_keys($params))). ') VALUES ';
        $sql .= '('. implode(',',array_fill(0, count($params), '?')) . ');';
        return $sql;
    }

    public static function buildReplaceQuery($tableName, $params)
    {
        $sql = "REPLACE INTO $tableName";
        $sql .= ' ('. implode(',', array_map(function($column) { return "`$column`"; }, array_keys($params))). ') VALUES ';
        $sql .= '('. implode(',',array_fill(0, count($params), '?')) . ');';
        return $sql;
    }

    public static function buildDeleteQuery($tableName, $params)
    {
        list($where, $params) = self::buildWhere($params);
        $sql = "DELETE FROM $tableName $where";
        return [$sql, $params];
    }

    public static function buildUpdateQuery($tableName, $params, $where)
    {
        $array = [];
        $query = 'SET '. implode(',', array_map(function($key) { return "$key = ?"; }, array_keys($params)));
        list($where, $where_params) = self::buildWHere($where);
        $query = "UPDATE {$tableName} {$query}{$where}";
        return [$query, array_merge(array_values($params), array_values($where_params))];
    }

    public static function buildWhere($params)
    {
        foreach ($params as $column => $value) {
            if (is_array($value)) {
                $where[] = "{$column} IN (?)";
                $where_params[] = implode(',', array_map(function($v) { return "'${v}'"; }, $value));
            } else {
                $where[] = "{$column} = ?";
                $where_params[] = $value;
            }
        }
        return [' WHERE '. implode(' AND ', $where), $where_params];
    }
}
