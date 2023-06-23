<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\SymfonyWebTestCase;

class DefenseCategoryRepositoryTest extends SymfonyWebTestCase
{
    private DefenseCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(DefenseCategoryRepository::class);
    }

    public function testGetAllCategories(): void
    {
        $categories = $this->repository->getAllCategories();

        $this->assertNotEmpty($categories);
    }
}
