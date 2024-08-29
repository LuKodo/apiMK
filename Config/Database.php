<?php
namespace API\Config;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new SimploDB([
                'host' => 'localhost',
                'database' => 'virtualstore',
                'username' => 'root',
                'password' => '123456',
                'port' => 3308,
            ]);
        }
        return self::$instance;
    }
}