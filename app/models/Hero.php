<?php

namespace app\models;

use app\db\DataBase;

class Hero extends BaseModel
{
    public $name;
    public $health;
    public $strength;
    public $defence;
    public $speed;
    public $luck;
    public $attac_round = 1;
    public $hasToAttack;
    public $skills;
    public $default_skills = [1, 2];

    public static function rangeStats()
    {
        return [
            'health' => ['min' => 70, 'max' => 100],
            'strength' => ['min' => 70, 'max' => 80],
            'defence' => ['min' => 45, 'max' => 55],
            'speed' => ['min' => 40, 'max' => 50],
            'luck' => ['min' => 10, 'max' => 30],
        ];
    }

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
        return "hero";
    }

    public static function init()
    {
        $model = new Hero();

        foreach (static::rangeStats() as $property => $range) {
            $model->{$property} = random_int($range['min'], $range['max']);
        }
        $model->name = 'Orderus';

        $model->save();
        return $model;
    }

    public function save()
    {
        if ($this->isNewRecord) {
            $sql = 'INSERT INTO `' . static::tableName() . '` (`id`, `name`, `health`, `strength`, `defence`, `speed`, `luck` ) VALUES (NULL, :n , :heal , :strng , :def , :sp , :luc )';
        } else {
            $sql = 'UPDATE `' . static::tableName() . '` SET `name` = :n , `health` = :heal , `strength` = :strng , `defence` = :def , `speed` = :sp, `luck` = :luc WHERE `id` = :id';
        }
        try {
            $query = $this->db->prepare($sql);
            if (!$this->isNewRecord) {
                $query->bindParam(':id', $this->id, DataBase::PARAM_INT);
            }
            $query->bindParam(':n', $this->name, DataBase::PARAM_STR);
            $query->bindParam(':heal', $this->health, DataBase::PARAM_INT);
            $query->bindParam(':strng', $this->strength, DataBase::PARAM_INT);
            $query->bindParam(':def', $this->defence, DataBase::PARAM_INT);
            $query->bindParam(':sp', $this->speed, DataBase::PARAM_INT);
            $query->bindParam(':luc', $this->luck, DataBase::PARAM_INT);
            $query->execute();
            if ($this->isNewRecord) {
                $this->id = $this->db->lastInsertId();
                $skParams = [];
                $i = 1;
                $sql = 'INSERT INTO hero_skill(hero_id, skill_id) VALUES ';
                foreach ($this->default_skills as $sid) {
                    if ($i == sizeof($this->default_skills)) {
                        $sql .= '(:h_id , :s_id' . $sid . ' )';
                    } else {
                        $sql .= '(:h_id , :s_id' . $sid . ' ), ';
                    }
                    $skParams[':s_id' . $sid] = $sid;
                    $i++;
                }
                $query = $this->db->prepare($sql);
                foreach ($skParams as $key => $val) {
                    $query->bindParam($key, $val, DataBase::PARAM_INT);
                }
                $query->bindParam(':h_id', $this->id, DataBase::PARAM_INT);
                $query->execute();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function getSkills()
    {
        $sql = 'SELECT ' . Skill::tableName() . '.* FROM ' . Skill::tableName() . ' JOIN hero_skill ON ' . Skill::tableName() . '.id = hero_skill.skill_id AND hero_skill.hero_id = :h_id';
        $query = $this->db->prepare($sql);
        $query->bindParam(':h_id', $this->id, DataBase::PARAM_INT);
        $query->execute();
        $this->rounds =  $query->fetchAll(DataBase::FETCH_CLASS, Round::class);
        return $this->rounds;
    }
}
