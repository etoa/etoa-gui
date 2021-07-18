<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\WebTestCase;

class GameStatsGeneratorTest extends WebTestCase
{
    private GameStatsGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = $this->app[GameStatsGenerator::class];
    }

    public function testSmokeTest(): void
    {
        // given

        // when
        $out = $this->generator->generate();

        // then
        $this->assertStringContainsString('Erstellt am', $out);
    }
}
