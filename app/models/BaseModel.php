<?php
namespace app\models;

use app\App;
use app\db\DataBase;
use Countable;

class BaseModel implements ModelInterface
{
    public $db;
    public $id;
    public $isNewRecord = true;

    /**
     * BaseModel constructor.
     * @return $this for chain effect
     */
    public function __construct()
    {
        $this->db = new DataBase();
        return $this;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
        return $this;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function load($params)
    {
        if ($params) {
            foreach ($params as $key => $value) {
                $this->__set($key, $value);
            }
        }
        return true;
    }

    public static function findById($id, $class = '')
    {
        $sql = 'SELECT * FROM `' . static::tableName() . '` WHERE id = :id';
        $query = App::$app->db->prepare($sql);
        $query->bindParam(':id', $id, DataBase::PARAM_INT);
        if (!$class) {
            $class = get_called_class();
        }
        $query->execute();
        return $query->fetchObject($class);
    }

    public static function findAll($condition = [], $returnQuery = false)
    {

        $sql = 'SELECT * FROM `' . static::tableName() . '`';
        $params = [];
        if ($condition instanceof Countable) {
            foreach ($condition as $key => $val) {
                $sql .= ' WHERE ' . $key . ' = :'.$key;
                $params[':'.$key] = $val;
            }

        }
        $query = App::getApp()->db->prepare($sql);
        foreach ($params as $key => $val) {
            $query->bindParam($key, $val);
        }
        
        if ($returnQuery) {
            return $query;
        }
        $query->execute();
        return $query->fetchAll(DataBase::FETCH_CLASS, get_called_class());
    }

    public function save() {}

    public static function tableName():string{
        return get_called_class()::tableName();
    }

    public function buttons()
    {
        return '<a href="' . App::urlTo('/' . App::getApp()->controllerID . '/edit?id='. $this->id) . '"> Editeaza </a> | ' . '<a href="' . App::urlTo('/' . App::getApp()->controllerID . '/delete?id='. $this->id) . '"> Sterge </a>';
    }

    public function delete()
    {
        $sql = 'DELETE FROM `' . static::tableName() . '` WHERE id = :id';

        $query = App::getApp()->db->prepare($sql);

        $query->bindParam(':id', $this->id, DataBase::PARAM_INT);

        return $query->execute();
    }
}