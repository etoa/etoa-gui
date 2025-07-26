<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtoA\Alliance\AllianceImage;
use EtoA\Alliance\AllianceRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceRepository::class)]
#[ORM\Table(name: 'alliances')]
class Alliance
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_id")]
    protected int $id;

    #[ORM\Column(name: "alliance_tag")]
    protected string $tag;

    #[ORM\Column(name: "alliance_name")]
    protected string $name;

    #[ORM\Column(name: "alliance_text")]
    protected ?string $text;

    #[ORM\Column(name: "alliance_img")]
    protected ?string $image;

    #[ORM\Column(name: "alliance_img_check")]
    protected bool $imageCheck = false;

    #[ORM\Column(name: "alliance_url")]
    protected ?string $url;

    #[ORM\JoinColumn(name: 'alliance_mother', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    protected ?Alliance $mother = null;

    #[ORM\JoinColumn(name: 'alliance_mother_request', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    protected ?Alliance $motherRequest = null;

    #[ORM\Column(name: "alliance_accept_applications")]
    protected bool $acceptApplications = true;

    #[ORM\Column(name: "alliance_accept_bnd")]
    protected bool $acceptBnd = true;

    #[ORM\Column(name: "alliance_public_memberlist")]
    protected bool $publicMemberList = false;

    #[ORM\Column(name: "alliance_points")]
    protected int $points = 0;

    #[ORM\Column(name: "alliance_rank_current")]
    protected int $currentRank = 0;

    #[ORM\Column(name: "alliance_rank_last")]
    protected int $lastRank = 0;

    #[ORM\JoinColumn(name: 'alliance_founder_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    protected ?User $founder = null;

    #[ORM\Column(name: "alliance_foundation_date")]
    protected int $foundationTimestamp = 0;

    #[ORM\Column(name: "alliance_architect_id")]
    protected int $architectId = 0;

    #[ORM\Column(name: "alliance_technican_id")]
    protected int $technicianId = 0;

    #[ORM\Column(name: "alliance_diplomat_id")]
    protected int $diplomatId = 0;

    #[ORM\Column(name: "alliance_visits")]
    protected ?int $visits = 0;

    #[ORM\Column(name: "alliance_visits_ext")]
    protected ?int $visitsExternal = 0;

    #[ORM\Column(name: "alliance_application_template")]
    protected ?string $applicationTemplate;

    #[ORM\Column(name: "alliance_res_metal")]
    protected int $resMetal = 0;

    #[ORM\Column(name: "alliance_res_crystal")]
    protected int $resCrystal = 0;

    #[ORM\Column(name: "alliance_res_plastic")]
    protected int $resPlastic = 0;

    #[ORM\Column(name: "alliance_res_fuel")]
    protected int $resFuel = 0;

    #[ORM\Column(name: "alliance_res_food")]
    protected int $resFood = 0;

    #[ORM\Column(name: "alliance_objects_for_members")]
    protected int $objectsForMembers = 1;

    #[ORM\OneToOne(mappedBy: "id", targetEntity: AllianceStats::class,cascade: ['remove'])]
    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'alliance_id')]
    protected ?AllianceStats $allianceStats = null;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'user_id')]
    private Collection $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
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

        return AllianceImage::IMAGE_PATH . $this->image;
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

    public function isPublicMemberList(): ?bool
    {
        return $this->publicMemberList;
    }

    public function setPublicMemberList(bool $publicMemberList): static
    {
        $this->publicMemberList = $publicMemberList;

        return $this;
    }

    public function getFounder(): ?User
    {
        return $this->founder;
    }

    public function setFounder(?User $founder): static
    {
        $this->founder = $founder;

        return $this;
    }

    public function getMother(): ?self
    {
        return $this->mother;
    }

    public function setMother(?self $mother): static
    {
        $this->mother = $mother;

        return $this;
    }

    public function getMotherRequest(): ?self
    {
        return $this->motherRequest;
    }

    public function setMotherRequest(?self $motherRequest): static
    {
        $this->motherRequest = $motherRequest;

        return $this;
    }

    public function getAllianceStats(): ?AllianceStats
    {
        return $this->allianceStats;
    }

    public function setAllianceStats(?AllianceStats $allianceStats): static
    {
        $this->allianceStats = $allianceStats;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setAlliance($this);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getAlliance() === $this) {
                $member->setAlliance(null);
            }
        }

        return $this;
    }
}
