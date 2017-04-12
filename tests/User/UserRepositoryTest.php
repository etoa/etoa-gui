<?php

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserRepositoryTest extends AbstractDbTestCase
{
    /** @var UserRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.user.repository'];
    }

    public function testGetDiscoverMask()
    {
        $userId = 10;
        $discoverMask = '000000000';

        $this->connection
            ->createQueryBuilder()
            ->insert('users')
            ->values([
                'user_id' => ':userId',
                'discoverymask' => ':discoverymask',
            ])->setParameters([
                'userId' => $userId,
                'discoverymask' => $discoverMask,
            ])->execute();

        $this->assertSame($discoverMask, $this->repository->getDiscoverMask($userId));
    }

    public function testGetPoints()
    {
        $userId = 10;
        $points = 100;

        $this->connection
            ->createQueryBuilder()
            ->insert('users')
            ->values([
                'user_id' => ':userId',
                'user_points' => ':points',
            ])->setParameters([
                'userId' => $userId,
                'points' => $points,
            ])->execute();

        $this->assertSame($points, $this->repository->getPoints($userId));
    }

    public function testGetAllianceId()
    {
        $userId = 10;
        $allianceId = 100;

        $this->connection
            ->createQueryBuilder()
            ->insert('users')
            ->values([
                'user_id' => ':userId',
                'user_alliance_id' => ':allianceId',
            ])->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])->execute();

        $this->assertSame($allianceId, $this->repository->getAllianceId($userId));
    }
}
