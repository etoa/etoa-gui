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
        // given
        $allianceId = 1;
        $text = 'text';

        // when
        $id = $this->repository->addEntry($allianceId, $text);

        // then
        $this->assertGreaterThan(0, $id);

        $entry = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('alliance_history')
            ->where('history_id = ' . $id)
            ->execute()
            ->fetchAssociative();

        $this->assertNotFalse($entry);
        $this->assertEquals($allianceId, $entry['history_alliance_id']);
        $this->assertEquals($text, $entry['history_text']);
    }
}
