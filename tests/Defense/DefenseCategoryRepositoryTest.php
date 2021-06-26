<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseCategoryRepositoryTest extends AbstractDbTestCase
{
    private DefenseCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.defense_category.repository'];
    }

    public function testGetAllCategories(): void
    {
        $categories = $this->repository->getAllCategories();

        $this->assertNotEmpty($categories);
    }
}
