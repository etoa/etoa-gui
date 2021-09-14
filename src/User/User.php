<?php

declare(strict_types=1);

namespace EtoA\User;

class User
{
    public const NAME_PATTERN = '/^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$/';
    public const NICK_PATTERN = '/^.[^\'\"\?\<\>\$\!\=\;\&]*$/';

    public int $id;
    public string $name;
    public string $nick;
    public string $password;
    public ?string $passwordTemp;
    public int $lastLogin;
    public int $lastOnline;
    public int $loginTime;
    public int $actionTime;
    public int $logoutTime;
    public ?string $sessionKey;
    public string $email;
    public string $emailFix;
    public ?string $ip;
    public ?string $hostname;
    public int $blockedFrom;
    public int $blockedTo;
    public ?string $banReason;
    public int $attackBans;
    public int $banAdminId;
    public int $hmodFrom;
    public int $hmodTo;
    public int $raceId;
    public int $allianceId;
    public int $allianceShipPoints;
    public int $allianceShipPointsUsed;
    public int $allianceLeave;
    public int $sittingDays;
    public int $multiDelets;
    public bool $setup;
    public int $points;
    public int $rank;
    public int $rankHighest;
    public int $allianceRankId;
    public int $registered;
    public ?string $profileText;
    public bool $ghost;
    public int $admin;
    public int $chatAdmin;
    public int $visits;
    public ?string $avatar;
    public ?string $signature;
    public ?string $client;
    public int $resFromRaid;
    public int $resFromTf;
    public int $resFromAsteroid;
    public int $resFromNebula;
    public int $userMainPlanetChanged;
    public ?string $profileBoardUrl;
    public ?string $profileImage;
    public bool $profileImageCheck;
    public int $specialistId;
    public int $specialistTime;
    public int $deleted;
    public ?string $observe;
    public int $lastInvasion;
    public int $spyAttackCounter;
    public ?string $discoveryMask;
    public int $discoveryMaskLastUpdated;
    public float $boosBonusProduction;
    public float $boosBonusBuilding;
    public ?string $dualEmail;
    public ?string $dualName;
    public ?string $verificationKey;
    public int $npc;
    public bool $userChangedMainPlanet;

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
        $this->boosBonusProduction = (float) $data['boost_bonus_production'];
        $this->boosBonusBuilding = (float) $data['boost_bonus_building'];
        $this->dualEmail = $data['dual_email'];
        $this->dualName = $data['dual_name'];
        $this->verificationKey = $data['verification_key'];
        $this->npc = (int) $data['npc'];
        $this->userChangedMainPlanet = (bool) $data['user_changed_main_planet'];
    }

    public function getProfileImageUrl(): ?string
    {
        if ($this->profileImage == '') {
            return null;
        }

        return ProfileImage::IMAGE_PATH . $this->profileImage;
    }
}
