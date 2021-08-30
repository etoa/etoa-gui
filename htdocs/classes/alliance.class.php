<?php

use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceMemberCosts;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\MessageRepository;
use EtoA\User\UserService;

/**
 * The alliance object
 *
 * @author MrCage <mrcage@etoa.ch>
 * @copyright Copyright (c) 2004-2009 by EtoA Gaming, www.etoa.ch
 *
 * @property-read string $imageUrl
 * @property-read float $avgPoints
 */
class Alliance
{
    protected $id;
    protected $name;
    protected $tag;
    protected $points;
    protected $memberCount;
    protected $visits;
    protected $visitsExt;
    protected $text;
    protected $image;
    protected $url;
    protected $acceptApplications;
    protected $acceptPact;
    protected $foundationDate;
    protected $publicMemberList;

    protected $wings = null;
    protected $wingRequests = null;
    protected $ranks = null;

    protected $founderId;
    protected $founder = null;

    protected $motherId;
    protected $mother = null;
    protected $motherRequestId;
    protected $motherRequest = null;

    protected $valid;
    protected $changedFields;

    protected $resMetal;
    protected $resCrystal;
    protected $resPlastic;
    protected $resFuel;
    protected $resFood;

    protected $allianceObjectsForMembers;

    private ConfigurationService $config;

    /**
     * Constructor
     */
    public function __construct($id)
    {
        // TODO
        global $app;

        $this->config = $app[ConfigurationService::class];

        $this->id = $id;
        $this->valid = false;
        $res = dbquery("
          SELECT
              alliances.*,
              COUNT(users.user_id) as member_count
          FROM
              alliances
          LEFT JOIN
              users
              ON user_alliance_id=alliance_id
          WHERE
              alliance_id=" . $this->id . "
          GROUP BY
              alliance_id
          LIMIT 1;
          ");
        if (mysql_num_rows($res) > 0) {
            $arr = mysql_fetch_assoc($res);
            $this->name = $arr['alliance_name'];
            $this->tag = $arr['alliance_tag'];
            $this->motherId = $arr['alliance_mother'];
            $this->motherRequestId = $arr['alliance_mother_request'];
            $this->points = $arr['alliance_points'];
            $this->memberCount = $arr['member_count'];
            $this->founderId = $arr['alliance_founder_id'];
            $this->visits = $arr['alliance_visits'];
            $this->visitsExt = $arr['alliance_visits_ext'];
            $this->text = $arr['alliance_text'];
            $this->image = $arr['alliance_img'];
            $this->url = $arr['alliance_url'];
            $this->acceptApplications = (bool)$arr['alliance_accept_applications'];
            $this->acceptPact = (bool)$arr['alliance_accept_bnd'];
            $this->publicMemberList = (bool)$arr['alliance_public_memberlist'];
            $this->foundationDate = $arr['alliance_foundation_date'];

            $this->resMetal = $arr['alliance_res_metal'];
            $this->resCrystal = $arr['alliance_res_crystal'];
            $this->resPlastic = $arr['alliance_res_plastic'];
            $this->resFuel = $arr['alliance_res_fuel'];
            $this->resFood = $arr['alliance_res_food'];

            $this->allianceObjectsForMembers = $arr['alliance_objects_for_members'];

            $this->changedFields = array();
            $this->valid = true;
        }
    }
}
