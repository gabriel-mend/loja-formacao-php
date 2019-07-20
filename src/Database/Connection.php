<?php
namespace LojaApp\Database;

class Connection
{
    public static $instance;

    public function __construct(){}

    public static function getInstance()
    {                               
        if(!self::$instance)
        {
            $connector = 'mysql:dbname=' . DB_NAME .';host=' . DB_HOST;
            self::$instance = new \PDO($connector, DB_USER, DB_PASSOWORD);
            self::$instance->exec('SET NAMES ' . DB_CHARSET . ';');
        }
        return self::$instance;
    }
}
