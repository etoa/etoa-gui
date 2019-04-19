<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use EtoA\AbstractDbTestCase;

class TutorialUserProgressRepositoryTest extends AbstractDbTestCase
{
    /** @var TutorialUserProgressRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.tutorial.userprogressrepository'];
    }

    public function testHasReadTutorialNoProgress(): void
    {
        $this->assertFalse($this->repository->hasReadTutorial(1, 1));
    }

    /**
     * @dataProvider readTutorialDataProvider
     */
    public function testHasReadTutorial(bool $closed): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('tutorial_user_progress')
            ->values([
                'tup_user_id' => 1,
                'tup_tutorial_id' => 1,
                'tup_closed' => (int)$closed,
            ])->execute();

        $this->assertSame($closed, $this->repository->hasReadTutorial(1, 1));
    }

    public function readTutorialDataProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }

    public function testCloseTutorial(): void
    {
        $userId = 1;
        $tutorialId = 1;
        $this->connection
            ->createQueryBuilder()
            ->insert('tutorial_user_progress')
            ->values([
                'tup_user_id' => $userId,
                'tup_tutorial_id' => $tutorialId,
                'tup_closed' => 0,
            ])->execute();

        $this->repository->closeTutorial($userId, $tutorialId);

        $this->assertTrue($this->repository->hasReadTutorial($userId, $tutorialId));
    }
}
