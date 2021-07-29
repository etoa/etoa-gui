<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\AbstractDbTestCase;

class AllianceBoardCategoryRepositoryTest extends AbstractDbTestCase
{
    private AllianceBoardCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceBoardCategoryRepository::class];
    }

    public function testGetCategoryTopicCounts(): void
    {
        $categoryId = $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->assertSame([$categoryId => 0], $this->repository->getCategoryTopicCounts([$categoryId]));
    }

    public function testGetCategoryPostCounts(): void
    {
        $categoryId = $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->assertSame([$categoryId => 0], $this->repository->getCategoryPostCounts([$categoryId]));
    }

    public function testGetCategories(): void
    {
        $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->assertNotEmpty($this->repository->getCategories(2));
    }

    public function testGetCategory(): void
    {
        $categoryId = $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->assertNotNull($this->repository->getCategory($categoryId, 2));
    }

    public function testGetCategoryId(): void
    {
        $categoryId = $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->assertSame([$categoryId], $this->repository->getCategoryIds(2));
    }

    public function testDeleteCategory(): void
    {
        $categoryId = $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->repository->deleteCategory(-1, 2);
        $this->assertNotNull($this->repository->getCategory($categoryId, 2));

        $this->repository->deleteCategory($categoryId, 2);
        $this->assertNull($this->repository->getCategory($categoryId, 2));
    }

    public function testDeleteAllCategories(): void
    {
        $categoryId = $this->repository->addCategory('Name', 'Description', 1, 'bullet', 2);

        $this->repository->deleteAllCategories(1);
        $this->assertNotNull($this->repository->getCategory($categoryId, 2));

        $this->repository->deleteAllCategories(2);
        $this->assertNull($this->repository->getCategory($categoryId, 2));
    }
}
