<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceDiplomacy
{
    public int $id;
    public string $name;
    public int $alliance1Id;
    public int $alliance2Id;
    public ?string $alliance1Name;
    public ?string $alliance2Name;
    public ?string $alliance1Tag;
    public ?string $alliance2Tag;
    public int $level;
    public string $text;
    public int $date;
    public string $publicText;
    public int $points;
    public int $diplomatId;
    public int $otherAllianceId;
    public ?string $otherAllianceName;
    public ?string $otherAllianceTag;
    public int $otherAlliancePoints = 0;
    public int $otherAllianceAveragePoints = 0;

    public function __construct(array $data, int $allianceId)
    {
        $this->id = (int) $data['alliance_bnd_id'];
        $this->name = $data['alliance_bnd_name'];
        $this->alliance1Id = (int) $data['alliance_bnd_alliance_id1'];
        $this->alliance2Id = (int) $data['alliance_bnd_alliance_id2'];
        $this->alliance1Name = $data['alliance1Name'];
        $this->alliance2Name = $data['alliance2Name'];
        $this->alliance1Tag = $data['alliance1Tag'];
        $this->alliance2Tag = $data['alliance2Tag'];
        $this->level = (int) $data['alliance_bnd_level'];
        $this->text = $data['alliance_bnd_text'];
        $this->date = (int) $data['alliance_bnd_date'];
        $this->publicText = $data['alliance_bnd_text_pub'];
        $this->points = (int) $data['alliance_bnd_points'];
        $this->diplomatId = (int) $data['alliance_bnd_diplomat_id'];

        $this->otherAllianceId = $allianceId === $this->alliance2Id ? $this->alliance1Id : $this->alliance2Id;
        $this->otherAllianceName = $allianceId === $this->alliance2Id ? $this->alliance1Name : $this->alliance2Name;
        $this->otherAllianceTag = $allianceId === $this->alliance2Id ? $this->alliance1Tag : $this->alliance2Tag;
    }
}
