<?php

class Db
{
    protected $path;
    public function __construct($path)
    {
        $isNewDb = !file_exists($path);

        $this->connection = new PDO('sqlite:' . $path);

        if ($isNewDb) {
            $this->afterCreateDb();
        }
    }

    public function afterCreateDb()
    {
    }

    protected $connection;
    protected function getConnection()
    {
        return $this->connection;
    }

    public function execute($sql, $binds = [])
    {
        $result = $this->getConnection()->prepare($sql);
        $result->execute($binds);
        return $result;
    }

    public function fetch($sql, $binds = [])
    {
        $result = $this->execute($sql, $binds);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $rows = [];
        while ($row = $result->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function create($table, $attributes)
    {
        $binds = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $binds[':' . $attributeName] = $attributeValue;
        }
        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($attributes)) . ') VALUES (' . implode(', ', array_keys($binds)) . ')';
        $this->execute($sql, $binds);
        return $this->getConnection()->lastInsertId();
    }

    public function read($table, $where = '', $binds = [], $fields = '*')
    {
        $where = trim($where);
        $sql = 'SELECT ' . $fields . ' FROM ' . $table . ($where ? ' WHERE ' . $where : '');
        return $this->fetch($sql, $binds);
    }

    public function update($table, $attributes, $where, $binds = [])
    {
        $bindKeysPrefix = ':_update_';
        if (count($binds)) {
            $bindKeysMaxLength = max(array_map('strlen', array_keys($binds)));
            $bindKeysPrefix = str_pad($bindKeysPrefix, $bindKeysMaxLength, '_');
        }

        $assigns = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $assigns[] = ($attributeName . ' = ' . $bindKeysPrefix . $attributeName);
            $binds[$bindKeysPrefix . $attributeName] = $attributeValue;
        }

        $where = trim($where);
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $assigns) . ($where ? ' WHERE ' . $where : '');
        $result = $this->execute($sql, $binds);
        return $result->rowCount();
    }

    public function delete($table, $where, $binds = [])
    {
        $where = trim($where);
        $sql = 'DELETE FROM ' . $table . ($where ? ' WHERE ' . $where : '');
        $result = $this->execute($sql, $binds);
        return $result->rowCount();
    }
}

class MyDb extends Db
{
    public function afterCreateDb()
    {
        $this->connection->exec('
            CREATE TABLE visits (
                id          INTEGER PRIMARY KEY,
                user_agent  TEXT,
                url         TEXT,
                visited_at  TEXT,
                request     TEXT
            );
        ');
    }
}

$db = new MyDb(__DIR__ . '/database.sqlite');
