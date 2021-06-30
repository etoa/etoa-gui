<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\WebTestCase;

class AllianceManagementServiceTest extends WebTestCase
{
    private AllianceManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[AllianceManagementService::class];
    }

    public function testCreate(): void
    {
        // given
        $tag = 'TES';
        $name = 'The Testers';
        $founderId = 1;

        $this->createUser($founderId, 'Tester');

        // when
        $alliance = $this->service->create($tag, $name, $founderId);

        // then
        $this->assertEquals($tag, $alliance->tag);
        $this->assertEquals($name, $alliance->name);
        $this->assertEquals($founderId, $alliance->founderId);

        $allianceRecord = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('alliances')
            ->where("alliance_id = " . $alliance->id)
            ->execute()
            ->fetchAssociative();

        $this->assertIsArray($allianceRecord);
        $this->assertEquals($tag, $allianceRecord['alliance_tag']);
        $this->assertEquals($name, $allianceRecord['alliance_name']);
        $this->assertEquals($founderId, $allianceRecord['alliance_founder_id']);
    }

    public function testRemove(): void
    {
        // given
        $id = 1;
        $tag = 'TES';
        $name = 'The Testers';
        $founderId = 1;

        $this->createUser($founderId, 'Tester');
        $this->createAlliance($id, $tag, $name, $founderId);

        // when
        $done = $this->service->remove($id);

        // then
        $this->assertTrue($done);

        $allianceRecord = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('alliances')
            ->where("alliance_id = " . $id)
            ->execute()
            ->fetchAssociative();

        $this->assertFalse($allianceRecord);
    }

    public function testAddMember(): void
    {
        // given
        $id = 1;
        $tag = 'TES';
        $name = 'The Testers';
        $founderId = 1;
        $memberId = 2;

        $this->createUser($founderId, 'Founder', $id);
        $this->createUser($memberId, 'Member');
        $this->createAlliance($id, $tag, $name, $founderId);

        // when
        $this->service->addMember($id, $memberId);

        // then
        $memberRecord = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where("user_id = " . $memberId)
            ->execute()
            ->fetchAssociative();

        $this->assertEquals($id, $memberRecord['user_alliance_id']);
    }

    public function testKickMember(): void
    {
        // given
        $id = 1;
        $tag = 'TES';
        $name = 'The Testers';
        $founderId = 1;
        $memberId = 2;

        $this->createUser($founderId, 'Founder', $id);
        $this->createUser($memberId, 'Member', $id);
        $this->createAlliance($id, $tag, $name, $founderId);

        // when
        $this->service->kickMember($id, $memberId);

        // then
        $memberRecord = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where("user_id = " . $memberId)
            ->execute()
            ->fetchAssociative();

        $this->assertEquals(0, $memberRecord['user_alliance_id']);
    }

    private function createUser(int $userId, string $nick, int $allianceId = 0): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('users')
            ->values([
                'user_id' => ':userId',
                'user_nick' => ':nick',
                'user_alliance_id' => ':allianceId',
            ])
            ->setParameters([
                'userId' => $userId,
                'nick' => $nick,
                'allianceId' => $allianceId,
            ])
            ->execute();
    }

    private function createAlliance(int $allianceId, string $name, string $tag, int $founderId): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('alliances')
            ->values([
                'alliance_id' => ':allianceId',
                'alliance_name' => ':name',
                'alliance_tag' => ':tag',
                'alliance_founder_id' => ':founderId',
            ])
            ->setParameters([
                'allianceId' => $allianceId,
                'name' => $name,
                'tag' => $tag,
                'founderId' => $founderId,
            ])
            ->execute();
    }
}
