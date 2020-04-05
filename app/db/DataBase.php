<?php
namespace app\db;

use Exception;

class DataBase extends \PDO
{
    // const HOST = '127.0.0.1';
    // const DB_NAME = 'interviu_emag';
    // const USER = 'root';
    // const PASSWORD = '';

    public $host;
    public $dbName;
    public $user;
    public $pass;

    public function __construct()
    {
        $this->host = env('DB_HOST');
        $this->dbName = env('DB_NAME');
        $this->user = env('DB_USER');
        $this->pass = env('DB_PASS');
        try {
            parent::__construct('mysql:host=' . $this->host . ';dbname=' . $this->dbName, $this->user, $this->pass);
        } catch (Exception $e) {
            // we need an exception handler
            throw $e;
        }
        return $this;
    }


}