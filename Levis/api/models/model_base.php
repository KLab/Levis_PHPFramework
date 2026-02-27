<?php
declare(strict_types=1);

class ModelBase
{
    protected ?DB $db = null;

    public static function getColumns(): array
    {
        throw new LogicException("Not implement getColumns in this class");
    }

    public static function getTableName(): string
    {
        return underscore(get_called_class());
    }

    public function __construct()
    {
        $this->db = DB::connect();
    }

    public static function getAll(): array
    {
        return DB::connect()->execute('SELECT * FROM '. static::getTableName());
    }

    public static function getById(int $id): array
    {
        return DB::connect()->execute('SELECT * FROM '. static::getTableName() . ' WHERE id = ?', [$id]);
    }

    public static function get(array $params): array
    {
        return DB::connect()->rows(static::getTableName(), $params);
    }

    public static function insert(array $params): void
    {
        DB::connect()->insert(static::getTableName(), $params);
    }

    public static function update(array $params, array $where): void
    {
        DB::connect()->update(static::getTableName(), $params, $where);
    }

    public static function buildInsertQuery(array $params): string
    {
        $sql = 'INSERT INTO '. static::getTableName();
        $sql .= ' ('. implode(',', array_map(fn(string $column): string => "`$column`", array_keys($params))). ') VALUES ';
        $sql .= '('. implode(',',array_fill(0, count($params), '?')) . ');';
        return $sql;
    }

    public function delete(array $params): void
    {
        DB::connect()->delete(static::getTableName(), $params);
    }

    public function begin(): void
    {
        $this->db->begin();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollback();
    }
}
