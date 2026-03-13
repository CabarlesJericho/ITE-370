<?php

class DB_Security
{
    private static $pdo;

    public static function connect()
    {
        $dsn = "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME') . ";charset=utf8mb4";
        self::$pdo = new PDO($dsn, env('DB_USER'), env('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    // Safe query - prevents SQL injection
    public static function query($sql, $params = [])
    {
        if (!self::$pdo) self::connect();
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Escape single value
    public static function escape($value)
    {
        if (!self::$pdo) self::connect();
        return self::$pdo->quote($value);
    }

    // Named params example
    public static function find($table, $id)
    {
        return self::query("SELECT * FROM {$table} WHERE id = :id", ['id' => $id])->fetch();
    }

    // Prevent mass assignment
    public static function safeInsert($table, $data, $allowed = [])
    {
        $allowed = $allowed ?: array_keys($data);
        $fields = array_intersect_key($data, array_flip($allowed));
        $sql = "INSERT INTO {$table} (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_fill(0, count($fields), '?')) . ")";
        return self::query($sql, array_values($fields));
    }
}
