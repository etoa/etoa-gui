<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Admin\AllianceBoardAvatar;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements \EtoA\User\UserInterface, PasswordAuthenticatedUserInterface
{
    public const NAME_PATTERN = '/^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$/';
    public const NICK_PATTERN = '/^.[^\'\"\?\<\>\$\!\=\;\&]*$/';

    protected int $id;
    protected string $name;
    protected string $nick;
    protected string $password;
    protected ?string $passwordTemp;
    protected int $lastLogin;
    protected int $lastOnline;
    protected int $loginTime;
    protected int $actionTime;
    protected int $logoutTime;
    protected ?string $sessionKey;
    protected string $email;
    protected string $emailFix;
    protected ?string $ip;
    protected ?string $hostname;
    protected int $blockedFrom;
    protected int $blockedTo;
    protected ?string $banReason;
    protected int $attackBans;
    protected int $banAdminId;
    protected int $hmodFrom;
    protected int $hmodTo;
    protected int $raceId;
    protected int $allianceId;
    protected int $allianceShipPoints;
    protected int $allianceShipPointsUsed;
    protected int $allianceLeave;
    protected int $sittingDays;
    protected int $multiDelets;
    protected bool $setup;
    protected int $points;
    protected int $rank;
    protected int $rankHighest;
    protected int $allianceRankId;
    protected int $registered;
    protected ?string $profileText;
    protected bool $ghost;
    protected int $admin;
    protected int $chatAdmin;
    protected int $visits;
    protected ?string $avatar;
    protected ?string $signature;
    protected ?string $client;
    protected int $resFromRaid;
    protected int $resFromTf;
    protected int $resFromAsteroid;
    protected int $resFromNebula;
    protected int $userMainPlanetChanged;
    protected ?string $profileBoardUrl;
    protected ?string $profileImage;
    protected bool $profileImageCheck;
    protected int $specialistId;
    protected int $specialistTime;
    protected int $deleted;
    protected ?string $observe;
    protected int $lastInvasion;
    protected int $spyAttackCounter;
    protected ?string $discoveryMask;
    protected int $discoveryMaskLastUpdated;
    protected float $boostBonusProduction;
    protected float $boostBonusBuilding;
    protected ?string $dualEmail;
    protected ?string $dualName;
    protected ?string $verificationKey;
    protected int $npc;
    protected bool $userChangedMainPlanet;

    public function __construct(array $data)
    {
        $this->id = (int) $data['user_id'];
        $this->name = $data['user_name'];
        $this->nick = $data['user_nick'];
        $this->password = $data['user_password'];
        $this->passwordTemp = $data['user_password_temp'];
        $this->lastOnline = (int) $data['user_last_online'];
        $this->lastLogin = (int) $data['user_last_login'];
        $this->loginTime = (int) $data['user_logintime'];
        $this->actionTime = (int) $data['user_acttime'];
        $this->logoutTime = (int) $data['user_logouttime'];
        $this->sessionKey = $data['user_session_key'];
        $this->email = $data['user_email'];
        $this->emailFix = $data['user_email_fix'];
        $this->ip = $data['user_ip'];
        $this->hostname = $data['user_hostname'];
        $this->blockedFrom = (int) $data['user_blocked_from'];
        $this->blockedTo = (int) $data['user_blocked_to'];
        $this->banReason = $data['user_ban_reason'];
        $this->attackBans = (int) $data['user_attack_bans'];
        $this->banAdminId = (int) $data['user_ban_admin_id'];
        $this->hmodFrom = (int) $data['user_hmode_from'];
        $this->hmodTo = (int) $data['user_hmode_to'];
        $this->raceId = (int) $data['user_race_id'];
        $this->allianceId = (int) $data['user_alliance_id'];
        $this->allianceShipPoints = (int) $data['user_alliace_shippoints'];
        $this->allianceShipPointsUsed = (int) $data['user_alliace_shippoints_used'];
        $this->allianceLeave = (int) $data['user_alliance_leave'];
        $this->sittingDays = (int) $data['user_sitting_days'];
        $this->multiDelets = (int) $data['user_multi_delets'];
        $this->setup = (bool) $data['user_setup'];
        $this->points = (int) $data['user_points'];
        $this->rank = (int) $data['user_rank'];
        $this->rankHighest = (int) $data['user_rank_highest'];
        $this->allianceRankId = (int) $data['user_alliance_rank_id'];
        $this->registered = (int) $data['user_registered'];
        $this->profileText = $data['user_profile_text'];
        $this->ghost = (bool) $data['user_ghost'];
        $this->admin = (int) $data['admin'];
        $this->chatAdmin = (int) $data['user_chatadmin'];
        $this->visits = (int) $data['user_visits'];
        $this->avatar = $data['user_avatar'];
        $this->signature = $data['user_signature'];
        $this->client = $data['user_client'];
        $this->resFromRaid = (int) $data['user_res_from_raid'];
        $this->resFromTf = (int) $data['user_res_from_tf'];
        $this->resFromAsteroid = (int) $data['user_res_from_asteroid'];
        $this->resFromNebula = (int) $data['user_res_from_nebula'];
        $this->userMainPlanetChanged = (int) $data['user_main_planet_changed'];
        $this->profileBoardUrl = $data['user_profile_board_url'];
        $this->profileImage = $data['user_profile_img'];
        $this->profileImageCheck = (bool) $data['user_profile_img_check'];
        $this->specialistId = (int) $data['user_specialist_id'];
        $this->specialistTime = (int) $data['user_specialist_time'];
        $this->deleted = (int) $data['user_deleted'];
        $this->observe = $data['user_observe'];
        $this->lastInvasion = (int) $data['lastinvasion'];
        $this->spyAttackCounter = (int) $data['spyattack_counter'];
        $this->discoveryMask = $data['discoverymask'];
        $this->discoveryMaskLastUpdated = (int) $data['discoverymask_last_updated'];
        $this->boostBonusProduction = (float) $data['boost_bonus_production'];
        $this->boostBonusBuilding = (float) $data['boost_bonus_building'];
        $this->dualEmail = $data['dual_email'];
        $this->dualName = $data['dual_name'];
        $this->verificationKey = $data['verification_key'];
        $this->npc = (int) $data['npc'];
        $this->userChangedMainPlanet = (bool) $data['user_changed_main_planet'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPasswordTemp(): ?string
    {
        return $this->passwordTemp;
    }

    public function setPasswordTemp(?string $passwordTemp): void
    {
        $this->passwordTemp = $passwordTemp;
    }

    public function getLastLogin(): int
    {
        return $this->lastLogin;
    }

    public function setLastLogin(int $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getLastOnline(): int
    {
        return $this->lastOnline;
    }

    public function setLastOnline(int $lastOnline): void
    {
        $this->lastOnline = $lastOnline;
    }

    public function getLoginTime(): int
    {
        return $this->loginTime;
    }

    public function setLoginTime(int $loginTime): void
    {
        $this->loginTime = $loginTime;
    }

    public function getActionTime(): int
    {
        return $this->actionTime;
    }

    public function setActionTime(int $actionTime): void
    {
        $this->actionTime = $actionTime;
    }

    public function getLogoutTime(): int
    {
        return $this->logoutTime;
    }

    public function setLogoutTime(int $logoutTime): void
    {
        $this->logoutTime = $logoutTime;
    }

    public function getSessionKey(): ?string
    {
        return $this->sessionKey;
    }

    public function setSessionKey(?string $sessionKey): void
    {
        $this->sessionKey = $sessionKey;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmailFix(): string
    {
        return $this->emailFix;
    }

    public function setEmailFix(string $emailFix): void
    {
        $this->emailFix = $emailFix;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): void
    {
        $this->hostname = $hostname;
    }

    public function getBlockedFrom(): int
    {
        return $this->blockedFrom;
    }

    public function setBlockedFrom(int $blockedFrom): void
    {
        $this->blockedFrom = $blockedFrom;
    }

    public function getBlockedTo(): int
    {
        return $this->blockedTo;
    }

    public function setBlockedTo(int $blockedTo): void
    {
        $this->blockedTo = $blockedTo;
    }

    public function getBanReason(): ?string
    {
        return $this->banReason;
    }

    public function setBanReason(?string $banReason): void
    {
        $this->banReason = $banReason;
    }

    public function getAttackBans(): int
    {
        return $this->attackBans;
    }

    public function setAttackBans(int $attackBans): void
    {
        $this->attackBans = $attackBans;
    }

    public function getBanAdminId(): int
    {
        return $this->banAdminId;
    }

    public function setBanAdminId(int $banAdminId): void
    {
        $this->banAdminId = $banAdminId;
    }

    public function getHmodFrom(): int
    {
        return $this->hmodFrom;
    }

    public function setHmodFrom(int $hmodFrom): void
    {
        $this->hmodFrom = $hmodFrom;
    }

    public function getHmodTo(): int
    {
        return $this->hmodTo;
    }

    public function setHmodTo(int $hmodTo): void
    {
        $this->hmodTo = $hmodTo;
    }

    public function getRaceId(): int
    {
        return $this->raceId;
    }

    public function setRaceId(int $raceId): void
    {
        $this->raceId = $raceId;
    }

    public function getAllianceId(): int
    {
        return $this->allianceId;
    }

    public function setAllianceId(int $allianceId): void
    {
        $this->allianceId = $allianceId;
    }

    public function getAllianceShipPoints(): int
    {
        return $this->allianceShipPoints;
    }

    public function setAllianceShipPoints(int $allianceShipPoints): void
    {
        $this->allianceShipPoints = $allianceShipPoints;
    }

    public function getAllianceShipPointsUsed(): int
    {
        return $this->allianceShipPointsUsed;
    }

    public function setAllianceShipPointsUsed(int $allianceShipPointsUsed): void
    {
        $this->allianceShipPointsUsed = $allianceShipPointsUsed;
    }

    public function getAllianceLeave(): int
    {
        return $this->allianceLeave;
    }

    public function setAllianceLeave(int $allianceLeave): void
    {
        $this->allianceLeave = $allianceLeave;
    }

    public function getSittingDays(): int
    {
        return $this->sittingDays;
    }

    public function setSittingDays(int $sittingDays): void
    {
        $this->sittingDays = $sittingDays;
    }

    public function getMultiDelets(): int
    {
        return $this->multiDelets;
    }

    public function setMultiDelets(int $multiDelets): void
    {
        $this->multiDelets = $multiDelets;
    }

    public function isSetup(): bool
    {
        return $this->setup;
    }

    public function setSetup(bool $setup): void
    {
        $this->setup = $setup;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function setRank(int $rank): void
    {
        $this->rank = $rank;
    }

    public function getRankHighest(): int
    {
        return $this->rankHighest;
    }

    public function setRankHighest(int $rankHighest): void
    {
        $this->rankHighest = $rankHighest;
    }

    public function getAllianceRankId(): int
    {
        return $this->allianceRankId;
    }

    public function setAllianceRankId(int $allianceRankId): void
    {
        $this->allianceRankId = $allianceRankId;
    }

    public function getRegistered(): int
    {
        return $this->registered;
    }

    public function setRegistered(int $registered): void
    {
        $this->registered = $registered;
    }

    public function getProfileText(): ?string
    {
        return $this->profileText;
    }

    public function setProfileText(?string $profileText): void
    {
        $this->profileText = $profileText;
    }

    public function isGhost(): bool
    {
        return $this->ghost;
    }

    public function setGhost(bool $ghost): void
    {
        $this->ghost = $ghost;
    }

    public function getAdmin(): int
    {
        return $this->admin;
    }

    public function setAdmin(int $admin): void
    {
        $this->admin = $admin;
    }

    public function getChatAdmin(): int
    {
        return $this->chatAdmin;
    }

    public function setChatAdmin(int $chatAdmin): void
    {
        $this->chatAdmin = $chatAdmin;
    }

    public function getVisits(): int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): void
    {
        $this->visits = $visits;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): void
    {
        $this->signature = $signature;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): void
    {
        $this->client = $client;
    }

    public function getResFromRaid(): int
    {
        return $this->resFromRaid;
    }

    public function setResFromRaid(int $resFromRaid): void
    {
        $this->resFromRaid = $resFromRaid;
    }

    public function getResFromTf(): int
    {
        return $this->resFromTf;
    }

    public function setResFromTf(int $resFromTf): void
    {
        $this->resFromTf = $resFromTf;
    }

    public function getResFromAsteroid(): int
    {
        return $this->resFromAsteroid;
    }

    public function setResFromAsteroid(int $resFromAsteroid): void
    {
        $this->resFromAsteroid = $resFromAsteroid;
    }

    public function getResFromNebula(): int
    {
        return $this->resFromNebula;
    }

    public function setResFromNebula(int $resFromNebula): void
    {
        $this->resFromNebula = $resFromNebula;
    }

    public function getUserMainPlanetChanged(): int
    {
        return $this->userMainPlanetChanged;
    }

    public function setUserMainPlanetChanged(int $userMainPlanetChanged): void
    {
        $this->userMainPlanetChanged = $userMainPlanetChanged;
    }

    public function getProfileBoardUrl(): ?string
    {
        return $this->profileBoardUrl;
    }

    public function setProfileBoardUrl(?string $profileBoardUrl): void
    {
        $this->profileBoardUrl = $profileBoardUrl;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): void
    {
        $this->profileImage = $profileImage;
    }

    public function isProfileImageCheck(): bool
    {
        return $this->profileImageCheck;
    }

    public function setProfileImageCheck(bool $profileImageCheck): void
    {
        $this->profileImageCheck = $profileImageCheck;
    }

    public function getSpecialistId(): int
    {
        return $this->specialistId;
    }

    public function setSpecialistId(int $specialistId): void
    {
        $this->specialistId = $specialistId;
    }

    public function getSpecialistTime(): int
    {
        return $this->specialistTime;
    }

    public function setSpecialistTime(int $specialistTime): void
    {
        $this->specialistTime = $specialistTime;
    }

    public function getDeleted(): int
    {
        return $this->deleted;
    }

    public function setDeleted(int $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getObserve(): ?string
    {
        return $this->observe;
    }

    public function setObserve(?string $observe): void
    {
        $this->observe = $observe;
    }

    public function getLastInvasion(): int
    {
        return $this->lastInvasion;
    }

    public function setLastInvasion(int $lastInvasion): void
    {
        $this->lastInvasion = $lastInvasion;
    }

    public function getSpyAttackCounter(): int
    {
        return $this->spyAttackCounter;
    }

    public function setSpyAttackCounter(int $spyAttackCounter): void
    {
        $this->spyAttackCounter = $spyAttackCounter;
    }

    public function getDiscoveryMask(): ?string
    {
        return $this->discoveryMask;
    }

    public function setDiscoveryMask(?string $discoveryMask): void
    {
        $this->discoveryMask = $discoveryMask;
    }

    public function getDiscoveryMaskLastUpdated(): int
    {
        return $this->discoveryMaskLastUpdated;
    }

    public function setDiscoveryMaskLastUpdated(int $discoveryMaskLastUpdated): void
    {
        $this->discoveryMaskLastUpdated = $discoveryMaskLastUpdated;
    }

    public function getboostBonusProduction(): float
    {
        return $this->boostBonusProduction;
    }

    public function setboostBonusProduction(float $boostBonusProduction): void
    {
        $this->boostBonusProduction = $boostBonusProduction;
    }

    public function getboostBonusBuilding(): float
    {
        return $this->boostBonusBuilding;
    }

    public function setboostBonusBuilding(float $boostBonusBuilding): void
    {
        $this->boostBonusBuilding = $boostBonusBuilding;
    }

    public function getDualEmail(): ?string
    {
        return $this->dualEmail;
    }

    public function setDualEmail(?string $dualEmail): void
    {
        $this->dualEmail = $dualEmail;
    }

    public function getDualName(): ?string
    {
        return $this->dualName;
    }

    public function setDualName(?string $dualName): void
    {
        $this->dualName = $dualName;
    }

    public function getVerificationKey(): ?string
    {
        return $this->verificationKey;
    }

    public function setVerificationKey(?string $verificationKey): void
    {
        $this->verificationKey = $verificationKey;
    }

    public function getNpc(): int
    {
        return $this->npc;
    }

    public function setNpc(int $npc): void
    {
        $this->npc = $npc;
    }

    public function isUserChangedMainPlanet(): bool
    {
        return $this->userChangedMainPlanet;
    }

    public function setUserChangedMainPlanet(bool $userChangedMainPlanet): void
    {
        $this->userChangedMainPlanet = $userChangedMainPlanet;
    }

    public function getProfileImageUrl(): ?string
    {
        if ($this->profileImage == '') {
            return null;
        }

        return ProfileImage::IMAGE_PATH . $this->profileImage;
    }

    public function getAvatarUrl(): ?string
    {
        if ($this->avatar == '') {
            return null;
        }

        return AllianceBoardAvatar::IMAGE_PATH . $this->avatar;
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }
}
