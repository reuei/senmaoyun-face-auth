<?php
/**
 * 数据库操作类（PDO，无外部依赖）
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($table, $data)
    {
        $keys = array_keys($data);
        $fields = '`' . implode('`,`', $keys) . '`';
        $placeholders = ':' . implode(',:', $keys);

        $this->query(
            "INSERT INTO `{$table}` ({$fields}) VALUES ({$placeholders})",
            $data
        );
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "`{$key}` = :set_{$key}";
        }
        $setStr = implode(', ', $sets);

        $params = [];
        foreach ($data as $key => $val) {
            $params["set_{$key}"] = $val;
        }
        $params = array_merge($params, $whereParams);

        return $this->query(
            "UPDATE `{$table}` SET {$setStr} WHERE {$where}",
            $params
        )->rowCount();
    }

    public function delete($table, $where, $params = [])
    {
        return $this->query("DELETE FROM `{$table}` WHERE {$where}", $params)->rowCount();
    }

    public function count($table, $where = '1=1', $params = [])
    {
        $row = $this->fetch("SELECT COUNT(*) as cnt FROM `{$table}` WHERE {$where}", $params);
        return (int)($row['cnt'] ?? 0);
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function table($name)
    {
        return DB_PREFIX . $name;
    }
}

function db()
{
    return Database::getInstance();
}