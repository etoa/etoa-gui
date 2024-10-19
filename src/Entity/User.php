<?php

declare(strict_types=1);

namespace EtoA\Entity;

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

    #[ORM\Column(name: "user_name", type: 'string', length: 180)]
    protected string $name;

    #[ORM\Column(name: "user_nick", type: "string", length: 180, unique: true)]
    protected string $nick;

    #[ORM\Column(name: "user_password", type: "string")]
    protected ?string $password = null;

    #[ORM\Column(name: "user_password_temp", type: "string")]
    protected ?string $passwordTemp;

    #[ORM\Column(name: "user_last_login", type: "integer")]
    protected int $lastLogin;

    #[ORM\Column(name: "user_last_online", type: "integer")]
    protected int $lastOnline;

    #[ORM\Column(name: "user_logintime", type: "integer")]
    protected int $loginTime;

    #[ORM\Column(name: "user_acttime", type: "integer")]
    protected int $actionTime;

    #[ORM\Column(name: "user_logouttime", type: "integer")]
    protected int $logoutTime;

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
    protected int $blockedFrom;

    #[ORM\Column(name: "user_blocked_to", type: "integer")]
    protected int $blockedTo;

    #[ORM\Column(name: "user_ban_reason", type: "string")]
    protected ?string $banReason;

    #[ORM\Column(name: "user_attack_bans", type: "integer")]
    protected int $attackBans;

    #[ORM\Column(name: "user_ban_admin_id", type: "integer")]
    protected int $banAdminId;

    #[ORM\Column(name: "user_hmode_from", type: "integer")]
    protected int $hmodFrom;

    #[ORM\Column(name: "user_hmode_to", type: "integer")]
    protected int $hmodTo;

    #[ORM\Column(name: "user_race_id", type: "integer")]
    protected int $raceId;

    #[ORM\JoinColumn(name: 'user_race_id', referencedColumnName: 'race_id')]
    #[ORM\ManyToOne(targetEntity: Race::class)]
    protected Race|null $race;

    #[ORM\Column(name: "user_alliance_id", type: "integer")]
    protected int $allianceId;

    #[ORM\Column(name: "user_alliance_shippoints", type: "integer")]
    protected int $allianceShipPoints;

    #[ORM\Column(name: "user_alliance_shippoints_used", type: "integer")]
    protected int $allianceShipPointsUsed;

    #[ORM\Column(name: "user_alliance_leave", type: "integer")]
    protected int $allianceLeave;

    #[ORM\Column(name: "user_sitting_days", type: "integer")]
    protected int $sittingDays;

    #[ORM\Column(name: "user_multi_delets", type: "integer")]
    protected int $multiDelets;

    #[ORM\Column(name: "user_setup", type: "boolean")]
    protected bool $setup;


    #[ORM\Column(name: "user_points", type: "integer")]
    protected int $points;

    #[ORM\Column(name: "user_rank", type: "integer")]
    protected int $rank;

    #[ORM\Column(name: "user_rank_highest", type: "integer")]
    protected int $rankHighest;

    #[ORM\Column(name: "user_alliance_rank_id", type: "integer")]
    protected int $allianceRankId;

    #[ORM\Column(name: "user_registered", type: "integer")]
    protected int $registered;

    #[ORM\Column(name: "user_profile_text", type: "string")]
    protected ?string $profileText;

    #[ORM\Column(name: "user_ghost", type: "boolean")]
    protected bool $ghost;

    #[ORM\Column(type: "integer")]
    protected int $admin;

    #[ORM\Column(name: "user_chatadmin", type: "integer")]
    protected int $chatAdmin;

    #[ORM\Column(name: "user_visits", type: "integer")]
    protected int $visits;

    #[ORM\Column(name: "user_avatar", type: "string")]
    protected ?string $avatar;

    #[ORM\Column(name: "user_signature", type: "string")]
    protected ?string $signature;

    #[ORM\Column(name: "user_client", type: "string")]
    protected ?string $client;

    #[ORM\Column(name: "user_res_from_raid", type: "integer")]
    protected int $resFromRaid;

    #[ORM\Column(name: "user_res_from_tf", type: "integer")]
    protected int $resFromTf;

    #[ORM\Column(name: "user_res_from_asteroid", type: "integer")]
    protected int $resFromAsteroid;

    #[ORM\Column(name: "user_res_from_nebula", type: "integer")]
    protected int $resFromNebula;

    #[ORM\Column(type: "integer")]
    protected int $userMainPlanetChanged;

    #[ORM\Column(name: "user_profile_board_url", type: "string")]
    protected ?string $profileBoardUrl;

    #[ORM\Column(name: "user_profile_img", type: "string")]
    protected ?string $profileImage;

    #[ORM\Column(name: "user_profile_img_check", type: "boolean")]
    protected bool $profileImageCheck;

    #[ORM\Column(name: "user_specialist_id", type: "integer")]
    protected int $specialistId;

    #[ORM\ManyToOne(targetEntity: Specialist::class)]
    #[ORM\JoinColumn(name: 'user_specialist_id', referencedColumnName: 'specialist_id')]
    protected Specialist $specialist;

    #[ORM\Column(name: "user_specialist_time", type: "integer")]
    protected int $specialistTime;

    #[ORM\Column(name: "user_deleted", type: "integer")]
    protected int $deleted;

    #[ORM\Column(name: "user_observe", type: "string")]
    protected ?string $observe;

    #[ORM\Column(name: "lastinvasion", type: "integer")]
    protected int $lastInvasion;

    #[ORM\Column(name: "spyattack_counter", type: "integer")]
    protected int $spyAttackCounter;

    #[ORM\Column(name: "discoverymask", type: "string")]
    protected ?string $discoveryMask;

    #[ORM\Column(name: "discoverymask_last_updated", type: "integer")]
    protected int $discoveryMaskLastUpdated;

    #[ORM\Column(type: "float")]
    protected float $boostBonusProduction;

    #[ORM\Column(type: "float")]
    protected float $boostBonusBuilding;

    #[ORM\Column(type: "string")]
    protected ?string $dualEmail;

    #[ORM\Column(type: "string")]
    protected ?string $dualName;

    #[ORM\Column(type: "string")]
    protected ?string $verificationKey;

    #[ORM\Column(type: "integer")]
    protected int $npc;

    #[ORM\Column(type: "boolean")]
    protected bool $userChangedMainPlanet;

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

    public function getBanAdminId(): ?int
    {
        return $this->banAdminId;
    }

    public function setBanAdminId(int $banAdminId): static
    {
        $this->banAdminId = $banAdminId;

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

    public function getRaceId(): ?int
    {
        return $this->raceId;
    }

    public function setRaceId(int $raceId): static
    {
        $this->raceId = $raceId;

        return $this;
    }

    public function getAllianceId(): ?int
    {
        return $this->allianceId;
    }

    public function setAllianceId(int $allianceId): static
    {
        $this->allianceId = $allianceId;

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

    public function getAllianceRankId(): ?int
    {
        return $this->allianceRankId;
    }

    public function setAllianceRankId(int $allianceRankId): static
    {
        $this->allianceRankId = $allianceRankId;

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

    public function getUserMainPlanetChanged(): ?int
    {
        return $this->userMainPlanetChanged;
    }

    public function setUserMainPlanetChanged(int $userMainPlanetChanged): static
    {
        $this->userMainPlanetChanged = $userMainPlanetChanged;

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

    public function getSpecialistId()
    {
        return $this->specialistId;
    }

    public function setSpecialistId($specialistId): static
    {
        $this->specialistId = $specialistId;

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

    public function getNpc(): ?int
    {
        return $this->npc;
    }

    public function setNpc(int $npc): static
    {
        $this->npc = $npc;

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
}
