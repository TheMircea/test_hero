<?php

namespace app\models;

use app\db\DataBase;
use app\helpers\Helper;
use app\App;
use PDO;

class Skill extends BaseModel
{
    public $name;
    public $attribute;
    public $effect;
    public $chance;
    public $active_on;

    const ATTACK = 1;
    const DEFENCE = 0;

    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    /**
     * return table name that is represented by this class
     * @return string
     */
    public static function tableName(): string
    {
        return "skill";
    }

    public function save()
    {
        if ($this->isNewRecord) {
            $sql = 'INSERT INTO `' . static::tableName() . '` (`id`, `name`, `attribute`, `effect`, `chance`, `active_on`) VALUES (NULL, :n, :attr, :eff, :ch, :active )';
        } else {
            $sql = 'UPDATE `' . static::tableName() . '` SET `name` = :n, `attribute` = :attr, `effect` = :eff, `chance` = :ch, active_on = :active WHERE `id` = :id';
        }
        try {
            $query = $this->db->prepare($sql);
            if (!$this->isNewRecord) {
                $query->bindParam(':id', $this->id, DataBase::PARAM_INT);
            }
            $query->bindParam(':n', $this->name, DataBase::PARAM_STR);
            $query->bindParam(':attr', $this->attribute, DataBase::PARAM_STR);
            $query->bindParam(':eff', $this->effect, DataBase::PARAM_STR);
            $query->bindParam(':ch', $this->chance, DataBase::PARAM_INT);
            $query->bindParam(':active', $this->active_on, DataBase::PARAM_INT);
            $query->execute();
            if ($this->isNewRecord) {
                $this->id = $this->db->lastInsertId();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
