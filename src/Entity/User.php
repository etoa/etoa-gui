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
    private int $id;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserProperties::class, cascade: ['persist', 'remove'])]
    private ?UserProperties $userProperties;

    #[ORM\Column(name: "user_name", type: 'string', length: 180)]
    private string $name;

    #[ORM\Column(name: "user_nick", type: "string", length: 180, unique: true)]
    private string $nick;

    #[ORM\Column(name: "user_password", type: "string")]
    private ?string $password = null;

    #[ORM\Column(name: "user_password_temp", type: "string")]
    private ?string $passwordTemp;

    #[ORM\Column(name: "user_last_login", type: "integer")]
    private int $lastLogin = 0;

    #[ORM\Column(name: "user_last_online", type: "integer")]
    private int $lastOnline = 0;

    #[ORM\Column(name: "user_logintime", type: "integer")]
    private int $loginTime = 0;

    #[ORM\Column(name: "user_acttime", type: "integer")]
    private int $actionTime = 0;

    #[ORM\Column(name: "user_logouttime", type: "integer")]
    private int $logoutTime = 0;

    #[ORM\Column(name: "user_session_key", type: "string")]
    private ?string $sessionKey;

    #[ORM\Column(name: "user_email", type: "string")]
    private string $email;

    #[ORM\Column(name: "user_email_fix", type: "string")]
    private string $emailFix;

    #[ORM\Column(name: "user_ip", type: "string")]
    private ?string $ip;

    #[ORM\Column(name: "user_hostname", type: "string")]
    private ?string $hostname;

    #[ORM\Column(name: "user_blocked_from", type: "integer")]
    private int $blockedFrom = 0;

    #[ORM\Column(name: "user_blocked_to", type: "integer")]
    private int $blockedTo = 0;

    #[ORM\Column(name: "user_ban_reason", type: "string")]
    private ?string $banReason;

    #[ORM\Column(name: "user_attack_bans", type: "integer")]
    private int $attackBans = 0;

    #[ORM\JoinColumn(name: 'user_ban_admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $banAdmin = null;

    #[ORM\Column(name: "user_hmode_from", type: "integer")]
    private int $hmodFrom = 0;

    #[ORM\Column(name: "user_hmode_to", type: "integer")]
    private int $hmodTo = 0;

    #[ORM\JoinColumn(name: 'user_race_id', referencedColumnName: 'race_id')]
    #[ORM\ManyToOne(targetEntity: Race::class)]
    private ?Race $race = null;

    #[ORM\JoinColumn(name: 'user_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class, inversedBy: 'members')]
    private ?Alliance $alliance = null;

    #[ORM\Column(name: "user_alliance_shippoints", type: "integer")]
    private int $allianceShipPoints = 0;

    #[ORM\Column(name: "user_alliance_shippoints_used", type: "integer")]
    private int $allianceShipPointsUsed = 0;

    #[ORM\Column(name: "user_alliance_leave", type: "integer")]
    private int $allianceLeave = 0;

    #[ORM\Column(name: "user_sitting_days", type: "integer")]
    private int $sittingDays = 20;

    #[ORM\Column(name: "user_multi_delets", type: "integer")]
    private int $multiDelets = 0;

    #[ORM\Column(name: "user_setup", type: "boolean")]
    private bool $setup = false;


    #[ORM\Column(name: "user_points", type: "integer")]
    private int $points = 0;

    #[ORM\Column(name: "user_rank", type: "integer")]
    private int $rank = 0;

    #[ORM\Column(name: "user_rank_highest", type: "integer")]
    private int $rankHighest = 0;

    #[ORM\JoinColumn(name: 'user_alliance_rank_id', referencedColumnName: 'rank_id')]
    #[ORM\ManyToOne(targetEntity: AllianceRank::class)]
    private ?AllianceRank $allianceRank = null;

    #[ORM\Column(name: "user_registered", type: "integer")]
    private int $registered = 1097597003;

    #[ORM\Column(name: "user_profile_text", type: "string")]
    private ?string $profileText;

    #[ORM\Column(name: "user_ghost", type: "boolean")]
    private bool $ghost = false;

    #[ORM\Column(type: "integer")]
    private int $admin = 0;

    #[ORM\Column(name: "user_chatadmin", type: "integer")]
    private int $chatAdmin = 0;

    #[ORM\Column(name: "user_visits", type: "integer")]
    private int $visits = 0;

    #[ORM\Column(name: "user_avatar", type: "string")]
    private ?string $avatar;

    #[ORM\Column(name: "user_signature", type: "string")]
    private ?string $signature;

    #[ORM\Column(name: "user_client", type: "string")]
    private ?string $client;

    #[ORM\Column(name: "user_res_from_raid", type: "integer")]
    private int $resFromRaid = 0;

    #[ORM\Column(name: "user_res_from_tf", type: "integer")]
    private int $resFromTf = 0;

    #[ORM\Column(name: "user_res_from_asteroid", type: "integer")]
    private int $resFromAsteroid = 0;

    #[ORM\Column(name: "user_res_from_nebula", type: "integer")]
    private int $resFromNebula = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $userMainPlanetChanged = false;

    #[ORM\Column(name: "user_profile_board_url", type: "string")]
    private ?string $profileBoardUrl;

    #[ORM\Column(name: "user_profile_img", type: "string")]
    private ?string $profileImage;

    #[ORM\Column(name: "user_profile_img_check", type: "boolean")]
    private bool $profileImageCheck = false;

    #[ORM\ManyToOne(targetEntity: Specialist::class)]
    #[ORM\JoinColumn(name: 'user_specialist_id', referencedColumnName: 'specialist_id')]
    private ?Specialist $specialist = null;

    #[ORM\Column(name: "user_specialist_time", type: "integer")]
    private int $specialistTime = 0;

    #[ORM\Column(name: "user_deleted", type: "integer")]
    private int $deleted = 0;

    #[ORM\Column(name: "user_observe", type: "string")]
    private ?string $observe;

    #[ORM\Column(name: "lastinvasion", type: "integer")]
    private int $lastInvasion = 0;

    #[ORM\Column(name: "spyattack_counter", type: "integer")]
    private int $spyAttackCounter = 0;

    #[ORM\Column(name: "discoverymask", type: "string")]
    private ?string $discoveryMask;

    #[ORM\Column(name: "discoverymask_last_updated", type: "integer")]
    private int $discoveryMaskLastUpdated = 0;

    #[ORM\Column(type: "float")]
    private float $boostBonusProduction = 0;

    #[ORM\Column(type: "float")]
    private float $boostBonusBuilding = 0;

    #[ORM\Column(type: "string")]
    private ?string $dualEmail;

    #[ORM\Column(type: "string")]
    private ?string $dualName;

    #[ORM\Column(type: "string")]
    private ?string $verificationKey;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $npc = false;

    #[ORM\Column(type: "boolean")]
    private bool $userChangedMainPlanet = false;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: UserRating::class, cascade: ['persist'], orphanRemoval: true)]
    private ?UserRating $userRating;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: UserSession::class, orphanRemoval: true)]
    private ?UserSession $session;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Planet::class)]
    private ?Collection $planets;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TechnologyListItem::class, cascade: ['persist', 'remove'])]
    private Collection $techlist;
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLog::class, cascade: ['persist', 'remove'])]
    private Collection $logs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserComment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MissileListItem::class, cascade: ['persist', 'remove'])]
    private Collection $missiles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AllianceApplication::class, cascade: ['persist', 'remove'])]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ShipListItem::class, cascade: ['persist', 'remove'])]
    private Collection $shipList;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ShipQueueItem::class, cascade: ['persist', 'remove'])]
    private Collection $shipQueue;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DefenseListItem::class, cascade: ['persist', 'remove'])]
    private Collection $defenseList;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BuildingListItem::class, cascade: ['persist', 'remove'])]
    private Collection $buildingList;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Buddy::class, cascade: ['persist', 'remove'])]
    private Collection $buddyList;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MarketResource::class, cascade: ['persist', 'remove'])]
    private Collection $marketResources;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MarketShip::class, cascade: ['persist', 'remove'])]
    private Collection $marketShips;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MarketAuction::class, cascade: ['persist', 'remove'])]
    private Collection $marketAuctions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notepad::class, cascade: ['persist', 'remove'])]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Bookmark::class, cascade: ['persist', 'remove'])]
    private Collection $bookmarks;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FleetBookmark::class, cascade: ['persist', 'remove'])]
    private Collection $fleetBookmarks;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserMulti::class, cascade: ['persist', 'remove'])]
    private Collection $userMultis;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPoints::class, cascade: ['persist', 'remove'])]
    private Collection $userPoints;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserWarning::class, cascade: ['persist', 'remove'])]
    private Collection $userWarnings;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSitting::class, cascade: ['persist', 'remove'])]
    private Collection $userSittings;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSurveillance::class, cascade: ['persist', 'remove'])]
    private Collection $userSurveillances;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ticket::class, cascade: ['persist', 'remove'])]
    private Collection $tickets;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Report::class, cascade: ['persist', 'remove'])]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Fleet::class, cascade: ['persist', 'remove'])]
    private Collection $fleets;

    #[ORM\OneToMany(mappedBy: 'leader', targetEntity: Fleet::class, cascade: ['persist', 'remove'])]
    private Collection $fleetsLeader;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSessionLog::class, cascade: ['persist', 'remove'])]
    private Collection $sessionLogs;

    public function __construct()
    {
        $this->planets = new ArrayCollection();
        $this->techlist = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->missiles = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->shipList = new ArrayCollection();
        $this->shipQueue = new ArrayCollection();
        $this->defenseList = new ArrayCollection();
        $this->buildingList = new ArrayCollection();
        $this->buddyList = new ArrayCollection();
        $this->marketResources = new ArrayCollection();
        $this->marketShips = new ArrayCollection();
        $this->marketAuctions = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->fleetBookmarks = new ArrayCollection();
        $this->userMultis = new ArrayCollection();
        $this->userPoints = new ArrayCollection();
        $this->userWarnings = new ArrayCollection();
        $this->userSittings = new ArrayCollection();
        $this->userSurveillances = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->fleets = new ArrayCollection();
        $this->fleetsLeader = new ArrayCollection();
        $this->sessionLogs = new ArrayCollection();
    }

    public function __toString()
    {
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
    public function getPlanets(): ?Collection
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

    /**
     * @return Collection<int, MissileListItem>
     */
    public function getMissiles(): Collection
    {
        return $this->missiles;
    }

    public function addMissile(MissileListItem $missile): static
    {
        if (!$this->missiles->contains($missile)) {
            $this->missiles->add($missile);
            $missile->setUser($this);
        }

        return $this;
    }

    public function removeMissile(MissileListItem $missile): static
    {
        if ($this->missiles->removeElement($missile)) {
            // set the owning side to null (unless already changed)
            if ($missile->getUser() === $this) {
                $missile->setUser(null);
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

    public function addAppliction(AllianceApplication $appliction): static
    {
        if (!$this->applications->contains($appliction)) {
            $this->applications->add($appliction);
            $appliction->setUser($this);
        }

        return $this;
    }

    public function removeAppliction(AllianceApplication $appliction): static
    {
        if ($this->applications->removeElement($appliction)) {
            // set the owning side to null (unless already changed)
            if ($appliction->getUser() === $this) {
                $appliction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShipListItem>
     */
    public function getShipList(): Collection
    {
        return $this->shipList;
    }

    public function addShipList(ShipListItem $shipList): static
    {
        if (!$this->shipList->contains($shipList)) {
            $this->shipList->add($shipList);
            $shipList->setUser($this);
        }

        return $this;
    }

    public function removeShipList(ShipListItem $shipList): static
    {
        if ($this->shipList->removeElement($shipList)) {
            // set the owning side to null (unless already changed)
            if ($shipList->getUser() === $this) {
                $shipList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShipQueueItem>
     */
    public function getShipQueue(): Collection
    {
        return $this->shipQueue;
    }

    public function addShipQueue(ShipQueueItem $shipQueue): static
    {
        if (!$this->shipQueue->contains($shipQueue)) {
            $this->shipQueue->add($shipQueue);
            $shipQueue->setUser($this);
        }

        return $this;
    }

    public function removeShipQueue(ShipQueueItem $shipQueue): static
    {
        if ($this->shipQueue->removeElement($shipQueue)) {
            // set the owning side to null (unless already changed)
            if ($shipQueue->getUser() === $this) {
                $shipQueue->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DefenseListItem>
     */
    public function getDefenseList(): Collection
    {
        return $this->defenseList;
    }

    public function addDefenseList(DefenseListItem $defenseList): static
    {
        if (!$this->defenseList->contains($defenseList)) {
            $this->defenseList->add($defenseList);
            $defenseList->setUser($this);
        }

        return $this;
    }

    public function removeDefenseList(DefenseListItem $defenseList): static
    {
        if ($this->defenseList->removeElement($defenseList)) {
            // set the owning side to null (unless already changed)
            if ($defenseList->getUser() === $this) {
                $defenseList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BuildingListItem>
     */
    public function getBuildingList(): Collection
    {
        return $this->buildingList;
    }

    public function addBuildingList(BuildingListItem $buildingList): static
    {
        if (!$this->buildingList->contains($buildingList)) {
            $this->buildingList->add($buildingList);
            $buildingList->setUser($this);
        }

        return $this;
    }

    public function removeBuildingList(BuildingListItem $buildingList): static
    {
        if ($this->buildingList->removeElement($buildingList)) {
            // set the owning side to null (unless already changed)
            if ($buildingList->getUser() === $this) {
                $buildingList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Buddy>
     */
    public function getBuddyList(): Collection
    {
        return $this->buddyList;
    }

    public function addBuddyList(Buddy $buddyList): static
    {
        if (!$this->buddyList->contains($buddyList)) {
            $this->buddyList->add($buddyList);
            $buddyList->setUser($this);
        }

        return $this;
    }

    public function removeBuddyList(Buddy $buddyList): static
    {
        if ($this->buddyList->removeElement($buddyList)) {
            // set the owning side to null (unless already changed)
            if ($buddyList->getUser() === $this) {
                $buddyList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketResource>
     */
    public function getMarketResources(): Collection
    {
        return $this->marketResources;
    }

    public function addMarketResource(MarketResource $marketResource): static
    {
        if (!$this->marketResources->contains($marketResource)) {
            $this->marketResources->add($marketResource);
            $marketResource->setUser($this);
        }

        return $this;
    }

    public function removeMarketResource(MarketResource $marketResource): static
    {
        if ($this->marketResources->removeElement($marketResource)) {
            // set the owning side to null (unless already changed)
            if ($marketResource->getUser() === $this) {
                $marketResource->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketShip>
     */
    public function getMarketShips(): Collection
    {
        return $this->marketShips;
    }

    public function addMarketShip(MarketShip $marketShip): static
    {
        if (!$this->marketShips->contains($marketShip)) {
            $this->marketShips->add($marketShip);
            $marketShip->setUser($this);
        }

        return $this;
    }

    public function removeMarketShip(MarketShip $marketShip): static
    {
        if ($this->marketShips->removeElement($marketShip)) {
            // set the owning side to null (unless already changed)
            if ($marketShip->getUser() === $this) {
                $marketShip->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketAuction>
     */
    public function getMarketAuctions(): Collection
    {
        return $this->marketAuctions;
    }

    public function addMarketAuction(MarketAuction $marketAuction): static
    {
        if (!$this->marketAuctions->contains($marketAuction)) {
            $this->marketAuctions->add($marketAuction);
            $marketAuction->setUser($this);
        }

        return $this;
    }

    public function removeMarketAuction(MarketAuction $marketAuction): static
    {
        if ($this->marketAuctions->removeElement($marketAuction)) {
            // set the owning side to null (unless already changed)
            if ($marketAuction->getUser() === $this) {
                $marketAuction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notepad>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notepad $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Notepad $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bookmark>
     */
    public function getBookmarks(): Collection
    {
        return $this->bookmarks;
    }

    public function addBookmark(Bookmark $bookmark): static
    {
        if (!$this->bookmarks->contains($bookmark)) {
            $this->bookmarks->add($bookmark);
            $bookmark->setUser($this);
        }

        return $this;
    }

    public function removeBookmark(Bookmark $bookmark): static
    {
        if ($this->bookmarks->removeElement($bookmark)) {
            // set the owning side to null (unless already changed)
            if ($bookmark->getUser() === $this) {
                $bookmark->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FleetBookmark>
     */
    public function getFleetBookmarks(): Collection
    {
        return $this->fleetBookmarks;
    }

    public function addFleetBookmark(FleetBookmark $fleetBookmark): static
    {
        if (!$this->fleetBookmarks->contains($fleetBookmark)) {
            $this->fleetBookmarks->add($fleetBookmark);
            $fleetBookmark->setUser($this);
        }

        return $this;
    }

    public function removeFleetBookmark(FleetBookmark $fleetBookmark): static
    {
        if ($this->fleetBookmarks->removeElement($fleetBookmark)) {
            // set the owning side to null (unless already changed)
            if ($fleetBookmark->getUser() === $this) {
                $fleetBookmark->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserMulti>
     */
    public function getUserMultis(): Collection
    {
        return $this->userMultis;
    }

    public function addUserMulti(UserMulti $userMulti): static
    {
        if (!$this->userMultis->contains($userMulti)) {
            $this->userMultis->add($userMulti);
            $userMulti->setUser($this);
        }

        return $this;
    }

    public function removeUserMulti(UserMulti $userMulti): static
    {
        if ($this->userMultis->removeElement($userMulti)) {
            // set the owning side to null (unless already changed)
            if ($userMulti->getUser() === $this) {
                $userMulti->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserPoints>
     */
    public function getUserPoints(): Collection
    {
        return $this->userPoints;
    }

    public function addUserPoint(UserPoints $userPoint): static
    {
        if (!$this->userPoints->contains($userPoint)) {
            $this->userPoints->add($userPoint);
            $userPoint->setUser($this);
        }

        return $this;
    }

    public function removeUserPoint(UserPoints $userPoint): static
    {
        if ($this->userPoints->removeElement($userPoint)) {
            // set the owning side to null (unless already changed)
            if ($userPoint->getUser() === $this) {
                $userPoint->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserWarning>
     */
    public function getUserWarnings(): Collection
    {
        return $this->userWarnings;
    }

    public function addUserWarning(UserWarning $userWarning): static
    {
        if (!$this->userWarnings->contains($userWarning)) {
            $this->userWarnings->add($userWarning);
            $userWarning->setUser($this);
        }

        return $this;
    }

    public function removeUserWarning(UserWarning $userWarning): static
    {
        if ($this->userWarnings->removeElement($userWarning)) {
            // set the owning side to null (unless already changed)
            if ($userWarning->getUser() === $this) {
                $userWarning->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSitting>
     */
    public function getUserSittings(): Collection
    {
        return $this->userSittings;
    }

    public function addUserSitting(UserSitting $userSitting): static
    {
        if (!$this->userSittings->contains($userSitting)) {
            $this->userSittings->add($userSitting);
            $userSitting->setUser($this);
        }

        return $this;
    }

    public function removeUserSitting(UserSitting $userSitting): static
    {
        if ($this->userSittings->removeElement($userSitting)) {
            // set the owning side to null (unless already changed)
            if ($userSitting->getUser() === $this) {
                $userSitting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSurveillance>
     */
    public function getUserSurveillances(): Collection
    {
        return $this->userSurveillances;
    }

    public function addUserSurveillance(UserSurveillance $userSurveillance): static
    {
        if (!$this->userSurveillances->contains($userSurveillance)) {
            $this->userSurveillances->add($userSurveillance);
            $userSurveillance->setUser($this);
        }

        return $this;
    }

    public function removeUserSurveillance(UserSurveillance $userSurveillance): static
    {
        if ($this->userSurveillances->removeElement($userSurveillance)) {
            // set the owning side to null (unless already changed)
            if ($userSurveillance->getUser() === $this) {
                $userSurveillance->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getUser() === $this) {
                $ticket->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getUser() === $this) {
                $report->setUser(null);
            }
        }

        return $this;
    }

    public function addApplication(AllianceApplication $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setUser($this);
        }

        return $this;
    }

    public function removeApplication(AllianceApplication $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getUser() === $this) {
                $application->setUser(null);
            }
        }

        return $this;
    }

    public function getSession(): ?UserSession
    {
        return $this->session;
    }

    public function setSession(?UserSession $session): static
    {
        // unset the owning side of the relation if necessary
        if ($session === null && $this->session !== null) {
            $this->session->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($session !== null && $session->getUser() !== $this) {
            $session->setUser($this);
        }

        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection<int, Fleet>
     */
    public function getFleets(): Collection
    {
        return $this->fleets;
    }

    public function addFleet(Fleet $fleet): static
    {
        if (!$this->fleets->contains($fleet)) {
            $this->fleets->add($fleet);
            $fleet->setUser($this);
        }

        return $this;
    }

    public function removeFleet(Fleet $fleet): static
    {
        if ($this->fleets->removeElement($fleet)) {
            // set the owning side to null (unless already changed)
            if ($fleet->getUser() === $this) {
                $fleet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fleet>
     */
    public function getFleetsLeader(): Collection
    {
        return $this->fleetsLeader;
    }

    public function addFleetsLeader(Fleet $fleetsLeader): static
    {
        if (!$this->fleetsLeader->contains($fleetsLeader)) {
            $this->fleetsLeader->add($fleetsLeader);
            $fleetsLeader->setLeader($this);
        }

        return $this;
    }

    public function removeFleetsLeader(Fleet $fleetsLeader): static
    {
        if ($this->fleetsLeader->removeElement($fleetsLeader)) {
            // set the owning side to null (unless already changed)
            if ($fleetsLeader->getLeader() === $this) {
                $fleetsLeader->setLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSessionLog>
     */
    public function getSessionLogs(): Collection
    {
        return $this->sessionLogs;
    }

    public function addSessionLog(UserSessionLog $sessionLog): static
    {
        if (!$this->sessionLogs->contains($sessionLog)) {
            $this->sessionLogs->add($sessionLog);
            $sessionLog->setUser($this);
        }

        return $this;
    }

    public function removeSessionLog(UserSessionLog $sessionLog): static
    {
        if ($this->sessionLogs->removeElement($sessionLog)) {
            // set the owning side to null (unless already changed)
            if ($sessionLog->getUser() === $this) {
                $sessionLog->setUser(null);
            }
        }

        return $this;
    }
}
