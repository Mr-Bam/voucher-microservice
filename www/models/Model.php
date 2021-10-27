<?php

namespace app\models;

use app\lib\Database\Database;

abstract class Model
{
    public string $tableName = '';

    public Database $db;
    public array $data;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function assign($data)
    {
        $this->data = $data;
    }

    public function findOne($filters)
    {
        $whereSql = $this->prepareWhereSql($filters);
        $tableName = $this->tableName;
        $sql = "SELECT * FROM $tableName $whereSql";
        $dbConnection = $this->db->pdo->prepare($sql);
        $dbConnection->execute($filters);
        $result = $dbConnection->fetch(\PDO::FETCH_ASSOC);

        return $result;
    }

    private function prepareWhereSql($filters)
    {
        $where = [];
        foreach ($filters as $field => $value) {
            $where[] = "$field = :$field";
        }
        $whereSql = implode(' AND ', $where);
        if ($whereSql) {
            $whereSql = 'WHERE ' . $whereSql;
        }

        return $whereSql;
    }

    public function getData()
    {
        return $this->data;
    }
}