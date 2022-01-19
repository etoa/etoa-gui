<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\SymfonyWebTestCase;

class AllianceHistoryRepositoryTest extends SymfonyWebTestCase
{
    private AllianceHistoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(AllianceHistoryRepository::class);
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

        $entry = $this->getConnection()->fetchAssociative('SELECT * FROM alliance_history WHERE history_id = :id', ['id' => $id]);

        $this->assertNotFalse($entry);
        $this->assertEquals($allianceId, $entry['history_alliance_id']);
        $this->assertEquals($text, $entry['history_text']);
    }
}
