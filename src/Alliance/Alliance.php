<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Universe\Resources\BaseResources;

class Alliance
{
    public int $id;
    public string $tag;
    public string $name;
    public string $nameWithTag;
    public ?string $text;
    public ?string $image;
    public bool $imageCheck;
    public ?string $url;
    public int $motherId;
    public int $motherRequest;
    public bool $acceptApplications;
    public bool $acceptBnd;
    public bool $publicMemberList;
    public int $points;
    public int $currentRank;
    public int $lastRank;
    public int $founderId;
    public int $foundationTimestamp;
    public int $architectId;
    public int $technicianId;
    public int $diplomatId;
    public int $visits;
    public int $visitsExternal;
    public ?string $applicationTemplate;
    public int $resMetal;
    public int $resCrystal;
    public int $resPlastic;
    public int $resFuel;
    public int $resFood;
    public int $objectsForMembers;

    public function __construct(array $data)
    {
        $this->id = (int) $data['alliance_id'];
        $this->tag = $data['alliance_tag'];
        $this->name = $data['alliance_name'];
        $this->nameWithTag = sprintf('[%s] %s', $this->tag, $this->name);
        $this->text = $data['alliance_text'];
        $this->image = $data['alliance_img'];
        $this->imageCheck = (bool) $data['alliance_img_check'];
        $this->url = $data['alliance_url'];
        $this->motherId = (int) $data['alliance_mother'];
        $this->motherRequest = (int) $data['alliance_mother_request'];
        $this->acceptApplications = (bool) $data['alliance_accept_applications'];
        $this->acceptBnd = (bool) $data['alliance_accept_bnd'];
        $this->publicMemberList = (bool) $data['alliance_public_memberlist'];
        $this->points = (int) $data['alliance_points'];
        $this->currentRank = (int) $data['alliance_rank_current'];
        $this->lastRank = (int) $data['alliance_rank_last'];
        $this->founderId = (int) $data['alliance_founder_id'];
        $this->foundationTimestamp = (int) $data['alliance_foundation_date'];
        $this->architectId = (int) $data['alliance_architect_id'];
        $this->technicianId = (int) $data['alliance_technican_id'];
        $this->diplomatId = (int) $data['alliance_diplomat_id'];
        $this->visits = (int) $data['alliance_visits'];
        $this->visitsExternal = (int) $data['alliance_visits_ext'];
        $this->applicationTemplate = $data['alliance_application_template'];
        $this->resMetal = (int) $data['alliance_res_metal'];
        $this->resCrystal = (int) $data['alliance_res_crystal'];
        $this->resPlastic = (int) $data['alliance_res_plastic'];
        $this->resFuel = (int) $data['alliance_res_fuel'];
        $this->resFood = (int) $data['alliance_res_food'];
        $this->objectsForMembers = (int) $data['alliance_objects_for_members'];
    }

    public function toString(): string
    {
        return "[" . $this->tag . "] " . $this->name;
    }

    public function getImageUrl(): ?string
    {
        if ($this->image == '') {
            return null;
        }

        return ALLIANCE_IMG_DIR . "/" . $this->image;
    }

    public function getResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->resMetal;
        $resources->crystal = $this->resCrystal;
        $resources->plastic = $this->resPlastic;
        $resources->fuel = $this->resFuel;
        $resources->food = $this->resFood;

        return $resources;
    }
}
