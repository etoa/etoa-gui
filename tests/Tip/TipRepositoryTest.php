<?php declare(strict_types=1);

namespace EtoA\Tip;

use EtoA\SymfonyWebTestCase;

class TipRepositoryTest extends SymfonyWebTestCase
{
    private TipRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(TipRepository::class);
    }

    public function testGetRandomTipText(): void
    {
        $text = $this->repository->getRandomTipText();

        $this->assertNotNull($text);
    }
}
