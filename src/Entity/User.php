<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EtoA\User\UserInterface;
use EtoA\User\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const NAME_PATTERN = '/^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$/';
    public const NICK_PATTERN = '/^.[^\'\"\?\<\>\$\!\=\;\&]*$/';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "user_id", type: "integer")]
    protected int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\OneToOne(inversedBy: 'user', targetEntity: UserProperties::class, cascade: ['persist', 'remove'])]
    protected UserProperties $userProperties;

    #[ORM\Column(name: "user_name", type: 'string', length: 180)]
    protected string $name;

    #[ORM\Column(name: "user_nick", type: "string", length: 180, unique: true)]
    protected string $nick;

    #[ORM\Column(name: "user_password", type: "string")]
    protected ?string $password = null;

    #[ORM\Column(name: "user_password_temp", type: "string")]
    protected ?string $passwordTemp;

    #[ORM\Column(name: "user_last_login", type: "integer")]
    protected int $lastLogin = 0;

    #[ORM\Column(name: "user_last_online", type: "integer")]
    protected int $lastOnline = 0;

    #[ORM\Column(name: "user_logintime", type: "integer")]
    protected int $loginTime = 0;

    #[ORM\Column(name: "user_acttime", type: "integer")]
    protected int $actionTime = 0;

    #[ORM\Column(name: "user_logouttime", type: "integer")]
    protected int $logoutTime = 0;

    #[ORM\Column(name: "user_session_key", type: "string")]
    protected ?string $sessionKey;

    #[ORM\Column(name: "user_email", type: "string")]
    protected string $email;

    #[ORM\Column(name: "user_email_fix", type: "string")]
    protected string $emailFix;

    #[ORM\Column(name: "user_ip", type: "string")]
    protected ?string $ip;

    #[ORM\Column(name: "user_hostname", type: "string")]
    protected ?string $hostname;

    #[ORM\Column(name: "user_blocked_from", type: "integer")]
    protected int $blockedFrom = 0;

    #[ORM\Column(name: "user_blocked_to", type: "integer")]
    protected int $blockedTo = 0;

    #[ORM\Column(name: "user_ban_reason", type: "string")]
    protected ?string $banReason;

    #[ORM\Column(name: "user_attack_bans", type: "integer")]
    protected int $attackBans = 0;

    #[ORM\JoinColumn(name: 'user_ban_admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    protected ?AdminUser $banAdmin = null;

    #[ORM\Column(name: "user_hmode_from", type: "integer")]
    protected int $hmodFrom = 0;

    #[ORM\Column(name: "user_hmode_to", type: "integer")]
    protected int $hmodTo = 0;

    #[ORM\JoinColumn(name: 'user_race_id', referencedColumnName: 'race_id')]
    #[ORM\ManyToOne(targetEntity: Race::class)]
    protected ?Race $race = null;

    #[ORM\JoinColumn(name: 'user_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    protected ?Alliance $alliance = null;

    #[ORM\Column(name: "user_alliance_shippoints", type: "integer")]
    protected int $allianceShipPoints = 0;

    #[ORM\Column(name: "user_alliance_shippoints_used", type: "integer")]
    protected int $allianceShipPointsUsed = 0;

    #[ORM\Column(name: "user_alliance_leave", type: "integer")]
    protected int $allianceLeave = 0;

    #[ORM\Column(name: "user_sitting_days", type: "integer")]
    protected int $sittingDays = 20;

    #[ORM\Column(name: "user_multi_delets", type: "integer")]
    protected int $multiDelets = 0;

    #[ORM\Column(name: "user_setup", type: "boolean")]
    protected bool $setup = false;


    #[ORM\Column(name: "user_points", type: "integer")]
    protected int $points = 0;

    #[ORM\Column(name: "user_rank", type: "integer")]
    protected int $rank = 0;

    #[ORM\Column(name: "user_rank_highest", type: "integer")]
    protected int $rankHighest = 0;

    #[ORM\JoinColumn(name: 'user_alliance_rank_id', referencedColumnName: 'rank_id')]
    #[ORM\ManyToOne(targetEntity: AllianceRank::class)]
    protected ?AllianceRank $allianceRank = null;

    #[ORM\Column(name: "user_registered", type: "integer")]
    protected int $registered = 1097597003;

    #[ORM\Column(name: "user_profile_text", type: "string")]
    protected ?string $profileText;

    #[ORM\Column(name: "user_ghost", type: "boolean")]
    protected bool $ghost = false;

    #[ORM\Column(type: "integer")]
    protected int $admin = 0;

    #[ORM\Column(name: "user_chatadmin", type: "integer")]
    protected int $chatAdmin = 0;

    #[ORM\Column(name: "user_visits", type: "integer")]
    protected int $visits = 0;

    #[ORM\Column(name: "user_avatar", type: "string")]
    protected ?string $avatar;

    #[ORM\Column(name: "user_signature", type: "string")]
    protected ?string $signature;

    #[ORM\Column(name: "user_client", type: "string")]
    protected ?string $client;

    #[ORM\Column(name: "user_res_from_raid", type: "integer")]
    protected int $resFromRaid = 0;

    #[ORM\Column(name: "user_res_from_tf", type: "integer")]
    protected int $resFromTf = 0;

    #[ORM\Column(name: "user_res_from_asteroid", type: "integer")]
    protected int $resFromAsteroid = 0;

    #[ORM\Column(name: "user_res_from_nebula", type: "integer")]
    protected int $resFromNebula = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    protected bool $userMainPlanetChanged = false;

    #[ORM\Column(name: "user_profile_board_url", type: "string")]
    protected ?string $profileBoardUrl;

    #[ORM\Column(name: "user_profile_img", type: "string")]
    protected ?string $profileImage;

    #[ORM\Column(name: "user_profile_img_check", type: "boolean")]
    protected bool $profileImageCheck = false;

    #[ORM\ManyToOne(targetEntity: Specialist::class)]
    #[ORM\JoinColumn(name: 'user_specialist_id', referencedColumnName: 'specialist_id')]
    protected ?Specialist $specialist = null;

    #[ORM\Column(name: "user_specialist_time", type: "integer")]
    protected int $specialistTime = 0;

    #[ORM\Column(name: "user_deleted", type: "integer")]
    protected int $deleted = 0;

    #[ORM\Column(name: "user_observe", type: "string")]
    protected ?string $observe;

    #[ORM\Column(name: "lastinvasion", type: "integer")]
    protected int $lastInvasion = 0;

    #[ORM\Column(name: "spyattack_counter", type: "integer")]
    protected int $spyAttackCounter = 0;

    #[ORM\Column(name: "discoverymask", type: "string")]
    protected ?string $discoveryMask;

    #[ORM\Column(name: "discoverymask_last_updated", type: "integer")]
    protected int $discoveryMaskLastUpdated = 0;

    #[ORM\Column(type: "float")]
    protected float $boostBonusProduction = 0;

    #[ORM\Column(type: "float")]
    protected float $boostBonusBuilding = 0;

    #[ORM\Column(type: "string")]
    protected ?string $dualEmail;

    #[ORM\Column(type: "string")]
    protected ?string $dualName;

    #[ORM\Column(type: "string")]
    protected ?string $verificationKey;

    #[ORM\Column(type: Types::BOOLEAN)]
    protected bool $npc = false;

    #[ORM\Column(type: "boolean")]
    protected bool $userChangedMainPlanet = false;

    #[ORM\OneToOne(mappedBy: "id", targetEntity: UserRating::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    protected ?UserRating $userRating = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Planet::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'planet_user_id')]
    private Collection $planets;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TechnologyListItem::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'techlist_user_id')]
    private Collection $techlist;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLog::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    private Collection $logs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserComment::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'comment_user_id')]
    private Collection $comments;

    public function __construct()
    {
        $this->planets = new ArrayCollection();
        $this->techlist = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function __toString() {
        return $this->nick;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNick(): string
    {
        return $this->nick;
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

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordTemp(): ?string
    {
        return $this->passwordTemp;
    }

    public function setPasswordTemp(string $passwordTemp): static
    {
        $this->passwordTemp = $passwordTemp;

        return $this;
    }

    public function getLastLogin(): ?int
    {
        return $this->lastLogin;
    }

    public function setLastLogin(int $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getLastOnline(): ?int
    {
        return $this->lastOnline;
    }

    public function setLastOnline(int $lastOnline): static
    {
        $this->lastOnline = $lastOnline;

        return $this;
    }

    public function getLoginTime(): ?int
    {
        return $this->loginTime;
    }

    public function setLoginTime(int $loginTime): static
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    public function getActionTime(): ?int
    {
        return $this->actionTime;
    }

    public function setActionTime(int $actionTime): static
    {
        $this->actionTime = $actionTime;

        return $this;
    }

    public function getLogoutTime(): ?int
    {
        return $this->logoutTime;
    }

    public function setLogoutTime(int $logoutTime): static
    {
        $this->logoutTime = $logoutTime;

        return $this;
    }

    public function getSessionKey(): ?string
    {
        return $this->sessionKey;
    }

    public function setSessionKey(string $sessionKey): static
    {
        $this->sessionKey = $sessionKey;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailFix(): ?string
    {
        return $this->emailFix;
    }

    public function setEmailFix(string $emailFix): static
    {
        $this->emailFix = $emailFix;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): static
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getBlockedFrom(): ?int
    {
        return $this->blockedFrom;
    }

    public function setBlockedFrom(int $blockedFrom): static
    {
        $this->blockedFrom = $blockedFrom;

        return $this;
    }

    public function getBlockedTo(): ?int
    {
        return $this->blockedTo;
    }

    public function setBlockedTo(int $blockedTo): static
    {
        $this->blockedTo = $blockedTo;

        return $this;
    }

    public function getBanReason(): ?string
    {
        return $this->banReason;
    }

    public function setBanReason(string $banReason): static
    {
        $this->banReason = $banReason;

        return $this;
    }

    public function getAttackBans(): ?int
    {
        return $this->attackBans;
    }

    public function setAttackBans(int $attackBans): static
    {
        $this->attackBans = $attackBans;

        return $this;
    }

    public function getHmodFrom(): ?int
    {
        return $this->hmodFrom;
    }

    public function setHmodFrom(int $hmodFrom): static
    {
        $this->hmodFrom = $hmodFrom;

        return $this;
    }

    public function getHmodTo(): ?int
    {
        return $this->hmodTo;
    }

    public function setHmodTo(int $hmodTo): static
    {
        $this->hmodTo = $hmodTo;

        return $this;
    }

    public function getAllianceShipPoints(): ?int
    {
        return $this->allianceShipPoints;
    }

    public function setAllianceShipPoints(int $allianceShipPoints): static
    {
        $this->allianceShipPoints = $allianceShipPoints;

        return $this;
    }

    public function getAllianceShipPointsUsed(): ?int
    {
        return $this->allianceShipPointsUsed;
    }

    public function setAllianceShipPointsUsed(int $allianceShipPointsUsed): static
    {
        $this->allianceShipPointsUsed = $allianceShipPointsUsed;

        return $this;
    }

    public function getAllianceLeave(): ?int
    {
        return $this->allianceLeave;
    }

    public function setAllianceLeave(int $allianceLeave): static
    {
        $this->allianceLeave = $allianceLeave;

        return $this;
    }

    public function getSittingDays(): ?int
    {
        return $this->sittingDays;
    }

    public function setSittingDays(int $sittingDays): static
    {
        $this->sittingDays = $sittingDays;

        return $this;
    }

    public function getMultiDelets(): ?int
    {
        return $this->multiDelets;
    }

    public function setMultiDelets(int $multiDelets): static
    {
        $this->multiDelets = $multiDelets;

        return $this;
    }

    public function isSetup(): ?bool
    {
        return $this->setup;
    }

    public function setSetup(bool $setup): static
    {
        $this->setup = $setup;

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

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getRankHighest(): ?int
    {
        return $this->rankHighest;
    }

    public function setRankHighest(int $rankHighest): static
    {
        $this->rankHighest = $rankHighest;

        return $this;
    }

    public function getRegistered(): ?int
    {
        return $this->registered;
    }

    public function setRegistered(int $registered): static
    {
        $this->registered = $registered;

        return $this;
    }

    public function getProfileText(): ?string
    {
        return $this->profileText;
    }

    public function setProfileText(string $profileText): static
    {
        $this->profileText = $profileText;

        return $this;
    }

    public function isGhost(): ?bool
    {
        return $this->ghost;
    }

    public function setGhost(bool $ghost): static
    {
        $this->ghost = $ghost;

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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): static
    {
        $this->signature = $signature;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getResFromRaid(): ?int
    {
        return $this->resFromRaid;
    }

    public function setResFromRaid(int $resFromRaid): static
    {
        $this->resFromRaid = $resFromRaid;

        return $this;
    }

    public function getResFromTf(): ?int
    {
        return $this->resFromTf;
    }

    public function setResFromTf(int $resFromTf): static
    {
        $this->resFromTf = $resFromTf;

        return $this;
    }

    public function getResFromAsteroid(): ?int
    {
        return $this->resFromAsteroid;
    }

    public function setResFromAsteroid(int $resFromAsteroid): static
    {
        $this->resFromAsteroid = $resFromAsteroid;

        return $this;
    }

    public function getResFromNebula(): ?int
    {
        return $this->resFromNebula;
    }

    public function setResFromNebula(int $resFromNebula): static
    {
        $this->resFromNebula = $resFromNebula;

        return $this;
    }

    public function getProfileBoardUrl(): ?string
    {
        return $this->profileBoardUrl;
    }

    public function setProfileBoardUrl(string $profileBoardUrl): static
    {
        $this->profileBoardUrl = $profileBoardUrl;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function isProfileImageCheck(): ?bool
    {
        return $this->profileImageCheck;
    }

    public function setProfileImageCheck(bool $profileImageCheck): static
    {
        $this->profileImageCheck = $profileImageCheck;

        return $this;
    }

    public function getSpecialistTime(): ?int
    {
        return $this->specialistTime;
    }

    public function setSpecialistTime(int $specialistTime): static
    {
        $this->specialistTime = $specialistTime;

        return $this;
    }

    public function getDeleted(): ?int
    {
        return $this->deleted;
    }

    public function setDeleted(int $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getObserve(): ?string
    {
        return $this->observe;
    }

    public function setObserve(string $observe): static
    {
        $this->observe = $observe;

        return $this;
    }

    public function getLastInvasion(): ?int
    {
        return $this->lastInvasion;
    }

    public function setLastInvasion(int $lastInvasion): static
    {
        $this->lastInvasion = $lastInvasion;

        return $this;
    }

    public function getSpyAttackCounter(): ?int
    {
        return $this->spyAttackCounter;
    }

    public function setSpyAttackCounter(int $spyAttackCounter): static
    {
        $this->spyAttackCounter = $spyAttackCounter;

        return $this;
    }

    public function getDiscoveryMask(): ?string
    {
        return $this->discoveryMask;
    }

    public function setDiscoveryMask(string $discoveryMask): static
    {
        $this->discoveryMask = $discoveryMask;

        return $this;
    }

    public function getDiscoveryMaskLastUpdated(): ?int
    {
        return $this->discoveryMaskLastUpdated;
    }

    public function setDiscoveryMaskLastUpdated(int $discoveryMaskLastUpdated): static
    {
        $this->discoveryMaskLastUpdated = $discoveryMaskLastUpdated;

        return $this;
    }

    public function getBoostBonusProduction(): ?float
    {
        return $this->boostBonusProduction;
    }

    public function setBoostBonusProduction(float $boostBonusProduction): static
    {
        $this->boostBonusProduction = $boostBonusProduction;

        return $this;
    }

    public function getBoostBonusBuilding(): ?float
    {
        return $this->boostBonusBuilding;
    }

    public function setBoostBonusBuilding(float $boostBonusBuilding): static
    {
        $this->boostBonusBuilding = $boostBonusBuilding;

        return $this;
    }

    public function getDualEmail(): ?string
    {
        return $this->dualEmail;
    }

    public function setDualEmail(string $dualEmail): static
    {
        $this->dualEmail = $dualEmail;

        return $this;
    }

    public function getDualName(): ?string
    {
        return $this->dualName;
    }

    public function setDualName(string $dualName): static
    {
        $this->dualName = $dualName;

        return $this;
    }

    public function getVerificationKey(): ?string
    {
        return $this->verificationKey;
    }

    public function setVerificationKey(string $verificationKey): static
    {
        $this->verificationKey = $verificationKey;

        return $this;
    }

    public function isUserChangedMainPlanet(): ?bool
    {
        return $this->userChangedMainPlanet;
    }

    public function setUserChangedMainPlanet(bool $userChangedMainPlanet): static
    {
        $this->userChangedMainPlanet = $userChangedMainPlanet;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getSpecialist(): ?Specialist
    {
        return $this->specialist;
    }

    public function setSpecialist(?Specialist $specialist): static
    {
        $this->specialist = $specialist;

        return $this;
    }

    public function getUserProperties(): ?UserProperties
    {
        return $this->userProperties;
    }

    public function setUserProperties(?UserProperties $userProperties): static
    {
        $userProperties->setUser($this);
        $this->userProperties = $userProperties;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }

    public function getAllianceRank(): ?AllianceRank
    {
        return $this->allianceRank;
    }

    public function setAllianceRank(?AllianceRank $allianceRank): static
    {
        $this->allianceRank = $allianceRank;

        return $this;
    }

    public function getUserRating(): ?UserRating
    {
        return $this->userRating;
    }

    public function setUserRating(?UserRating $userRating): static
    {
        $userRating->setUser($this);

        $this->userRating = $userRating;

        return $this;
    }

    /**
     * @return Collection<int, Planet>
     */
    public function getPlanets(): Collection
    {
        return $this->planets;
    }

    public function addPlanet(Planet $planet): static
    {
        if (!$this->planets->contains($planet)) {
            $this->planets->add($planet);
            $planet->setUser($this);
        }

        return $this;
    }

    public function removePlanet(Planet $planet): static
    {
        if ($this->planets->removeElement($planet)) {
            // set the owning side to null (unless already changed)
            if ($planet->getUser() === $this) {
                $planet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TechnologyListItem>
     */
    public function getTechlist(): Collection
    {
        return $this->techlist;
    }

    public function addTechlist(TechnologyListItem $techlist): static
    {
        if (!$this->techlist->contains($techlist)) {
            $this->techlist->add($techlist);
            $techlist->setUser($this);
        }

        return $this;
    }

    public function removeTechlist(TechnologyListItem $techlist): static
    {
        if ($this->techlist->removeElement($techlist)) {
            // set the owning side to null (unless already changed)
            if ($techlist->getUser() === $this) {
                $techlist->setUser(null);
            }
        }

        return $this;
    }

    public function getBanAdmin(): ?AdminUser
    {
        return $this->banAdmin;
    }

    public function setBanAdmin(?AdminUser $banAdmin): static
    {
        $this->banAdmin = $banAdmin;

        return $this;
    }

    /**
     * @return Collection<int, UserLog>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(UserLog $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(UserLog $log): static
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }

    public function isUserMainPlanetChanged(): ?bool
    {
        return $this->userMainPlanetChanged;
    }

    public function setUserMainPlanetChanged(bool $userMainPlanetChanged): static
    {
        $this->userMainPlanetChanged = $userMainPlanetChanged;

        return $this;
    }

    public function isNpc(): ?bool
    {
        return $this->npc;
    }

    public function setNpc(bool $npc): static
    {
        $this->npc = $npc;

        return $this;
    }

    /**
     * @return Collection<int, UserComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(UserComment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(UserComment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getAdmin(): ?int
    {
        return $this->admin;
    }

    public function setAdmin(int $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    public function getChatAdmin(): ?int
    {
        return $this->chatAdmin;
    }

    public function setChatAdmin(int $chatAdmin): static
    {
        $this->chatAdmin = $chatAdmin;

        return $this;
    }
}
