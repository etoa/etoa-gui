<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceRepository::class)]
#[ORM\Table(name: 'alliances')]
class Alliance
{
    public const PROFILE_PICTURE_PATH = '/cache/allianceprofiles/';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_id")]
    private int $id;

    #[ORM\Column(name: "alliance_tag")]
    private string $tag;

    #[ORM\Column(name: "alliance_name")]
    private string $name;

    #[ORM\Column(name: "alliance_text")]
    private ?string $text;

    #[ORM\Column(name: "alliance_img")]
    private ?string $image;

    #[ORM\Column(name: "alliance_img_check")]
    private bool $imageCheck;

    #[ORM\Column(name: "alliance_url")]
    private ?string $url;

    #[ORM\Column(name: "alliance_mother")]
    private int $motherId;

    #[ORM\Column(name: "alliance_mother_request")]
    private int $motherRequest;

    #[ORM\Column(name: "alliance_accept_applications")]
    private bool $acceptApplications;

    #[ORM\Column(name: "alliance_accept_bnd")]
    private bool $acceptBnd;

    #[ORM\Column(name: "alliance_public_memberlist")]
    private bool $privateMemberList;

    #[ORM\Column(name: "alliance_points")]
    private int $points;

    #[ORM\Column(name: "alliance_rank_current")]
    private int $currentRank;

    #[ORM\Column(name: "alliance_rank_last")]
    private int $lastRank;

    #[ORM\Column(name: "alliance_founder_id")]
    private int $founderId;

    #[ORM\Column(name: "alliance_foundation_date")]
    private int $foundationTimestamp;

    #[ORM\Column(name: "alliance_architect_id")]
    private int $architectId;

    #[ORM\Column(name: "alliance_technician_id")]
    private int $technicianId;

    #[ORM\Column(name: "alliance_diplomat_id")]
    private int $diplomatId;

    #[ORM\Column(name: "alliance_visists")]
    private int $visits;

    #[ORM\Column(name: "alliance_visits_ext")]
    private int $visitsExternal;

    #[ORM\Column(name: "alliance_application_template")]
    private ?string $applicationTemplate;

    #[ORM\Column(name: "alliance_res_metal")]
    private int $resMetal;

    #[ORM\Column(name: "alliance_res_crystal")]
    private int $resCrystal;

    #[ORM\Column(name: "alliance_res_plastic")]
    private int $resPlastic;

    #[ORM\Column(name: "alliance_res_fuel")]
    private int $resFuel;

    #[ORM\Column(name: "alliance_res_food")]
    private int $resFood;

    #[ORM\Column(name: "alliance_objects_for_members")]
    private int $objectsForMembers;

    public function toString(): string
    {
        return "[" . $this->tag . "] " . $this->name;
    }

    public function getImageUrl(): ?string
    {
        if ($this->image == '') {
            return null;
        }

        return self::PROFILE_PICTURE_PATH . $this->image;
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isImageCheck(): ?bool
    {
        return $this->imageCheck;
    }

    public function setImageCheck(bool $imageCheck): static
    {
        $this->imageCheck = $imageCheck;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getMotherId(): ?int
    {
        return $this->motherId;
    }

    public function setMotherId(int $motherId): static
    {
        $this->motherId = $motherId;

        return $this;
    }

    public function getMotherRequest(): ?int
    {
        return $this->motherRequest;
    }

    public function setMotherRequest(int $motherRequest): static
    {
        $this->motherRequest = $motherRequest;

        return $this;
    }

    public function isAcceptApplications(): ?bool
    {
        return $this->acceptApplications;
    }

    public function setAcceptApplications(bool $acceptApplications): static
    {
        $this->acceptApplications = $acceptApplications;

        return $this;
    }

    public function isAcceptBnd(): ?bool
    {
        return $this->acceptBnd;
    }

    public function setAcceptBnd(bool $acceptBnd): static
    {
        $this->acceptBnd = $acceptBnd;

        return $this;
    }

    public function isPrivateMemberList(): ?bool
    {
        return $this->privateMemberList;
    }

    public function setPrivateMemberList(bool $privateMemberList): static
    {
        $this->privateMemberList = $privateMemberList;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getCurrentRank(): ?int
    {
        return $this->currentRank;
    }

    public function setCurrentRank(int $currentRank): static
    {
        $this->currentRank = $currentRank;

        return $this;
    }

    public function getLastRank(): ?int
    {
        return $this->lastRank;
    }

    public function setLastRank(int $lastRank): static
    {
        $this->lastRank = $lastRank;

        return $this;
    }

    public function getFounderId(): ?int
    {
        return $this->founderId;
    }

    public function setFounderId(int $founderId): static
    {
        $this->founderId = $founderId;

        return $this;
    }

    public function getFoundationTimestamp(): ?int
    {
        return $this->foundationTimestamp;
    }

    public function setFoundationTimestamp(int $foundationTimestamp): static
    {
        $this->foundationTimestamp = $foundationTimestamp;

        return $this;
    }

    public function getArchitectId(): ?int
    {
        return $this->architectId;
    }

    public function setArchitectId(int $architectId): static
    {
        $this->architectId = $architectId;

        return $this;
    }

    public function getTechnicianId(): ?int
    {
        return $this->technicianId;
    }

    public function setTechnicianId(int $technicianId): static
    {
        $this->technicianId = $technicianId;

        return $this;
    }

    public function getDiplomatId(): ?int
    {
        return $this->diplomatId;
    }

    public function setDiplomatId(int $diplomatId): static
    {
        $this->diplomatId = $diplomatId;

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): static
    {
        $this->visits = $visits;

        return $this;
    }

    public function getVisitsExternal(): ?int
    {
        return $this->visitsExternal;
    }

    public function setVisitsExternal(int $visitsExternal): static
    {
        $this->visitsExternal = $visitsExternal;

        return $this;
    }

    public function getApplicationTemplate(): ?string
    {
        return $this->applicationTemplate;
    }

    public function setApplicationTemplate(string $applicationTemplate): static
    {
        $this->applicationTemplate = $applicationTemplate;

        return $this;
    }

    public function getResMetal(): ?int
    {
        return $this->resMetal;
    }

    public function setResMetal(int $resMetal): static
    {
        $this->resMetal = $resMetal;

        return $this;
    }

    public function getResCrystal(): ?int
    {
        return $this->resCrystal;
    }

    public function setResCrystal(int $resCrystal): static
    {
        $this->resCrystal = $resCrystal;

        return $this;
    }

    public function getResPlastic(): ?int
    {
        return $this->resPlastic;
    }

    public function setResPlastic(int $resPlastic): static
    {
        $this->resPlastic = $resPlastic;

        return $this;
    }

    public function getResFuel(): ?int
    {
        return $this->resFuel;
    }

    public function setResFuel(int $resFuel): static
    {
        $this->resFuel = $resFuel;

        return $this;
    }

    public function getResFood(): ?int
    {
        return $this->resFood;
    }

    public function setResFood(int $resFood): static
    {
        $this->resFood = $resFood;

        return $this;
    }

    public function getObjectsForMembers(): ?int
    {
        return $this->objectsForMembers;
    }

    public function setObjectsForMembers(int $objectsForMembers): static
    {
        $this->objectsForMembers = $objectsForMembers;

        return $this;
    }
}
