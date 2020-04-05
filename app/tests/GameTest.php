<?php

use app\models\Game;
use app\models\Hero;

final class GameTest extends \Codeception\Test\Unit
{
    public function testHeroInit(): void
    {
        $author = new Hero();
        $load = $author->load([
            'token' => 'test',
            'last_name' => 'test',
            'phone' => '080808',
            'email' => 'test@test',
            'birthday' => '2020-01-01',
        ]);

        $this->assertIsBool($load);

        $this->assertEquals('test', $author->first_name);

        $save = $author->save();

        $this->assertTrue($save);
    }
}
