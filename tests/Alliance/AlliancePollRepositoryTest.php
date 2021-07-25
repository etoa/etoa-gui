<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AlliancePollRepositoryTest extends AbstractDbTestCase
{
    private AlliancePollRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AlliancePollRepository::class];
    }

    public function testGetPolls(): void
    {
        $this->repository->add(1, 'Title', 'Question', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer');

        $this->assertNotEmpty($this->repository->getPolls(1));
    }

    public function testGetPoll(): void
    {
        $pollId = $this->repository->add(1, 'Title', 'Question', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer');

        $this->assertNotNull($this->repository->getPoll($pollId, 1));

        $this->repository->deletePoll($pollId, 1);

        $this->assertNull($this->repository->getPoll($pollId, 1));
    }

    public function testAddVote(): void
    {
        $pollId = $this->repository->add(1, 'Title', 'Question', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer');

        $this->repository->addVote($pollId, 1, 2, 1);

        $poll = $this->repository->getPoll($pollId, 1);
        $this->assertNotNull($poll);

        $this->assertSame(1, $poll->answer1Count);
    }

    public function testUpdateActive(): void
    {
        $pollId = $this->repository->add(1, 'Title', 'Question', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer');

        $this->repository->updateActive($pollId, 1, false);

        $poll = $this->repository->getPoll($pollId, 1);
        $this->assertNotNull($poll);

        $this->assertFalse($poll->active);
    }

    public function testUpdatePoll(): void
    {
        $pollId = $this->repository->add(1, 'Title', 'Question', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer', 'Answer');

        $this->repository->updatePoll($pollId, 1, 'Title New', 'Question New', 'Answer 1', 'Answer 2', 'Answer 3', 'Answer 4', 'Answer 5', 'Answer 6', 'Answer 7', 'Answer 8');

        $poll = $this->repository->getPoll($pollId, 1);
        $this->assertNotNull($poll);

        $this->assertSame('Title New', $poll->title);
        $this->assertSame('Question New', $poll->question);
        $this->assertSame('Answer 1', $poll->answer1);
        $this->assertSame('Answer 2', $poll->answer2);
        $this->assertSame('Answer 3', $poll->answer3);
        $this->assertSame('Answer 4', $poll->answer4);
        $this->assertSame('Answer 5', $poll->answer5);
        $this->assertSame('Answer 6', $poll->answer6);
        $this->assertSame('Answer 7', $poll->answer7);
        $this->assertSame('Answer 8', $poll->answer8);
    }
}
