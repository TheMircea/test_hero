<?php

namespace app\models;

use app\db\DataBase;

class Round extends BaseModel
{
    public $game_id;
    public $number;
    public $hero_stats;
    public $beast_stats;
    public $used_skills;

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
        return "round";
    }

    public function save()
    {
        if ($this->isNewRecord) {
            $sql = 'INSERT INTO `' . static::tableName() . '` (`id`, `game_id`, `number`, `hero_stats`, `beast_stats`, `used_skills`) VALUES (NULL, :g_id , :no , :hs , :bs , :us)';
        } else {
            $sql = 'UPDATE `' . static::tableName() . '` SET `game_id` = :g_id , `number` = :no , `hero_stats` = :hs , `beast_stats` = :bs , `used_skills` = :us WHERE `id` = :id';
        }
        try {
            $query = $this->db->prepare($sql);
            if (!$this->isNewRecord) {
                $query->bindParam(':id', $this->id, DataBase::PARAM_INT);
            }
            $query->bindParam(':g_id', $this->game_id, DataBase::PARAM_STR);
            $query->bindParam(':no', $this->number, DataBase::PARAM_STR);
            $query->bindParam(':hs', $this->hero_stats, DataBase::PARAM_STR);
            $query->bindParam(':bs', $this->beast_stats, DataBase::PARAM_STR);
            $query->bindParam(':us', $this->used_skills, DataBase::PARAM_STR);
            $query->execute();
            if ($this->isNewRecord) {
                $this->id = $this->db->lastInsertId();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public static function parseStats($stats)
    {
        return json_decode($stats, true);
    }

    public static function newRound(Game $game, $rounds)
    {
        $currentRound = new Round();

        if ($rounds) {
            $lastRound = $rounds[sizeof($rounds)-1];
            $currentRound->number = $lastRound->number + 1;
            $hero = static::parseStats($lastRound->hero_stats);
            $beast = static::parseStats($lastRound->beast_stats);
            $getWhoAtackFirst = false;
        } else {
            $hero = $game->getHeroStats();
            $beast = $game->getBeastStats();
            $currentRound->number = 1;
            $getWhoAtackFirst = true;
        }

        if ($getWhoAtackFirst) {
            if ($hero['speed'] == $beast['speed']) {
                $beast['hasToAttack'] = ($beast['luck'] > $hero['luck']) ? 1 : 0;
                $hero['hasToAttack'] = ($beast['hasToAttack'] == 1) ? 0 : 1;
            } else {
                $beast['hasToAttack'] = ($beast['speed'] > $hero['speed']) ? 1 : 0;
                $hero['hasToAttack'] = ($beast['hasToAttack'] == 1) ? 0 : 1;
            }
        }
        $dmg = ($beast['hasToAttack'] == 1) ? ($beast['strength'] - $hero['defence']) : ($hero['strength'] - $beast['defence']);

        $hero['health'] = ($beast['hasToAttack'] == 1) ? $hero['health'] - $dmg : $hero['health'];
        $beast['health'] = ($hero['hasToAttack'] == 1) ? $beast['health'] - $dmg : $beast['health'];

        $skills = $game->getHero()->getSkills();
        $usedSkills = [];
        foreach ($skills as $skill) {
            $chance = random_int(0, 100);
            if ($skill->chance > $chance) {
                if ($hero['hasToAttack'] == 1 && $skill->active_on == Skill::ATTACK) {
                    /**
                     * Stim care este atacul asa ca o sa-l facem sa atace de 2 ori... aici trebuie implementata logica de atac per efect
                     */
                    $beast['health'] -= $dmg;
                }
                if ($hero['hasToAttack'] == 0 && $skill->active_on == Skill::DEFENCE) {
                    $hero['health'] += $dmg / 2;
                }
                $usedSkills[] = [
                    'name' => $skill->name,
                    'chance' => $skill->chance,
                ];
            }
        }
        $currentRound->game_id = $game->id;
        $hero['hasToAttack'] = $hero['hasToAttack'] ==  1 ? 0 : 1;
        $beast['hasToAttack'] = $beast['hasToAttack'] ==  0 ? 1 : 0;
        $currentRound->beast_stats = json_encode($beast);
        $currentRound->hero_stats = json_encode($hero);
        $currentRound->used_skills = json_encode($usedSkills);

        $currentRound->save();

        return $currentRound;
    }
}
