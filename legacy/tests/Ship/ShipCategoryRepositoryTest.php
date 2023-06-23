<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\SymfonyWebTestCase;

class ShipCategoryRepositoryTest extends SymfonyWebTestCase
{
    private ShipCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(ShipCategoryRepository::class);
    }

    public function testGetAllCategories(): void
    {
        $categories = $this->repository->getAllCategories();

        $this->assertNotEmpty($categories);
    }

    public function testGetCategory(): void
    {
        $category = $this->repository->getCategory(1);

        $this->assertNotNull($category);
    }
}
