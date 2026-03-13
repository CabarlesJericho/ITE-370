<?php

class ErrorHandler
{
    private static $log = 'logs/errors.log';
    private static $debug = false;

    public static function start()
    {
        self::$debug = env('APP_DEBUG', false);
        if (!is_dir('logs')) mkdir('logs');
        
        set_error_handler([self::class, 'error']);
        set_exception_handler([self::class, 'exception']);
        register_shutdown_function([self::class, 'shutdown']);
    }

    public static function error($code, $msg, $file, $line)
    {
        self::log(compact('code', 'msg', 'file', 'line'));
        self::$debug ? self::debugPage($msg, $file, $line) : self::errorPage();
        exit;
    }

    public static function exception($e)
    {
        self::log([
            'type' => get_class($e),
            'msg' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        self::$debug ? self::debugPage($e->getMessage(), $e->getFile(), $e->getLine()) : self::errorPage();
    }

    public static function shutdown()
    {
        $e = error_get_last();
        if ($e && in_array($e['type'], [E_ERROR, E_PARSE])) {
            self::error($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }

    private static function log($data)
    {
        $data['time'] = date('Y-m-d H:i:s');
        file_put_contents(self::$log, json_encode($data) . "\n", FILE_APPEND | LOCK_EX);
    }

    private static function debugPage($msg, $file, $line)
    {
        echo "<h1>❌ Error</h1><p>$msg</p><p><strong>$file:$line</strong></p>";
    }

    private static function errorPage()
    {
        http_response_code(500);
        echo "<h1>Oops!</h1><p>Something went wrong.</p>";
    }
}
