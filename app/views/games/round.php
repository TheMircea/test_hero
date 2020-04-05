<?php

use app\App;
use app\models\Beast;
use app\models\Hero;
use app\models\Round;
if ($game->end == 0) {
    $curentHero = json_decode($round->hero_stats, true);
    $curentBeast = json_decode($round->beast_stats, true);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rounds</title>
</head>
<body>
    <div class="container">
        <h1>Rounds</h1>
        <p>Statusul jocului: </p><?= $game->end == 1 ? 'A luat sfarsit' : 'In desfasurare' ?>
        <p><?= $game->end == 1 ? 'Winner: ' . $game->winner . '<br/> Rounds:' . $game->no_rounds : '' ?></p>
        <?php if ($game->end == 0) { ?>
        <a href="<?= App::urlTo('/games/round?token='.$game->token)?>">
            Urmatoarea runda
        </a>
        <?php } ?>
        <hr/>
        <?php if ($game->end == 0) { ?>
            <p>Runda currenta</p>
            <ul>
                <li>
                    Erou
                </li>
                <li>
                    <?= $curentHero['hasToAttack'] == 1 ? 'Ataca' : 'Se apara' ?>
                </li>
                <li>
                    <?= 'Viata: ' . $curentHero['health'] ?>
                </li>
            </ul>
            <ul>
                <li>
                    Bestie
                </li>
                <li>
                    <?= $curentBeast['hasToAttack'] == 1 ? 'Ataca' : 'Se apara' ?>
                </li>
                <li>
                    <?= 'Viata: ' . $curentBeast['health'] ?>
                </li>
            </ul>
        <?php } ?>
        <hr/>
        <table>
            <thead>
                <tr>
                    <th>Round No</th>
                    <th>Hero Stats</th>
                    <th>Beast Stats</th>
                    <th>Used Skills</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rounds as $item) { 
                    $hero = Round::parseStats($item->hero_stats);
                    $beast = Round::parseStats($item->beast_stats);
                ?>

                    <tr>
                        <td><?=$item->number ?></td>
                        <td>
                            <ul>
                                <?php foreach (Hero::rangeStats() as $property => $values) { ?>
                                    <li>
                                        <?= ucfirst($property) ?> : <?= $hero[$property] ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td>
                            <ul>
                                <?php foreach (Beast::rangeStats() as $property => $values) { ?>
                                    <li>
                                        <?= ucfirst($property) ?> : <?= $beast[$property] ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td>
                            <?php 
                                $skills = json_decode($item->used_skills, true);
                                if ($skills) {
                                    foreach ($skills as $skill) {
                                        echo $skill['name'] . "\n";
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>