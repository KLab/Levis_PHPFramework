<?php

class ModelBase
{
    protected $db = null;

    public static function getColumns()
    {
        throw new LogicException("Not implement getColumns in this class");
    }

    public static function getTableName()
    {
        return underscore(get_called_class());
    }

    public function __construct()
    {
        $this->db = DB::connect();
    }

    public static function getAll()
    {
        return DB::connect()->execute('SELECT * FROM '. static::getTableName());
    }

    public static function getById($id)
    {
        return DB::connect()->execute('SELECT * FROM '. static::getTableName() . ' WHERE id = ?', [$id]);
    }

    public static function get($params)
    {
        return DB::connect()->rows(static::getTableName(), $params);
    }

    public static function insert($params)
    {
        DB::connect()->insert(static::getTableName(), $params);
    }

    public static function update($params, $where)
    {
        DB::connect()->update(static::getTableName(), $params, $where);
    }

    public static function buildInsertQuery($params)
    {
        $sql = 'INSERT INTO '. static::getTableName();
        $sql .= ' ('. implode(',', array_map(function($column) { return "`$column`"; }, array_keys($params))). ') VALUES ';
        $sql .= '('. implode(',',array_fill(0, count($params), '?')) . ');';
        return $sql;
    }

    public function delete($params)
    {
        DB::connect()->delete(static::getTableName(), $params);
    }

    public function begin()
    {
        $this->db->begin();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }
}
