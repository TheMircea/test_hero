<?php

use app\models\Beast;
use app\models\Game;
use app\models\Hero;
use app\models\Round;

final class GameTest extends \Codeception\Test\Unit
{
    public function testHeroInit(): void
    {
        $hero = Hero::init();
        
        $this->assertInstanceOf(Hero::class, $hero);
        $beast = Beast::init();
        
        $this->assertInstanceOf(Beast::class, $beast);
        
        $game = Game::init($hero, $beast);
        
        $this->assertInstanceOf(Game::class, $game);
        
        $round = Round::newRound($game);

        $this->assertInstanceOf(Round::class, $round);
        
        $rounds = $game->rounds;
        $this->assertIsArray($rounds);
        
        if ($hero->speed > $beast->speed) {
            $hero = json_decode($round->hero_stats, true);
            $this->assertEquals(1, $hero['hasToAttack']);
            $bstats = json_decode($round->beast_stats, true);
            $this->assertGreaterThan($beast->health, $bstats['health']);

        }
    }
}
