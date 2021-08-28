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
    protected $members = null;
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

    //
    // Magic functions
    //

    /**
     * Returns a propperly formated alliance name
     */
    public function __toString()
    {
        return "[" . $this->tag . "] " . $this->name;
    }

    /**
     * Gets alliance properties
     */
    public function __get($key)
    {
        try {
            // Return special non-defined properties
            if ($key == "avgPoints")
                return floor($this->points / $this->memberCount);
            if ($key == "imageUrl")
                return ALLIANCE_IMG_DIR . "/" . $this->image;

            // Check if property exists
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in der Klasse " . __CLASS__);

            // Do actions for some special properties
            if ($key == "members" && $this->members == null)
                $this->getMembers();
            if ($key == "wings" && $this->wings == null)
                $this->getWings();
            if ($key == "wingRequests" && $this->wingRequests == null)
                $this->getWingRequests();
            if ($key == "mother" && $this->mother == null)
                $this->mother = new Alliance($this->motherId);
            if ($key == "motherRequest" && $this->motherRequest == null)
                $this->motherRequest = new Alliance($this->motherRequestId);
            if ($key == "founder" && $this->founder == null) {
                if (isset($this->members[$this->founderId]))
                    $this->founder = &$this->members[$this->founderId];
                else
                    $this->founder = new User($this->founderId);
            }

            // Protected properties
            if ($key == "changedFields")
                throw new EException("Property $key der Klasse " . __CLASS__ . " ist geschützt!");


            return $this->$key;
        } catch (EException $e) {
            echo $e;
            return null;
        }
    }

    //
    // Member management
    //

    /**
     * Returns alliance members as an array of user objecs
     * Use $object->members from outside of the class
     */
    private function &getMembers()
    {
        if ($this->members == null) {
            $this->members = array();
            $res = dbquery("
                SELECT
                    user_id
                FROM
                    users
                WHERE
                    user_alliance_id=" . $this->id . "
                ");
            if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_row($res)) {
                    $this->members[$arr[0]] = new User($arr[0]);
                }
            }
        }
        return $this->members;
    }

    /**
     * Removes an user from the alliance
     */
    public function kickMember($userId, $kick = 1)
    {
        // TODO
        global $app;

        if (!$this->isAtWar()) {
            $res = dbquery("SELECT id FROM fleet WHERE user_id='" . $userId . "' AND (action='alliance' OR action='support') LIMIT 1;");
            if (mysql_num_rows($res) == 0) {
                $this->getMembers();
                if ($this->members[$userId]->isValid) {
                    $this->members[$userId]->alliance = null;
                    $this->members[$userId]->allianceLeave = time();
                    if ($this->members[$userId]->allianceId == 0) {
                        /** @var MessageRepository $messageRepository */
                        $messageRepository = $app[MessageRepository::class];
                        if ($kick == 1) {
                            $messageRepository->createSystemMessage($userId, MSG_ALLYMAIL_CAT, "Allianzausschluss", "Du wurdest aus der Allianz [b]" . $this->__toString() . "[/b] ausgeschlossen!");
                        } else {
                            $messageRepository->createSystemMessage($this->__get('founder')->id, MSG_ALLYMAIL_CAT, "Allianzaustritt", "Der Spieler " . $this->members[$userId] . " trat aus der Allianz aus!");
                        }

                        /** @var AllianceHistoryRepository $allianceHistoryRepository */
                        $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                        $allianceHistoryRepository->addEntry($this->id, "[b]" . $this->members[$userId] . "[/b] ist nun kein Mitglied mehr von uns.");

                        unset($this->members[$userId]);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    //
    // Wing management
    //

    /**
     * Returns all wings of this alliance as an array of alliance objects
     * Use $object->wings from outside of the class
     */
    private function &getWings()
    {
        if ($this->wings == null) {
            $this->wings = array();
            $res = dbquery("
              SELECT
                  alliance_id
              FROM
                  alliances
              WHERE
                  alliance_mother=" . $this->id . "
                  AND alliance_id!=" . $this->id . "
              ");
            if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_row($res)) {
                    $this->wings[$arr[0]] = new Alliance($arr[0]);
                }
            }
        }
        return $this->wings;
    }

    private function &getWingRequests()
    {
        if ($this->wingRequests == null) {
            $this->wingRequests = array();
            $res = dbquery("
              SELECT
                  alliance_id
              FROM
                  alliances
              WHERE
                  alliance_mother_request=" . $this->id . "
                  AND alliance_id!=" . $this->id . "
              ");
            if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_row($res)) {
                    $this->wingRequests[$arr[0]] = new Alliance($arr[0]);
                }
            }
        }
        return $this->wingRequests;
    }

    /**
     * Add a request for wing membership
     */
    public function addWingRequest($allianceId)
    {
        global $app;

        /** @var MessageRepository $messageRepository */
        $messageRepository = $app[MessageRepository::class];

        $this->getWingRequests();
        if ($allianceId != $this->id && $allianceId != $this->motherId &&  $allianceId != $this->motherRequestId) {
            $res = dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother_request=" . $this->id . "
                WHERE
                    alliance_mother_request=0
                    AND alliance_mother=0
                    AND alliance_id=" . $allianceId . "
                ");
            if (mysql_affected_rows() > 0) {
                $this->wingRequests[$allianceId] = new Alliance($allianceId);
                $messageRepository->createSystemMessage($this->wingRequests[$allianceId]->__get('founder')->id, MSG_ALLYMAIL_CAT, "Wing-Anfrage", "Die Allianz [b]" . $this->__toString() . "[/b] möchte eure Allianz als Wing hinzufügen. [page alliance action=wings]Anfrage beantworten[/page]");
                return true;
            }
        }
        return false;
    }

    /**
     * Cancel the request for wing membership
     */
    public function cancelWingRequest($wingId)
    {
        global $app;

        /** @var MessageRepository $messageRepository */
        $messageRepository = $app[MessageRepository::class];

        $this->getWingRequests();
        dbquery("
            UPDATE
                alliances
            SET
                alliance_mother_request=0
            WHERE
                alliance_mother_request=" . $this->id . "
                AND alliance_id=" . $wingId . "
            ");
        if (mysql_affected_rows() > 0) {
            if ($this->wingRequests != null) {
                $messageRepository->createSystemMessage($this->wingRequests[$wingId]->__get('founder')->id, MSG_ALLYMAIL_CAT, "Wing-Anfrage zurückgezogen", "Die Allianz [b]" . $this->__toString() . "[/b] hat die Wing-Anfrage zurückgezogen.");
                unset($this->wingRequests[$wingId]);
            } else {
                $tmpWing = new Alliance($wingId);
                $messageRepository->createSystemMessage($tmpWing->__get('founder')->id, MSG_ALLYMAIL_CAT, "Wing-Anfrage zurückgezogen", "Die Allianz [b]" . $this->__toString() . "[/b] hat die Wing-Anfrage zurückgezogen.");
                unset($tmpWing);
            }
            return true;
        }
        return false;
    }

    /**
     * Revoke request for wing membership
     */
    public function revokeWingRequest()
    {
        global $app;

        /** @var MessageRepository $messageRepository */
        $messageRepository = $app[MessageRepository::class];

        if ($this->motherRequestId > 0) {
            dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother_request=0
                WHERE
                    alliance_mother_request=" . $this->motherRequestId . "
                    AND alliance_id=" . $this->id . "
                ");
            if (mysql_affected_rows() > 0) {
                $messageRepository->createSystemMessage($this->__get('motherRequest')->__get('founder')->id ,MSG_ALLYMAIL_CAT, "Wing-Anfrage zurückgewiesen", "Die Allianz [b]" . $this->__toString() . "[/b] hat die Wing-Anfrage zurückgewiesen.");
                $this->motherRequestId = 0;
                $this->motherRequest = null;
                return true;
            }
        }
        return false;
    }

    /**
     * Grant request for wing membership
     */
    public function grantWingRequest()
    {
        // TODO
        global $app;

        if ($this->motherRequestId > 0) {
            $res = dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother=" . $this->motherRequestId . ",
                    alliance_mother_request=0
                WHERE
                    alliance_id=" . $this->id . "
                ");
            if (mysql_affected_rows() > 0) {
                $this->mother = $this->motherRequest;
                $this->motherId = $this->motherRequestId;
                $this->motherRequestId = 0;
                $this->motherRequest = null;

                /** @var AllianceHistoryRepository $allianceHistoryRepository */
                $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                $allianceHistoryRepository->addEntry($this->__get('mother')->id, "[b]" . $this->__toString() . "[/b] wurde als neuer Wing hinzugefügt.");
                $allianceHistoryRepository->addEntry($this->id, "Wir sind nun ein Wing von [b]" . $this->__get('mother') . "[/b]");

                /** @var MessageRepository $messageRepository */
                $messageRepository = $app[MessageRepository::class];
                $messageRepository->createSystemMessage($this->__get('mother')->__get('founder')->id, MSG_ALLYMAIL_CAT, "Neuer Wing", "Die Allianz [b]" . $this->__toString() . "[/b] ist nun ein Wing von [b]" . $this->__get('mother') . "[/b]");
                return true;
            }
        }
        return false;
    }

    /**
     * Add a wing
     */
    public function addWing($allianceId)
    {
        // TODO
        global $app;

        $this->getWings();
        if ($allianceId != $this->id && !isset($this->wings[$allianceId])) {
            $res = dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother=" . $this->id . ",
                    alliance_mother_request=0
                WHERE
                    alliance_id=" . $allianceId . "
                ");
            if (mysql_affected_rows() > 0) {
                $this->wings[$allianceId] = new Alliance($allianceId);

                /** @var AllianceHistoryRepository $allianceHistoryRepository */
                $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                $allianceHistoryRepository->addEntry($this->id, $this->wings[$allianceId] . " wurde als neuer Wing hinzugefügt.");
                $allianceHistoryRepository->addEntry($this->wings[$allianceId]->id, "Wir sing nun ein Wing von " . $this->__toString());

                /** @var MessageRepository $messageRepository */
                $messageRepository = $app[MessageRepository::class];
                $messageRepository->createSystemMessage($this->__get('founder')->id, MSG_ALLYMAIL_CAT, "Neuer Wing", "Die Allianz [b]" . $this->wings[$allianceId] . "[/b] ist nun ein Wing von [b]" . $this->__toString() . "[/b]");
                $messageRepository->createSystemMessage($this->wings[$allianceId]->__get('founder')->id, MSG_ALLYMAIL_CAT, "Neuer Wing", "Die Allianz [b]" . $this->wings[$allianceId] . "[/b] ist nun ein Wing von [b]" . $this->__toString() . "[/b]");
                return true;
            }
        }
        return false;
    }

    /**
     * Removes a wing
     */
    public function removeWing($wingId)
    {
        // TODO
        global $app;

        dbquery("
            UPDATE
                alliances
            SET
                alliance_mother=0
            WHERE
                alliance_mother=" . $this->id . "
                AND alliance_id=" . $wingId . "
            ");
        if (mysql_affected_rows() > 0) {
            /** @var AllianceHistoryRepository $allianceHistoryRepository */
            $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
            if ($this->wings != null) {
                $allianceHistoryRepository->addEntry($this->id, $this->wings[$wingId] . " ist nun kein Wing mehr von uns");
                $allianceHistoryRepository->addEntry($this->wings[$wingId]->id, "Wir sind nun kein Wing mehr von [b]" . $this->__toString() . "[/b]");

                /** @var MessageRepository $messageRepository */
                $messageRepository = $app[MessageRepository::class];
                $messageRepository->createSystemMessage($this->__get('founder')->id, MSG_ALLYMAIL_CAT, "Wing aufgelöst", "Die Allianz [b]" . $this->wings[$wingId] . "[/b] ist kein Wing mehr von [b]" . $this->__toString() . "[/b]");
                $messageRepository->createSystemMessage($this->wings[$wingId]->__get('founder')->id, MSG_ALLYMAIL_CAT, "Wing aufgelöst", "Die Allianz [b]" . $this->wings[$wingId] . "[/b] ist kein Wing mehr von [b]" . $this->__toString() . "[/b]");
                unset($this->wings[$wingId]);
            } else {
                $tmpWing = new Alliance($wingId);
                $allianceHistoryRepository->addEntry($this->id, $tmpWing . " ist nun kein Wing mehr.");
                $allianceHistoryRepository->addEntry($tmpWing->id, "Wir sind nun kein Wing mehr von [b]" . $this->__toString() . "[/b]");
                unset($tmpWing);
            }
            return true;
        }
        return false;
    }

    /**
     * Check rights for an action
     * @param AllianceRights::* $action
     */
    static function checkActionRights(string $action, $msg = TRUE): bool
    {
        global $myRight, $isFounder, $page;
        if ($isFounder || $myRight[$action]) {
            return true;
        }

        if ($msg) {
            error_msg("Keine Berechtigung!");
            echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
        }
        return false;
    }

    /**
     * Check rights for an action
     * use this function if you're not on the alliance page
     *
     * @param AllianceRights::* $action
     */
    function checkActionRightsNA(string $action): bool
    {
        global $cu, $app;

        if ($this->founderId == $cu->id) return true;

        /** @var AllianceRankRepository $allianceRankRepository */
        $allianceRankRepository = $app[AllianceRankRepository::class];

        return $allianceRankRepository->hasActionRights($this->id, $cu->allianceRankId, $action);
    }

    public function isAtWar()
    {
        global $app;
        /** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
        $allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

        return $allianceDiplomacyRepository->isAtWar($this->id);
    }

    static function allianceShipPointsUpdate()
    {
        // TODO
        global $app;

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        $pres = dbquery("SELECT
                            alliance_buildlist_alliance_id,
                            alliance_buildlist_current_level,
                            alliance_res_metal,
                            alliance_res_crystal,
                            alliance_res_plastic,
                            alliance_res_fuel,
                            alliance_res_food
                        FROM
                            alliance_buildlist
                        INNER JOIN
                            alliances
                        ON
                            alliance_id=alliance_buildlist_alliance_id
                            AND alliance_buildlist_building_id='3'
                        ");
        while ($parr = mysql_fetch_row($pres)) {
            if (!($parr[2] < 0 || $parr[3] < 0 || $parr[4] < 0 || $parr[5] < 0 || $parr[6] < 0)) {
                // New exponential algorithm by river
                // NOTE: if changed, also change in content/alliance/base.inc.php
                $shipPointsAdd = ceil($config->getInt("alliance_shippoints_per_hour") * pow($config->getFloat('alliance_shippoints_base'), ($parr[1] - 1)));

                dbquery("UPDATE
                            users
                        SET
                            user_alliace_shippoints=user_alliace_shippoints + '" . $shipPointsAdd . "'
                        WHERE
                             user_alliance_id='" . $parr[0] . "';");
            }
        }
    }
}
