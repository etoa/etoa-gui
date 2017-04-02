<?php

namespace EtoA\Tutorial;

use EtoA\AbstractDbTestCase;

class TutorialUserProgressRepositoryTest extends AbstractDbTestCase
{
    /** @var TutorialUserProgressRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.tutorial.userprogressrepository'];
    }

    public function testHasReadTutorialNoProgress()
    {
        $this->assertFalse($this->repository->hasReadTutorial(1, 1));
    }

    /**
     * @dataProvider readTutorialDataProvider
     */
    public function testHasReadTutorial($closed)
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

    public function readTutorialDataProvider()
    {
        return [
            [false],
            [true],
        ];
    }
}
