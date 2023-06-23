<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\SymfonyWebTestCase;

class GameStatsGeneratorTest extends SymfonyWebTestCase
{
    private GameStatsGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = self::getContainer()->get(GameStatsGenerator::class);
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
