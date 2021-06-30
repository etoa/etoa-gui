<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceHistoryRepositoryTest extends AbstractDbTestCase
{
    private AllianceHistoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceHistoryRepository::class];
    }

    public function testAddEntry(): void
    {
        $allianceId = 1;
        $text = 'test';

        // when
        $id = $this->repository->addEntry($allianceId, $text);

        // then
        $record = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('alliance_history')
            ->where('history_id = ' . $id)
            ->execute()
            ->fetchAssociative();

        $this->assertEquals($allianceId, $record['history_alliance_id']);
        $this->assertEquals($text, $record['history_text']);
    }
}
