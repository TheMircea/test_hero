<?php

namespace app;

use app\db\DataBase;
use Exception;

class App
{
    /** @var \app\db\DataBase */
    public $db;

    public $controller;

    public $controllerID;

    public $action;

    public $url;

    public $session;

    public $baseUrl;

    public $hosts;

    /** @var \app\App */
    public static $app = null;

    // public function __construct($baseUrl = '', $hosts = [])
    // {
    //     $this->baseUrl = $baseUrl;
    //     $this->parseRequest();

    //     $parser = parse_url($this->url);
    //     // print_r($parser);
    //     // if (!in_array($parser['host'], $hosts, true)) {
    //     //     throw new Exception('Host not allowed');
    //     // }

    //     $pathArray = explode('/', $parser['path']);

    //     if ($this->baseUrl && $this->baseUrl !== $pathArray[1]) {
    //         throw new Exception('Something is fishi');
    //     }

    //     $controllerPath = 'app\\controllers\\' . ucfirst($pathArray[2]) . 'Controller';

    //     if (!\class_exists($controllerPath)) {
    //         throw new Exception('Controller not found');
    //     }

    //     $this->controller = new $controllerPath;

    //     if (isset($pathArray[3])) {
    //         if (method_exists($this->controller, $pathArray[3])) {
    //             $this->action = $pathArray[3];
    //         } else {
    //             $this->action = 'index';
    //         }
    //         $args = null;
    //         if (isset($parser['query'])) {
    //             parse_str($parser['query'], $args);
    //         }
    //         return $this->controller->{$this->action}($args);
    //         // return call_user_func([
    //         //     get_class($this->controller),
    //         //     $this->action
    //         // ], $args);
    //     }
    //     $this->db = new DataBase();
    //     $this::$app = $this;
    // }

    public static function init($baseUrl = '', $hosts = [])
    {
        $app = new App();
        $app::$app = $app;
        $app->baseUrl = $baseUrl;
        $app->parseRequest();

        $parser = parse_url($app->url);
        // print_r($parser);
        // if (!in_array($parser['host'], $hosts, true)) {
        //     throw new Exception('Host not allowed');
        // }

        $pathArray = explode('/', $parser['path']);

        if ($app->baseUrl && $app->baseUrl !== $pathArray[1]) {
            throw new Exception('Something is fishi');
        }

        if (empty($pathArray[2])) {
            $pathArray[2] = 'users';
        }

        $controllerPath = 'app\\controllers\\' . ucfirst($pathArray[2]) . 'Controller';

        if (!\class_exists($controllerPath)) {
            throw new Exception('Controller not found');
        }

        $app->controller = new $controllerPath;
        $app->controllerID = $pathArray[2];
        $app->db = new DataBase();
        $app->session = Session::getInstance();

        if (isset($pathArray[3]) && method_exists($app->controller, $pathArray[3])) {
            $app->action = $pathArray[3];
        } else {
            $app->action = 'index';
        }
        $args = null;
        if (isset($parser['query'])) {
            parse_str($parser['query'], $args);
        }
        return $app->controller->{$app->action}($args);
        // return call_user_func([
        //     get_class($app->controller),
        //     $app->action
        // ], $args);
    }

    public function getSession()
    {
        return $this->session;
    }

    public function __get($name)
    {
        $name = lcfirst($name);
        return $this->{$name};
    }

    public function __set($name, $val)
    {
        $name = lcfirst($name);
        $this->{$name} = $val;
        return $this;
    }

    /**
     * @return app\App
     */
    public static function getApp()
    {
        if (self::$app == null) {
            self::$app = new App();
        }
        return self::$app;
    }

    public function parseRequest()
    {
        $this->url = $this->fullUrl();
    }

    public function urlOrigin()
    {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = $_SERVER['HTTP_HOST'];
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    public function fullUrl()
    {
        return $this->urlOrigin() . $_SERVER['REQUEST_URI'];
    }

    public static function urlTo($path)
    {
        return '/' . env('URL_BASE') . $path;
    }
}
