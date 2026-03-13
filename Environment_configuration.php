<?php

class EnvConfig
{
    private static $data = [];

    public static function load($file = '.env')
    {
        if (!file_exists($file)) return;
        
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            [$key, $value] = explode('=', $line, 2);
            self::$data[trim($key)] = self::parseValue(trim($value));
        }
    }

    public static function get($key, $default = null)
    {
        return self::$data[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    private static function parseValue($value)
    {
        // Remove quotes
        if (preg_match('/^"(.*)"$/', $value, $m)) return $m[1];
        if (preg_match("/^'(.*)'$/", $value, $m)) return $m[1];
        
        // true/false/null
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if ($value === 'null') return null;
        
        return $value;
    }
}
