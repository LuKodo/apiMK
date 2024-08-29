<?php
namespace API\Config;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new SimploDB([
                'host' => 'http://triton.inversioneslacentral.com',
                'database' => 'virtualstore',
                'username' => 'root',
                'password' => 'S4nt4Luc14*./',
                'port' => 3307,
            ]);
        }
        return self::$instance;
    }
}