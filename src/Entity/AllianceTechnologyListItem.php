<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceTechnologyListRepository;
use EtoA\Alliance\AllianceWithMemberCount;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceTechnologyListRepository::class)]
#[ORM\Table(name: 'alliance_techlist')]
class AllianceTechnologyListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_techlist_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "alliance_techlist_alliance_id", type: "integer")]
    private int $allianceId;

    #[ORM\Column(name: "alliance_techlist_tech_id", type: "integer")]
    private int $technologyId;

    #[ORM\Column(name: "alliance_techlist_current_level", type: "integer")]
    private int $level;

    #[ORM\Column(name: "alliance_techlist_build_start_time", type: "integer")]
    private int $buildStartTime;

    #[ORM\Column(name: "alliance_techlist_build_end_time", type: "integer")]
    private int $buildEndTime;

    #[ORM\Column(name: "alliance_techlist_member_for", type: "integer")]
    private int $memberFor;

    public static function createFromAlliance(AllianceWithMemberCount $alliance): AllianceTechnologyListItem
    {
        $item = new AllianceTechnologyListItem();
        $item->id = 0;
        $item->allianceId = $alliance->getId();
        $item->technologyId = 0;
        $item->level = 0;
        $item->buildStartTime = 0;
        $item->buildEndTime = 0;
        $item->memberFor = $alliance->memberCount;

        return $item;
    }

    public static function createFromData(array $data): AllianceTechnologyListItem
    {
        $item = new AllianceTechnologyListItem();
        $item->id = (int) $data['alliance_techlist_id'];
        $item->allianceId = (int) $data['alliance_techlist_alliance_id'];
        $item->technologyId = (int) $data['alliance_techlist_tech_id'];
        $item->level = (int) $data['alliance_techlist_current_level'];
        $item->buildStartTime = (int) $data['alliance_techlist_build_start_time'];
        $item->buildEndTime = (int) $data['alliance_techlist_build_end_time'];
        $item->memberFor = (int) $data['alliance_techlist_member_for'];

        return $item;
    }

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }
}
