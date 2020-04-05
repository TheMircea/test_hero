<?php

use app\App;
use app\models\Beast;
use app\models\Hero;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Init Game</title>
    <link rel="stylesheet" type="text/css" href="<?= App::urlTo('/assets/css/style.css') ?>">
</head>

<body>
    <div class="container">
        <h1>Welcome</h1>
        <hr>
        <p>A new hero and a beast have been created</p>
        <p>You can see thei stats below</p>
        <table>
            <thead>
                <tr>
                    <th>
                        Hero
                    </th>
                    <th>
                        Beast
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <?php foreach (Hero::rangeStats() as $property => $values) { ?>
                                <li>
                                    <?= ucfirst($property) ?> : <?= $hero->{$property} ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <?php foreach (Beast::rangeStats() as $property => $values) { ?>
                                <li>
                                    <?= ucfirst($property) ?> : <?= $beast->{$property} ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>By pressing start game, a new round will be initiate</p>
        <p><a href="<?= App::urlTo('/games/round?token='.$game->token) ?>"> Start Game </a></p>
    </div>
</body>

</html>