<?php

namespace app\models;

use app\db\DataBase;
use app\helpers\Helper;
use app\App;
use PDO;

class Game extends BaseModel
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    public $token;
    public $hero_id;
    public $beast_id;
    public $hero;
    public $beast;
    public $rounds;
    public $winner = '';
    public $end = 0;
    public $no_rounds = 0;

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
        return "game";
    }

    public static function init(Hero $hero, Beast $beast)
    {
        $model = new Game();
        $model->token = Helper::generateRandomString();
        $model->hero_id = $hero->id;
        $model->beast_id = $beast->id;
        $model->save();
        return $model;
    }

    public function save()
    {
        if ($this->isNewRecord) {
            $sql = 'INSERT INTO `' . static::tableName() . '` (`id`, `token`, `hero_id`, `beast_id`, `winner`, `end`, `no_rounds`) VALUES (NULL, :tk, :h_id, :b_id, :win, :e, :nr)';
        } else {
            $sql = 'UPDATE `' . static::tableName() . '` SET `token` = :tk, `hero_id` = :h_id, `beast_id` = :b_id, `winner` = :win, `end` = :e, `no_rounds` = :nr WHERE `id` = :id';
        }
        try {
            $query = $this->db->prepare($sql);
            if (!$this->isNewRecord) {
                $query->bindParam(':id', $this->id, DataBase::PARAM_INT);
            }
            $query->bindParam(':tk', $this->token, DataBase::PARAM_STR);
            $query->bindParam(':h_id', $this->hero_id, DataBase::PARAM_INT);
            $query->bindParam(':b_id', $this->beast_id, DataBase::PARAM_INT);
            $query->bindParam(':win', $this->winner, DataBase::PARAM_STR);
            $query->bindParam(':e', $this->end, DataBase::PARAM_INT);
            $query->bindParam(':nr', $this->no_rounds, DataBase::PARAM_INT);
            $query->execute();
            if ($this->isNewRecord) {
                $this->id = $this->db->lastInsertId();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public static function findByToken($token)
    {
        $sql = 'SELECT * FROM `' . static::tableName() . '` WHERE token = :tk';
        $query = App::$app->db->prepare($sql);
        $query->bindParam(':tk', $token, DataBase::PARAM_STR);
        $query->execute();
        return $query->fetchObject(static::class);
    }

    public function getRounds()
    {
        $sql = 'SELECT ' . Round::tableName() . '.* FROM ' . Round::tableName() . ' JOIN ' . static::tableName() . ' ON ' . Round::tableName() . '.game_id = ' . static::tableName() . '.id AND ' . Round::tableName() . '.game_id = :g_id';
        $query = $this->db->prepare($sql);
        $query->bindParam(':g_id', $this->id, DataBase::PARAM_INT);
        $query->execute();
        $this->rounds =  $query->fetchAll(DataBase::FETCH_CLASS, Round::class);
        return $this->rounds;
    }

    public function getHero()
    {
        if (!$this->hero) {
            $this->__set('hero', Hero::findById($this->hero_id));
        }
        return $this->__get('hero');
    }

    public function getBeast()
    {
        if (!$this->beast) {
            $this->__set('beast',  Beast::findById($this->beast_id));
        }
        return $this->__get('beast');
    }

    public function getHeroStats()
    {
        if (!$this->__get('hero')) {
            $this->getHero();
        }
        $stats = [];
        foreach (Hero::rangeStats() as $key => $val) {
            $stats[$key] = $this->hero->{$key};
        }
        return $stats;
    }

    public function getBeastStats()
    {
        if (!$this->__get('beast')) {
            $this->getBeast();
        }
        $stats = [];
        foreach (Beast::rangeStats() as $key => $val) {
            $stats[$key] = $this->beast->{$key};
        }
        return $stats;
    }

    public function isEnded()
    {
        if (sizeof($this->rounds) >= 20) {
            return true;
        }
        if ($this->rounds){
            $hero = json_decode($this->rounds[sizeof($this->rounds) - 1]->hero_stats, true);
            $beast = json_decode($this->rounds[sizeof($this->rounds) - 1]->beast_stats, true);
        } else {
            return false;
        }
        if ($hero['health'] <= 0 || $beast['health'] <= 0) {
            return true;
        }
        return false;
    }
}
