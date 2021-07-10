<?php declare(strict_types=1);

namespace EtoA\Tip;

use EtoA\AbstractDbTestCase;

class TipRepositoryTest extends AbstractDbTestCase
{
    private TipRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[TipRepository::class];
    }

    public function testGetRandomTipText(): void
    {
        $text = $this->repository->getRandomTipText();

        $this->assertNotNull($text);
    }
}
