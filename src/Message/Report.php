<?php declare(strict_types=1);

namespace EtoA\Message;

class Report
{
    public int $id;
    public int $timestamp;
    public string $type;
    public bool $read;
    public bool $deleted;
    public bool $archived;
    public int $userId;
    public int $allianceId;
    public ?string $content;
    public int $entity1Id;
    public int $entity2Id;
    public int $opponentId;

    public static function createFromArray(array $data): Report
    {
        $report = new Report();
        $report->id = (int) $data['id'];
        $report->timestamp = (int) $data['timestamp'];
        $report->type = $data['type'];
        $report->read = (bool) $data['read'];
        $report->deleted = (bool) $data['deleted'];
        $report->archived = (bool) $data['archived'];
        $report->userId = (int) $data['user_id'];
        $report->allianceId = (int) $data['alliance_id'];
        $report->content = $data['content'];
        $report->entity1Id = (int) $data['entity1_id'];
        $report->entity2Id = (int) $data['entity2_id'];
        $report->opponentId = (int) $data['opponent1_id'];

        return $report;
    }

    /**
     * @return int[]
     */
    public function getTransformedDataFromContent(): array
    {
        if ($this->content !== null) {
            return array_map(fn (string $value) => (int) $value, explode(':', $this->content));
        }

        return [];
    }
}
