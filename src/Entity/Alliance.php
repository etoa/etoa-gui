<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtoA\Alliance\AllianceImage;
use EtoA\Alliance\AllianceRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: AllianceRepository::class)]
#[ORM\Table(name: 'alliances')]
class Alliance
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_id")]
    private int $id;

    #[ORM\Column(name: "alliance_tag")]
    private string $tag;

    #[ORM\Column(name: "alliance_name")]
    private string $name;

    #[ORM\Column(name: "alliance_text")]
    private ?string $text = '';

    #[ORM\Column(name: "alliance_img")]
    private ?string $image = null;

    #[ORM\Column(name: "alliance_img_check")]
    private bool $imageCheck = false;

    #[ORM\Column(name: "alliance_url")]
    private ?string $url;

    #[ORM\JoinColumn(name: 'alliance_mother', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    #[Ignore]
    private ?Alliance $mother = null;

    #[ORM\JoinColumn(name: 'alliance_mother_request', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    #[Ignore]
    private ?Alliance $motherRequest = null;

    #[ORM\Column(name: "alliance_accept_applications")]
    private bool $acceptApplications = true;

    #[ORM\Column(name: "alliance_accept_bnd")]
    private bool $acceptBnd = true;

    #[ORM\Column(name: "alliance_public_memberlist")]
    private bool $publicMemberList = false;

    #[ORM\Column(name: "alliance_points")]
    private int $points = 0;

    #[ORM\Column(name: "alliance_rank_current")]
    private int $currentRank = 0;

    #[ORM\Column(name: "alliance_rank_last")]
    private int $lastRank = 0;

    #[ORM\JoinColumn(name: 'alliance_founder_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $founder = null;

    #[ORM\Column(name: "alliance_foundation_date")]
    private int $foundationTimestamp = 0;

    #[ORM\Column(name: "alliance_architect_id")]
    private int $architectId = 0;

    #[ORM\Column(name: "alliance_technican_id")]
    private int $technicianId = 0;

    #[ORM\Column(name: "alliance_diplomat_id")]
    private int $diplomatId = 0;

    #[ORM\Column(name: "alliance_visits")]
    private ?int $visits = 0;

    #[ORM\Column(name: "alliance_visits_ext")]
    private ?int $visitsExternal = 0;

    #[ORM\Column(name: "alliance_application_template")]
    private ?string $applicationTemplate;

    #[ORM\Column(name: "alliance_res_metal")]
    private int $resMetal = 0;

    #[ORM\Column(name: "alliance_res_crystal")]
    private int $resCrystal = 0;

    #[ORM\Column(name: "alliance_res_plastic")]
    private int $resPlastic = 0;

    #[ORM\Column(name: "alliance_res_fuel")]
    private int $resFuel = 0;

    #[ORM\Column(name: "alliance_res_food")]
    private int $resFood = 0;

    #[ORM\Column(name: "alliance_objects_for_members")]
    private int $objectsForMembers = 1;

    #[ORM\OneToOne(mappedBy: "alliance", targetEntity: AllianceStats::class, cascade: ['persist'], orphanRemoval: true)]
    #[Ignore]
    private ?AllianceStats $allianceStats = null;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: User::class)]
    #[Ignore]
    private Collection $members;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceBoardCategory::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $boardCategories;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceApplication::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'alliance1', targetEntity: AllianceDiplomacy::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $diplomacy1;

    #[ORM\OneToMany(mappedBy: 'alliance2', targetEntity: AllianceDiplomacy::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $diplomacy2;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceBuildListItem::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $buildlist;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceHistory::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $history;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AlliancePoints::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $alliancePoints;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceNews::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $news;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AlliancePoll::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $polls;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceRank::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $ranks;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceSpend::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $spends;

    #[ORM\OneToMany(mappedBy: 'alliance', targetEntity: AllianceTechnologyListItem::class, cascade: ['persist', 'remove'])]
    #[Ignore]
    private Collection $techlist;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->boardCategories = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->diplomacy1 = new ArrayCollection();
        $this->diplomacy2 = new ArrayCollection();
        $this->buildlist = new ArrayCollection();
        $this->history = new ArrayCollection();
        $this->alliancePoints = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->polls = new ArrayCollection();
        $this->ranks = new ArrayCollection();
        $this->spends = new ArrayCollection();
        $this->techlist = new ArrayCollection();

    }

    public function toString(): string
    {
        return "[" . $this->tag . "] " . $this->name;
    }

    #[Ignore]
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

    public function setImage(?string $image): static
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
        $allianceStats->setAlliance($this);
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

    /**
     * @return Collection<int, AllianceBoardCategory>
     */
    public function getBoardCategories(): Collection
    {
        return $this->boardCategories;
    }

    public function addBoardCategory(AllianceBoardCategory $boardCategory): static
    {
        if (!$this->boardCategories->contains($boardCategory)) {
            $this->boardCategories->add($boardCategory);
            $boardCategory->setAlliance($this);
        }

        return $this;
    }

    public function removeBoardCategory(AllianceBoardCategory $boardCategory): static
    {
        if ($this->boardCategories->removeElement($boardCategory)) {
            // set the owning side to null (unless already changed)
            if ($boardCategory->getAlliance() === $this) {
                $boardCategory->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceApplication>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(AllianceApplication $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setAlliance($this);
        }

        return $this;
    }

    public function removeApplication(AllianceApplication $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getAlliance() === $this) {
                $application->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceDiplomacy>
     */
    public function getDiplomacy1(): Collection
    {
        return $this->diplomacy1;
    }

    public function addDiplomacy1(AllianceDiplomacy $diplomacy1): static
    {
        if (!$this->diplomacy1->contains($diplomacy1)) {
            $this->diplomacy1->add($diplomacy1);
            $diplomacy1->setAlliance1($this);
        }

        return $this;
    }

    public function removeDiplomacy1(AllianceDiplomacy $diplomacy1): static
    {
        if ($this->diplomacy1->removeElement($diplomacy1)) {
            // set the owning side to null (unless already changed)
            if ($diplomacy1->getAlliance1() === $this) {
                $diplomacy1->setAlliance1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceDiplomacy>
     */
    public function getDiplomacy2(): Collection
    {
        return $this->diplomacy2;
    }

    public function addDiplomacy2(AllianceDiplomacy $diplomacy2): static
    {
        if (!$this->diplomacy2->contains($diplomacy2)) {
            $this->diplomacy2->add($diplomacy2);
            $diplomacy2->setAlliance2($this);
        }

        return $this;
    }

    public function removeDiplomacy2(AllianceDiplomacy $diplomacy2): static
    {
        if ($this->diplomacy2->removeElement($diplomacy2)) {
            // set the owning side to null (unless already changed)
            if ($diplomacy2->getAlliance2() === $this) {
                $diplomacy2->setAlliance2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceBuildListItem>
     */
    public function getBuildlist(): Collection
    {
        return $this->buildlist;
    }

    public function addBuildlist(AllianceBuildListItem $buildlist): static
    {
        if (!$this->buildlist->contains($buildlist)) {
            $this->buildlist->add($buildlist);
            $buildlist->setAlliance($this);
        }

        return $this;
    }

    public function removeBuildlist(AllianceBuildListItem $buildlist): static
    {
        if ($this->buildlist->removeElement($buildlist)) {
            // set the owning side to null (unless already changed)
            if ($buildlist->getAlliance() === $this) {
                $buildlist->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceHistory>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(AllianceHistory $history): static
    {
        if (!$this->history->contains($history)) {
            $this->history->add($history);
            $history->setAlliance($this);
        }

        return $this;
    }

    public function removeHistory(AllianceHistory $history): static
    {
        if ($this->history->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getAlliance() === $this) {
                $history->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AlliancePoints>
     */
    public function getAlliancePoints(): Collection
    {
        return $this->alliancePoints;
    }

    public function addAlliancePoint(AlliancePoints $alliancePoint): static
    {
        if (!$this->alliancePoints->contains($alliancePoint)) {
            $this->alliancePoints->add($alliancePoint);
            $alliancePoint->setAlliance($this);
        }

        return $this;
    }

    public function removeAlliancePoint(AlliancePoints $alliancePoint): static
    {
        if ($this->alliancePoints->removeElement($alliancePoint)) {
            // set the owning side to null (unless already changed)
            if ($alliancePoint->getAlliance() === $this) {
                $alliancePoint->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceNews>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(AllianceNews $news): static
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setAlliance($this);
        }

        return $this;
    }

    public function removeNews(AllianceNews $news): static
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getAlliance() === $this) {
                $news->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AlliancePoll>
     */
    public function getPolls(): Collection
    {
        return $this->polls;
    }

    public function addPoll(AlliancePoll $poll): static
    {
        if (!$this->polls->contains($poll)) {
            $this->polls->add($poll);
            $poll->setAlliance($this);
        }

        return $this;
    }

    public function removePoll(AlliancePoll $poll): static
    {
        if ($this->polls->removeElement($poll)) {
            // set the owning side to null (unless already changed)
            if ($poll->getAlliance() === $this) {
                $poll->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceRank>
     */
    public function getRanks(): Collection
    {
        return $this->ranks;
    }

    public function addRank(AllianceRank $rank): static
    {
        if (!$this->ranks->contains($rank)) {
            $this->ranks->add($rank);
            $rank->setAlliance($this);
        }

        return $this;
    }

    public function removeRank(AllianceRank $rank): static
    {
        if ($this->ranks->removeElement($rank)) {
            // set the owning side to null (unless already changed)
            if ($rank->getAlliance() === $this) {
                $rank->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceSpend>
     */
    public function getSpends(): Collection
    {
        return $this->spends;
    }

    public function addSpend(AllianceSpend $spend): static
    {
        if (!$this->spends->contains($spend)) {
            $this->spends->add($spend);
            $spend->setAlliance($this);
        }

        return $this;
    }

    public function removeSpend(AllianceSpend $spend): static
    {
        if ($this->spends->removeElement($spend)) {
            // set the owning side to null (unless already changed)
            if ($spend->getAlliance() === $this) {
                $spend->setAlliance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllianceTechnologyListItem>
     */
    public function getTechlist(): Collection
    {
        return $this->techlist;
    }

    public function addTechlist(AllianceTechnologyListItem $techlist): static
    {
        if (!$this->techlist->contains($techlist)) {
            $this->techlist->add($techlist);
            $techlist->setAlliance($this);
        }

        return $this;
    }

    public function removeTechlist(AllianceTechnologyListItem $techlist): static
    {
        if ($this->techlist->removeElement($techlist)) {
            // set the owning side to null (unless already changed)
            if ($techlist->getAlliance() === $this) {
                $techlist->setAlliance(null);
            }
        }

        return $this;
    }
}
