<?php

use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Core\Configuration\ConfigurationService;

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
        protected $buildlist = null;
        protected $techlist = null;

        private ConfigurationService $config;

        /**
        * Constructor
        */
        public function __construct($id)
        {
            // TODO
            global $app;

            $this->config = $app['etoa.config.service'];

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
              alliance_id=".$this->id."
          GROUP BY
              alliance_id
          LIMIT 1;
          ");
          if (mysql_num_rows($res)>0)
          {
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
            return "[".$this->tag."] ".$this->name;
        }

        /**
        * Destruktor
        */
        function __destruct()
        {
            $cnt = count($this->changedFields);
            if ($cnt > 0)
            {
                $sql = "UPDATE
                    alliances
                SET ";
                foreach ($this->changedFields as $k=>$v)
                {
                    if ($k == "visits")
                        $sql.= " alliance_visits=".$this->$k.",";
                    elseif ($k == "visitsExt")
                        $sql.= " alliance_visits_ext=".$this->$k.",";
                    elseif ($k == "founderId")
                        $sql.= " alliance_founder_id=".$this->$k.",";
                    else
                        echo " $k has no valid UPDATE query!<br/>";
                }
                $sql.=" alliance_id=alliance_id WHERE
                        alliance_id=".$this->id.";";
                dbquery($sql);
            }
            unset($this->changedFields);

        }

        /**
        * Chances alliance properties
        */
        public function __set($key, $val)
        {
            try
            {
                if (!property_exists($this,$key))
                    throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);

                if ($key=="visits")
                {
                    $this->$key = intval($val);
                    $this->changedFields[$key] = true;
                    return true;
                }
                if ($key=="visitsExt")
                {
                    $this->$key = intval($val);
                    $this->changedFields[$key] = true;
                    return true;
                }
                if ($key=="founderId")
                {
                    if ($this->members == null)
                        $this->getMembers();
                    if (isset($this->members[$val]))
                    {
                        $this->$key = intval($val);
                        $this->founder = & $this->members[$val];

                        // TODO
                        global $app;

                        /** @var AllianceHistoryRepository */
                        $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                        $allianceHistoryRepository->addEntry($this->id, "Der Spieler [b]".$this->founder."[/b] wird zum Gründer befördert.");

                        $this->founder->sendMessage(MSG_ALLYMAIL_CAT,"Gründer","Du hast nun die Gründerrechte deiner Allianz!");
                        $this->founder->addToUserLog("alliance","{nick} ist nun Gründer der Allianz ".$this->__toString());
                        $this->changedFields[$key] = true;
                        return true;
                    }
                    return false;
                }

                throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
            }
            catch (EException $e)
            {
                echo $e;
            }
        }

        /**
        * Gets alliance properties
        */
        public function __get($key)
        {
            try
            {
                // Return special non-defined properties
                if ($key == "avgPoints")
                    return floor($this->points / $this->memberCount);
                if ($key == "imageUrl")
                    return ALLIANCE_IMG_DIR."/".$this->image;

                // Check if property exists
                if (!property_exists($this,$key))
                    throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);

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
                if ($key == "founder" && $this->founder == null)
                {
                    if (isset($this->members[$this->founderId]))
                        $this->founder = & $this->members[$this->founderId];
                    else
                        $this->founder = new User($this->founderId);
                }
                if ($key == "techlist" && $this->techlist == null)
                    $this->techlist = new AllianceTechlist($this->id,TRUE);
                if ($key == "buildlist" && $this->buildlist == null)
                    $this->buildlist = new AllianceBuildList($this->id,TRUE);


                // Protected properties
                if ($key == "changedFields")
                    throw new EException("Property $key der Klasse ".__CLASS__." ist geschützt!");


                return $this->$key;
            }
            catch (EException $e)
            {
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
        private function & getMembers()
        {
            if ($this->members == null)
            {
                $this->members = array();
                $res = dbquery("
                SELECT
                    user_id
                FROM
                    users
                WHERE
                    user_alliance_id=".$this->id."
                ");
                if (mysql_num_rows($res)>0)
                {
                    while ($arr = mysql_fetch_row($res))
                    {
                        $this->members[$arr[0]] = new User($arr[0]);
                    }
                }
            }
            return $this->members;
        }

        //
        // Wing management
        //

        /**
        * Returns all wings of this alliance as an array of alliance objects
        * Use $object->wings from outside of the class
        */
        private function & getWings()
        {
            if ($this->wings == null)
            {
                $this->wings = array();
              $res = dbquery("
              SELECT
                  alliance_id
              FROM
                  alliances
              WHERE
                  alliance_mother=".$this->id."
                  AND alliance_id!=".$this->id."
              ");
              if (mysql_num_rows($res)>0)
              {
                  while ($arr = mysql_fetch_row($res))
                  {
                      $this->wings[$arr[0]] = new Alliance($arr[0]);
                  }
              }
            }
            return $this->wings;
        }

        private function & getWingRequests()
        {
            if ($this->wingRequests == null)
            {
                $this->wingRequests = array();
              $res = dbquery("
              SELECT
                  alliance_id
              FROM
                  alliances
              WHERE
                  alliance_mother_request=".$this->id."
                  AND alliance_id!=".$this->id."
              ");
              if (mysql_num_rows($res)>0)
              {
                  while ($arr = mysql_fetch_row($res))
                  {
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
            $this->getWingRequests();
            if ($allianceId != $this->id && $allianceId!= $this->motherId &&  $allianceId!= $this->motherRequestId)
            {
                $res = dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother_request=".$this->id."
                WHERE
                    alliance_mother_request=0
                    AND alliance_mother=0
                    AND alliance_id=".$allianceId."
                ");
                if (mysql_affected_rows()>0)
                {
                    $this->wingRequests[$allianceId] = new Alliance($allianceId);
                    $this->wingRequests[$allianceId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage","Die Allianz [b]".$this->__toString()."[/b] möchte eure Allianz als Wing hinzufügen. [page alliance action=wings]Anfrage beantworten[/page]");
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
            $this->getWingRequests();
            dbquery("
            UPDATE
                alliances
            SET
                alliance_mother_request=0
            WHERE
                alliance_mother_request=".$this->id."
                AND alliance_id=".$wingId."
            ");
            if (mysql_affected_rows()>0)
            {
                if ($this->wingRequests != null)
                {
                    $this->wingRequests[$wingId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgezogen","Die Allianz [b]".$this->__toString()."[/b] hat die Wing-Anfrage zurückgezogen.");
                    unset($this->wingRequests[$wingId]);
                }
                else
                {
                    $tmpWing = new Alliance($wingId);
                    $tmpWing->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgezogen","Die Allianz [b]".$this->__toString()."[/b] hat die Wing-Anfrage zurückgezogen.");
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
            if ($this->motherRequestId > 0)
            {
                dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother_request=0
                WHERE
                    alliance_mother_request=".$this->motherRequestId."
                    AND alliance_id=".$this->id."
                ");
                if (mysql_affected_rows()>0)
                {
                    $this->__get('motherRequest')->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgewiesen","Die Allianz [b]".$this->__toString()."[/b] hat die Wing-Anfrage zurückgewiesen.");
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

            if ($this->motherRequestId > 0)
            {
                $res = dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother=".$this->motherRequestId.",
                    alliance_mother_request=0
                WHERE
                    alliance_id=".$this->id."
                ");
                if (mysql_affected_rows()>0)
                {
                    $this->mother = $this->motherRequest;
                    $this->motherId = $this->motherRequestId;
                    $this->motherRequestId = 0;
                    $this->motherRequest = null;

                    /** @var AllianceHistoryRepository */
                    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry($this->__get('mother')->id, "[b]".$this->__toString()."[/b] wurde als neuer Wing hinzugefügt.");
                    $allianceHistoryRepository->addEntry($this->id, "Wir sind nun ein Wing von [b]".$this->__get('mother')."[/b]");

                    $this->__get('mother')->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Neuer Wing","Die Allianz [b]".$this->__toString()."[/b] ist nun ein Wing von [b]".$this->__get('mother')."[/b]");
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
            if ($allianceId != $this->id && !isset($this->wings[$allianceId]))
            {
                $res = dbquery("
                UPDATE
                    alliances
                SET
                    alliance_mother=".$this->id.",
                    alliance_mother_request=0
                WHERE
                    alliance_id=".$allianceId."
                ");
                if (mysql_affected_rows()>0)
                {
                    $this->wings[$allianceId] = new Alliance($allianceId);

                    /** @var AllianceHistoryRepository */
                    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry($this->id, $this->wings[$allianceId]." wurde als neuer Wing hinzugefügt.");
                    $allianceHistoryRepository->addEntry($this->wings[$allianceId]->id, "Wir sing nun ein Wing von ".$this->__toString());
                    $this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Neuer Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist nun ein Wing von [b]".$this->__toString()."[/b]");
                    $this->wings[$allianceId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Neuer Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist nun ein Wing von [b]".$this->__toString()."[/b]");
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
                alliance_mother=".$this->id."
                AND alliance_id=".$wingId."
            ");
            if (mysql_affected_rows()>0)
            {
                /** @var AllianceHistoryRepository */
                $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                if ($this->wings != null)
                {
                    $allianceHistoryRepository->addEntry($this->id, $this->wings[$wingId]." ist nun kein Wing mehr von uns");
                    $allianceHistoryRepository->addEntry($this->wings[$wingId]->id, "Wir sind nun kein Wing mehr von [b]".$this->__toString()."[/b]");
                    $this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing aufgelöst","Die Allianz [b]".$this->wings[$wingId]."[/b] ist kein Wing mehr von [b]".$this->__toString()."[/b]");
                    $this->wings[$wingId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing aufgelöst","Die Allianz [b]".$this->wings[$wingId]."[/b] ist kein Wing mehr von [b]".$this->__toString()."[/b]");
                    unset($this->wings[$wingId]);
                }
                else
                {
                    $tmpWing = new Alliance($wingId);
                    $allianceHistoryRepository->addEntry($this->id, $tmpWing." ist nun kein Wing mehr.");
                    $allianceHistoryRepository->addEntry($tmpWing->id, "Wir sind nun kein Wing mehr von [b]".$this->__toString()."[/b]");
                    unset($tmpWing);
                }
                return true;
            }
            return false;
        }

        /**
        * Changes allianceresources
        */
        function changeRes($m,$c,$p,$fu,$fo,$pw=0)
        {
            $sql = "
            UPDATE
                alliances
            SET
                alliance_res_metal=alliance_res_metal+".$m.",
                alliance_res_crystal=alliance_res_crystal+".$c.",
                alliance_res_plastic=alliance_res_plastic+".$p.",
                alliance_res_fuel=alliance_res_fuel+".$fu.",
                alliance_res_food=alliance_res_food+".$fo."
            WHERE
                alliance_id='".$this->id."';";
            dbquery($sql);
            $this->resMetal+=$m;
            $this->resCrystal+=$c;
            $this->resPlastic+=$p;
            $this->resFuel+=$fu;
            $this->resFood+=$fo;
        }

        //
        // Statics
        //

        /**
        * Check rights for an action
        */
      static function checkActionRights($action,$msg=TRUE)
      {
            global $myRight,$isFounder,$page;
            if ($isFounder || $myRight[$action])
            {
                return true;
            }

            if ($msg)
            {
                error_msg("Keine Berechtigung!");
                echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
            }
            return false;
      }

      /**
      * Check rights for an action
      * use this function if you're not on the alliance page
      */

      function checkActionRightsNA($action)
      {
          global $cu;

          if ($this->founderId == $cu->id) return true;

          $res = dbquery("
                        SELECT
                            alliance_rankrights.rr_id
                        FROM
                            alliance_ranks
                        INNER JOIN
                            alliance_rankrights
                        ON
                            alliance_ranks.rank_id=alliance_rankrights.rr_rank_id
                        INNER JOIN
                            alliance_rights
                        ON
                            alliance_rankrights.rr_right_id=alliance_rights.right_id
                            AND alliance_ranks.rank_alliance_id='".$this->id."'
                            AND alliance_rights.right_key='".$action."'
                            AND alliance_rankrights.rr_rank_id=".$cu->allianceRankId.";");
            if (mysql_num_rows($res)) return true;

            return false;
      }

      /**
      * Returns a sorted list of all alliances
      */
      static public function & getList()
      {
          $rtn = array();
          $res = dbquery("
          SELECT
              alliance_id,
              alliance_tag,
              alliance_name
          FROM
              alliances
            ORDER BY
                alliance_name
          ");
          if (mysql_num_rows($res)>0)
          {
              while ($arr = mysql_fetch_row($res))
                  $rtn[$arr[0]] = "[".$arr[1]."] ".$arr[2];
          }
          return $rtn;
      }

        public function isAtWar() {
            $sql = "SELECT alliance_bnd_id FROM alliance_bnd WHERE alliance_bnd_level=3 && (alliance_bnd_alliance_id1=".$this->id." OR alliance_bnd_alliance_id2=".$this->id.") LIMIT 1;";
            $res = dbquery($sql);
            if (mysql_num_rows($res) > 0) {
                  return true;
            }
            return false;
        }

      /**
      * Warcheck
      */

      public function checkWar($allianceId)
      {
        if ($this->id!=$allianceId && $allianceId>0)
        {
          $wres=dbquery("
              SELECT
                  COUNT(alliance_bnd_id)
              FROM
                  alliance_bnd
              WHERE
                  (
                      (
                          alliance_bnd_alliance_id1=".$this->id."
                          AND alliance_bnd_alliance_id2=".$allianceId."
                      )
                      OR
                      (
                          alliance_bnd_alliance_id2=".$this->id."
                          AND alliance_bnd_alliance_id1=".$allianceId."
                      )
                  )
                  AND alliance_bnd_level=3");
          $warr=mysql_fetch_row($wres);
          if ($warr[0]>0) return true;
        }
        return false;
      }

      /**
      * Bndcheck
      */

      public function checkBnd($allianceId)
      {
        if ($this->id!=$allianceId && $allianceId>0)
        {
          $bres=dbquery("
              SELECT
                  COUNT(alliance_bnd_id)
              FROM
                  alliance_bnd
              WHERE
                  (
                      (
                          alliance_bnd_alliance_id1=".$this->id."
                          AND alliance_bnd_alliance_id2=".$allianceId."
                      )
                      OR
                      (
                          alliance_bnd_alliance_id2=".$this->id."
                          AND alliance_bnd_alliance_id1=".$allianceId."
                      )
                  )
                  AND alliance_bnd_level=2");
          $barr=mysql_fetch_row($bres);
          if ($barr[0]>0) return true;
        }
        return false;
      }

    static function allianceShipPointsUpdate() {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app['etoa.config.service'];

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
            if (!($parr[2]<0 || $parr[3]<0 || $parr[4]<0 || $parr[5]<0 || $parr[6]<0)) {
                // New exponential algorithm by river
                // NOTE: if changed, also change in content/alliance/base.inc.php
                $shipPointsAdd = ceil($config->getInt("alliance_shippoints_per_hour")*pow($config->getFloat('alliance_shippoints_base'),($parr[1]-1)));

                dbquery("UPDATE
                            users
                        SET
                            user_alliace_shippoints=user_alliace_shippoints + '".$shipPointsAdd."'
                        WHERE
                             user_alliance_id='".$parr[0]."';");
            }
        }
    }

    /**
    * Returns the number of members of the given alliance
    */
    static public function countMembers($allianceId) {
        $narr = mysql_fetch_row(dbquery("
        SELECT
            COUNT(user_id) as member_count
        FROM
            users
        WHERE
            user_alliance_id=".$allianceId."
        ;"));
        return $narr[0];
    }
}
?>
