<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Race\Race;
use EtoA\Specialist\Specialist;
use EtoA\User\ProfileImage;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use EtoA\User\UserRepository;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements \EtoA\User\UserInterface, PasswordAuthenticatedUserInterface,\Symfony\Component\Security\Core\User\UserInterface
{
    public const NAME_PATTERN = '/^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$/';
    public const NICK_PATTERN = '/^.[^\'\"\?\<\>\$\!\=\;\&]*$/';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    protected int $userId;

    #[ORM\Column(type: 'string', length: 180)]
    protected string $userName;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    protected string $userNick;

    #[ORM\Column(type: "string")]
    protected ?string $userPassword = null;

    #[ORM\Column(type: "string")]
    protected ?string $userPasswordTemp;

    #[ORM\Column(type: "integer")]
    protected int $userLastLogin;

    #[ORM\Column(type: "integer")]
    protected int $userLastOnline;

    #[ORM\Column(type: "integer")]
    protected int $userLoginTime;

    #[ORM\Column(type: "integer")]
    protected int $userActionTime;

    #[ORM\Column(type: "integer")]
    protected int $userLogoutTime;

    #[ORM\Column(type: "string")]
    protected ?string $userSessionKey;

    #[ORM\Column(type: "string")]
    protected string $userEmail;

    #[ORM\Column(type: "string")]
    protected string $userEmailFix;

    #[ORM\Column(type: "string")]
    protected ?string $userIp;

    #[ORM\Column(type: "string")]
    protected ?string $userHostname;

    #[ORM\Column(type: "integer")]
    protected int $userBlockedFrom;

    #[ORM\Column(type: "integer")]
    protected int $userBlockedTo;

    #[ORM\Column(type: "string")]
    protected ?string $userBanReason;

    #[ORM\Column(type: "integer")]
    protected int $userAttackBans;

    #[ORM\Column(type: "integer")]
    protected int $userBanAdminId;

    #[ORM\Column(type: "integer")]
    protected int $userHmodFrom;

    #[ORM\Column(type: "integer")]
    protected int $userhmodTo;

    #[ORM\ManyToOne(targetEntity: Race::class)]
    protected Race $userRaceId;

    #[ORM\Column(type: "integer")]
    protected int $userAllianceId;

    #[ORM\Column(type: "integer")]
    protected int $userAllianceShipPoints;

    #[ORM\Column(type: "integer")]
    protected int $userAllianceShipPointsUsed;

    #[ORM\Column(type: "integer")]
    protected int $userallianceLeave;

    #[ORM\Column(type: "integer")]
    protected int $usersittingDays;

    #[ORM\Column(type: "integer")]
    protected int $userMultiDelets;

    #[ORM\Column(type: "boolean")]
    protected bool $userSetup;


    #[ORM\Column(type: "integer")]
    protected int $userPoints;

    #[ORM\Column(type: "integer")]
    protected int $userRank;

    #[ORM\Column(type: "integer")]
    protected int $userRankHighest;

    #[ORM\Column(type: "integer")]
    protected int $userAllianceRankId;

    #[ORM\Column(type: "integer")]
    protected int $userRegistered;

    #[ORM\Column(type: "string")]
    protected ?string $userProfileText;

    #[ORM\Column(type: "boolean")]
    protected bool $userGhost;

    #[ORM\Column(type: "integer")]
    protected int $admin;

    #[ORM\Column(type: "integer")]
    protected int $userChatAdmin;

    #[ORM\Column(type: "integer")]
    protected int $userVisits;

    #[ORM\Column(type: "string")]
    protected ?string $userAvatar;

    #[ORM\Column(type: "string")]
    protected ?string $userSignature;

    #[ORM\Column(type: "string")]
    protected ?string $userClient;

    #[ORM\Column(type: "integer")]
    protected int $userResFromRaid;

    #[ORM\Column(type: "integer")]
    protected int $userResFromTf;

    #[ORM\Column(type: "integer")]
    protected int $userResFromAsteroid;

    #[ORM\Column(type: "integer")]
    protected int $userResFromNebula;

    #[ORM\Column(type: "integer")]
    protected int $userMainPlanetChanged;

    #[ORM\Column(type: "string")]
    protected ?string $userProfileBoardUrl;

    #[ORM\Column(type: "string")]
    protected ?string $userProfileImage;

    #[ORM\Column(type: "boolean")]
    protected bool $userProfileImageCheck;

    #[ORM\ManyToOne(targetEntity: Specialist::class)]
    protected Specialist $userSpecialistId;

    #[ORM\Column(type: "integer")]
    protected int $userSpecialistTime;

    #[ORM\Column(type: "integer")]
    protected int $userDeleted;

    #[ORM\Column(type: "string")]
    protected ?string $userObserve;

    #[ORM\Column(type: "integer")]
    protected int $lastInvasion;

    #[ORM\Column(type: "integer")]
    protected int $spyAttackCounter;

    #[ORM\Column(type: "string")]
    protected ?string $discoveryMask;

    #[ORM\Column(type: "integer")]
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
        return $this->userPassword;
    }

    public function getId(): int
    {
        return $this->userId;
    }

    public function getNick(): string
    {
        return $this->userNick;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserNick(): ?string
    {
        return $this->userNick;
    }

    public function setUserNick(string $userNick): static
    {
        $this->userNick = $userNick;

        return $this;
    }

    public function getUserPassword(): ?string
    {
        return $this->userPassword;
    }

    public function setUserPassword(string $userPassword): static
    {
        $this->userPassword = $userPassword;

        return $this;
    }

    public function getUserPasswordTemp(): ?string
    {
        return $this->userPasswordTemp;
    }

    public function setUserPasswordTemp(string $userPasswordTemp): static
    {
        $this->userPasswordTemp = $userPasswordTemp;

        return $this;
    }

    public function getUserLastLogin(): ?int
    {
        return $this->userLastLogin;
    }

    public function setUserLastLogin(int $userLastLogin): static
    {
        $this->userLastLogin = $userLastLogin;

        return $this;
    }

    public function getUserLastOnline(): ?int
    {
        return $this->userLastOnline;
    }

    public function setUserLastOnline(int $userLastOnline): static
    {
        $this->userLastOnline = $userLastOnline;

        return $this;
    }

    public function getUserLoginTime(): ?int
    {
        return $this->userLoginTime;
    }

    public function setUserLoginTime(int $userLoginTime): static
    {
        $this->userLoginTime = $userLoginTime;

        return $this;
    }

    public function getUserActionTime(): ?int
    {
        return $this->userActionTime;
    }

    public function setUserActionTime(int $userActionTime): static
    {
        $this->userActionTime = $userActionTime;

        return $this;
    }

    public function getUserLogoutTime(): ?int
    {
        return $this->userLogoutTime;
    }

    public function setUserLogoutTime(int $userLogoutTime): static
    {
        $this->userLogoutTime = $userLogoutTime;

        return $this;
    }

    public function getUserSessionKey(): ?string
    {
        return $this->userSessionKey;
    }

    public function setUserSessionKey(string $userSessionKey): static
    {
        $this->userSessionKey = $userSessionKey;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): static
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserEmailFix(): ?string
    {
        return $this->userEmailFix;
    }

    public function setUserEmailFix(string $userEmailFix): static
    {
        $this->userEmailFix = $userEmailFix;

        return $this;
    }

    public function getUserIp(): ?string
    {
        return $this->userIp;
    }

    public function setUserIp(string $userIp): static
    {
        $this->userIp = $userIp;

        return $this;
    }

    public function getUserHostname(): ?string
    {
        return $this->userHostname;
    }

    public function setUserHostname(string $userHostname): static
    {
        $this->userHostname = $userHostname;

        return $this;
    }

    public function getUserBlockedFrom(): ?int
    {
        return $this->userBlockedFrom;
    }

    public function setUserBlockedFrom(int $userBlockedFrom): static
    {
        $this->userBlockedFrom = $userBlockedFrom;

        return $this;
    }

    public function getUserBlockedTo(): ?int
    {
        return $this->userBlockedTo;
    }

    public function setUserBlockedTo(int $userBlockedTo): static
    {
        $this->userBlockedTo = $userBlockedTo;

        return $this;
    }

    public function getUserBanReason(): ?string
    {
        return $this->userBanReason;
    }

    public function setUserBanReason(string $userBanReason): static
    {
        $this->userBanReason = $userBanReason;

        return $this;
    }

    public function getUserAttackBans(): ?int
    {
        return $this->userAttackBans;
    }

    public function setUserAttackBans(int $userAttackBans): static
    {
        $this->userAttackBans = $userAttackBans;

        return $this;
    }

    public function getUserBanAdminId(): ?int
    {
        return $this->userBanAdminId;
    }

    public function setUserBanAdminId(int $userBanAdminId): static
    {
        $this->userBanAdminId = $userBanAdminId;

        return $this;
    }

    public function getUserHmodFrom(): ?int
    {
        return $this->userHmodFrom;
    }

    public function setUserHmodFrom(int $userHmodFrom): static
    {
        $this->userHmodFrom = $userHmodFrom;

        return $this;
    }

    public function getUserhmodTo(): ?int
    {
        return $this->userhmodTo;
    }

    public function setUserhmodTo(int $userhmodTo): static
    {
        $this->userhmodTo = $userhmodTo;

        return $this;
    }

    public function getUserAllianceId(): ?int
    {
        return $this->userAllianceId;
    }

    public function setUserAllianceId(int $userAllianceId): static
    {
        $this->userAllianceId = $userAllianceId;

        return $this;
    }

    public function getUserAllianceShipPoints(): ?int
    {
        return $this->userAllianceShipPoints;
    }

    public function setUserAllianceShipPoints(int $userAllianceShipPoints): static
    {
        $this->userAllianceShipPoints = $userAllianceShipPoints;

        return $this;
    }

    public function getUserAllianceShipPointsUsed(): ?int
    {
        return $this->userAllianceShipPointsUsed;
    }

    public function setUserAllianceShipPointsUsed(int $userAllianceShipPointsUsed): static
    {
        $this->userAllianceShipPointsUsed = $userAllianceShipPointsUsed;

        return $this;
    }

    public function getUserallianceLeave(): ?int
    {
        return $this->userallianceLeave;
    }

    public function setUserallianceLeave(int $userallianceLeave): static
    {
        $this->userallianceLeave = $userallianceLeave;

        return $this;
    }

    public function getUsersittingDays(): ?int
    {
        return $this->usersittingDays;
    }

    public function setUsersittingDays(int $usersittingDays): static
    {
        $this->usersittingDays = $usersittingDays;

        return $this;
    }

    public function getUserMultiDelets(): ?int
    {
        return $this->userMultiDelets;
    }

    public function setUserMultiDelets(int $userMultiDelets): static
    {
        $this->userMultiDelets = $userMultiDelets;

        return $this;
    }

    public function isUserSetup(): ?bool
    {
        return $this->userSetup;
    }

    public function setUserSetup(bool $userSetup): static
    {
        $this->userSetup = $userSetup;

        return $this;
    }

    public function getUserPoints(): ?int
    {
        return $this->userPoints;
    }

    public function setUserPoints(int $userPoints): static
    {
        $this->userPoints = $userPoints;

        return $this;
    }

    public function getUserRank(): ?int
    {
        return $this->userRank;
    }

    public function setUserRank(int $userRank): static
    {
        $this->userRank = $userRank;

        return $this;
    }

    public function getUserRankHighest(): ?int
    {
        return $this->userRankHighest;
    }

    public function setUserRankHighest(int $userRankHighest): static
    {
        $this->userRankHighest = $userRankHighest;

        return $this;
    }

    public function getUserAllianceRankId(): ?int
    {
        return $this->userAllianceRankId;
    }

    public function setUserAllianceRankId(int $userAllianceRankId): static
    {
        $this->userAllianceRankId = $userAllianceRankId;

        return $this;
    }

    public function getUserRegistered(): ?int
    {
        return $this->userRegistered;
    }

    public function setUserRegistered(int $userRegistered): static
    {
        $this->userRegistered = $userRegistered;

        return $this;
    }

    public function getUserProfileText(): ?string
    {
        return $this->userProfileText;
    }

    public function setUserProfileText(string $userProfileText): static
    {
        $this->userProfileText = $userProfileText;

        return $this;
    }

    public function isUserGhost(): ?bool
    {
        return $this->userGhost;
    }

    public function setUserGhost(bool $userGhost): static
    {
        $this->userGhost = $userGhost;

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

    public function getUserChatAdmin(): ?int
    {
        return $this->userChatAdmin;
    }

    public function setUserChatAdmin(int $userChatAdmin): static
    {
        $this->userChatAdmin = $userChatAdmin;

        return $this;
    }

    public function getUserVisits(): ?int
    {
        return $this->userVisits;
    }

    public function setUserVisits(int $userVisits): static
    {
        $this->userVisits = $userVisits;

        return $this;
    }

    public function getUserAvatar(): ?string
    {
        return $this->userAvatar;
    }

    public function setUserAvatar(string $userAvatar): static
    {
        $this->userAvatar = $userAvatar;

        return $this;
    }

    public function getUserSignature(): ?string
    {
        return $this->userSignature;
    }

    public function setUserSignature(string $userSignature): static
    {
        $this->userSignature = $userSignature;

        return $this;
    }

    public function getUserClient(): ?string
    {
        return $this->userClient;
    }

    public function setUserClient(string $userClient): static
    {
        $this->userClient = $userClient;

        return $this;
    }

    public function getUserResFromRaid(): ?int
    {
        return $this->userResFromRaid;
    }

    public function setUserResFromRaid(int $userResFromRaid): static
    {
        $this->userResFromRaid = $userResFromRaid;

        return $this;
    }

    public function getUserResFromTf(): ?int
    {
        return $this->userResFromTf;
    }

    public function setUserResFromTf(int $userResFromTf): static
    {
        $this->userResFromTf = $userResFromTf;

        return $this;
    }

    public function getUserResFromAsteroid(): ?int
    {
        return $this->userResFromAsteroid;
    }

    public function setUserResFromAsteroid(int $userResFromAsteroid): static
    {
        $this->userResFromAsteroid = $userResFromAsteroid;

        return $this;
    }

    public function getUserResFromNebula(): ?int
    {
        return $this->userResFromNebula;
    }

    public function setUserResFromNebula(int $userResFromNebula): static
    {
        $this->userResFromNebula = $userResFromNebula;

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

    public function getUserProfileBoardUrl(): ?string
    {
        return $this->userProfileBoardUrl;
    }

    public function setUserProfileBoardUrl(string $userProfileBoardUrl): static
    {
        $this->userProfileBoardUrl = $userProfileBoardUrl;

        return $this;
    }

    public function getUserProfileImage(): ?string
    {
        return $this->userProfileImage;
    }

    public function setUserProfileImage(string $userProfileImage): static
    {
        $this->userProfileImage = $userProfileImage;

        return $this;
    }

    public function isUserProfileImageCheck(): ?bool
    {
        return $this->userProfileImageCheck;
    }

    public function setUserProfileImageCheck(bool $userProfileImageCheck): static
    {
        $this->userProfileImageCheck = $userProfileImageCheck;

        return $this;
    }

    public function getUserSpecialistTime(): ?int
    {
        return $this->userSpecialistTime;
    }

    public function setUserSpecialistTime(int $userSpecialistTime): static
    {
        $this->userSpecialistTime = $userSpecialistTime;

        return $this;
    }

    public function getUserDeleted(): ?int
    {
        return $this->userDeleted;
    }

    public function setUserDeleted(int $userDeleted): static
    {
        $this->userDeleted = $userDeleted;

        return $this;
    }

    public function getUserObserve(): ?string
    {
        return $this->userObserve;
    }

    public function setUserObserve(string $userObserve): static
    {
        $this->userObserve = $userObserve;

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

    public function getUserRaceId(): ?Race
    {
        return $this->userRaceId;
    }

    public function setUserRaceId(?Race $userRaceId): static
    {
        $this->userRaceId = $userRaceId;

        return $this;
    }

    public function getUserSpecialistId(): ?Specialist
    {
        return $this->userSpecialistId;
    }

    public function setUserSpecialistId(?Specialist $userSpecialistId): static
    {
        $this->userSpecialistId = $userSpecialistId;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_PLAYER'];
    }

    public function eraseCredentials()
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->userNick;
    }
}
