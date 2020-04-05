<?php

namespace app\models;

use app\db\DataBase;

class Beast extends BaseModel
{
    public $health;
    public $strength;
    public $defence;
    public $speed;
    public $attac_round = 1;
    public $luck;
    public $hasToAttack;

    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    public static function rangeStats()
    {
        return [
            'health' => ['min' => 60, 'max' => 90],
            'strength' => ['min' => 60, 'max' => 90],
            'defence' => ['min' => 40, 'max' => 60],
            'speed' => ['min' => 40, 'max' => 60],
            'luck' => ['min' => 25, 'max' => 40],
        ];
    }

    public static function init()
    {
        $model = new Beast();
        foreach (static::rangeStats() as $property => $range) {
            $model->{$property} = random_int($range['min'], $range['max']);
        }
        $model->save();
        return $model;
    }

    /**
     * return table name that is represented by this class
     * @return string
     */
    public static function tableName(): string
    {
        return "beast";
    }

    public function save()
    {
        if ($this->isNewRecord) {
            $sql = 'INSERT INTO `' . static::tableName() . '` (`id`, `health`, `strength`, `defence`, `speed`, `luck`) VALUES (NULL, :heal , :strng , :def , :sp , :luc)';
        } else {
            $sql = 'UPDATE `' . static::tableName() . '` SET `health` = :heal , `strength` = :strng , `defence` = :def , `speed` = :sp, `luck` = :luc WHERE `id` = :id';
        }
        try {
            $query = $this->db->prepare($sql);
            if (!$this->isNewRecord) {
                $query->bindParam(':id', $this->id, DataBase::PARAM_INT);
            }

            $query->bindParam(':heal', $this->health, DataBase::PARAM_INT);
            $query->bindParam(':strng', $this->strength, DataBase::PARAM_INT);
            $query->bindParam(':def', $this->defence, DataBase::PARAM_INT);
            $query->bindParam(':sp', $this->speed, DataBase::PARAM_INT);
            $query->bindParam(':luc', $this->luck, DataBase::PARAM_INT);

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
