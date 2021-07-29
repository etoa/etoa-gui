<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\AbstractDbTestCase;

class AllianceBoardTopicRepositoryTest extends AbstractDbTestCase
{
    private AllianceBoardTopicRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceBoardTopicRepository::class];
    }

    public function testGetTopicPostCounts(): void
    {
        $topicId = $this->repository->addTopic('Subject', 0, 1, 2, 'Nick');

        $this->assertSame([$topicId => 0], $this->repository->getTopicPostCounts([$topicId]));
    }

    public function testGetTopics(): void
    {
        $this->repository->addTopic('Subject', 0, 1, 2, 'Nick');

        $this->assertNotEmpty($this->repository->getTopics(1));
    }

    public function testGetTopic(): void
    {
        $topicId = $this->repository->addTopic('Subject', 0, 1, 2, 'Nick');

        $this->assertNotNull($this->repository->getTopic($topicId));
    }

    public function testGetTopicBnd(): void
    {
        $topicId = $this->repository->addTopic('Subject', 99, 0, 2, 'Nick');

        $this->assertNotNull($this->repository->getTopic($topicId, 99));
    }

    public function testIncreaseTopicCount(): void
    {
        $topicId = $this->repository->addTopic('Subject', 0, 1, 2, 'Nick');

        $this->repository->increaseTopicCount($topicId);

        $topic = $this->repository->getTopic($topicId);
        $this->assertNotNull($topic);
        $this->assertSame(1, $topic->count);
    }

    public function testDeleteTopic(): void
    {
        $topicId = $this->repository->addTopic('Subject', 0, 1, 2, 'Nick');

        $this->assertNotNull($this->repository->getTopic($topicId));

        $this->repository->deleteTopic($topicId);

        $this->assertNull($this->repository->getTopic($topicId));
    }

    public function testDeleteBndTopic(): void
    {
        $topicId = $this->repository->addTopic('Subject', 99, 0, 2, 'Nick');

        $this->assertNotNull($this->repository->getTopic($topicId, 99));

        $this->repository->deleteBndTopic(99);

        $this->assertNull($this->repository->getTopic($topicId, 99));
    }
}
