<?php
declare(strict_types=1);

class DB
{
    protected ?PDO $pdo = null;
    protected ?PDOStatement $stmt = null;

    public static function connect(): static
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
            $instance->initialize();
        }
        return $instance;
    }

    private function initialize(): void
    {
        $this->pdo = new PDO(DSN, USER, PASSWORD);
    }

    public function execute(string $sql, ?array $params = null): array
    {
        $this->executeWithoutResult($sql, $params);
        return $this->fetchAll();
    }

    public function executeWithoutResult(string $sql, ?array $params = null): void
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

    public function rows(string $tableName, array $params = []): array
    {
        list($where, $params) = self::buildWhere($params);
        return $this->execute("SELECT * FROM $tableName $where", $params);
    }

    public function insert(string $tableName, array $params): void
    {
        $sql = static::buildInsertQuery($tableName, $params);
        $this->executeWithoutResult($sql, array_values($params));
    }

    public function replace(string $tableName, array $params): void
    {
        $sql = static::buildReplaceQuery($tableName, $params);
        $this->executeWithoutResult($sql, array_values($params));
    }

    public function update(string $tableName, array $params, array $where): void
    {
        list($sql, $params) = self::buildUpdateQuery($tableName, $params, $where);
        $this->executeWithoutResult($sql, $params);
    }

    public function delete(string $tableName, array $where): void
    {
        list($sql, $params) = self::buildDeleteQuery($tableName, $where);
        $this->executeWithoutResult($sql, $params);
    }

    public function getTables(): array
    {
        $result = [];
        foreach($this->execute('show tables') as $row) {
            $result[] = $row['Tables_in_'. DB_NAME];
        }
        return $result;
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollback();
    }

    private function fetch(): array|false
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function fetchAll(): array
    {
        $result = [];
        while ($value = $this->fetch()) {
            $result[] = $value;
        }
        return $result;
    }

    public static function buildInsertQuery(string $tableName, array $params): string
    {
        $sql = "INSERT INTO $tableName";
        $sql .= ' ('. implode(',', array_map(fn(string $column): string => "`$column`", array_keys($params))). ') VALUES ';
        $sql .= '('. implode(',',array_fill(0, count($params), '?')) . ');';
        return $sql;
    }

    public static function buildReplaceQuery(string $tableName, array $params): string
    {
        $sql = "REPLACE INTO $tableName";
        $sql .= ' ('. implode(',', array_map(fn(string $column): string => "`$column`", array_keys($params))). ') VALUES ';
        $sql .= '('. implode(',',array_fill(0, count($params), '?')) . ');';
        return $sql;
    }

    public static function buildDeleteQuery(string $tableName, array $params): array
    {
        list($where, $params) = self::buildWhere($params);
        $sql = "DELETE FROM $tableName $where";
        return [$sql, $params];
    }

    public static function buildUpdateQuery(string $tableName, array $params, array $where): array
    {
        $query = 'SET '. implode(',', array_map(fn(string $key): string => "$key = ?", array_keys($params)));
        list($where, $where_params) = self::buildWhere($where);
        $query = "UPDATE {$tableName} {$query}{$where}";
        return [$query, array_merge(array_values($params), array_values($where_params))];
    }

    public static function buildWhere(array $params): array
    {
        $where = [];
        $where_params = [];
        foreach ($params as $column => $value) {
            if (is_array($value)) {
                $where[] = "{$column} IN (?)";
                $where_params[] = implode(',', array_map(fn(string $v): string => "'{$v}'", $value));
            } else {
                $where[] = "{$column} = ?";
                $where_params[] = $value;
            }
        }
        return [' WHERE '. implode(' AND ', $where), $where_params];
    }
}
