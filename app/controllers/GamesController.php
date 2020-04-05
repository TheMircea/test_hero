<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Hero;
use app\models\Beast;
use app\models\Game;
use app\models\Round;
use Exception;

class GamesController extends BaseController
{

    public function index()
    {
        $hero = Hero::init();
        $beast = Beast::init();
        $game = Game::init($hero, $beast);
        return $this->render('games/index', [
            'hero' => $hero,
            'beast' => $beast,
            'game' => $game,
        ]);
    }

    public function round($params)
    {
        $game = Game::findBytoken($params['token']);

        if ($game) {
            $rounds = $game->getRounds();
            if (!$game->isEnded()) {
                $round = Round::newRound($game, $rounds);
                print_r('aici');
            } else {
                $hero = json_decode($game->rounds[sizeof($game->rounds) - 1]->hero_stats, true);
                $game->isNewRecord = false;
                $game->winner = ($hero['health'] <= 0) ? 'Beast' : 'Hero';
                $game->end = 1;
                $game->no_rounds = sizeof($game->rounds);
                $game->save();
                $round = [];
            }
        } else {
            throw new Exception('Not found', 404);
        }
        return $this->render('games/round', [
            'game' => $game,
            'rounds' => $rounds,
            'round' => $round,
        ]);
    }
}
