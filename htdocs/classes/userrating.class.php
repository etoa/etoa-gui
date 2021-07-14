<?PHP
class UserRating
{
    private $id;

    private $battle = 0;
    private $trade = 0;
    private $diplomacy = 0;
    private $battlesFought = 0;
    private $battlesWon = 0;
    private $battlesLost = 0;
    private $tradesSell = 0;
    private $tradesBuy = 0;

    public function __construct($id)
    {
        $this->id = $id;
        $res = dbquery("
            SELECT
                *
            FROM
                user_ratings
            WHERE
                id=" . $this->id . "
            LIMIT 1;");
        if (mysql_num_rows($res) > 0) {
            $arr = mysql_fetch_assoc($res);
            $this->battle = $arr['battle_rating'];
            $this->trade = $arr['trade_rating'];
            $this->diplomacy = $arr['diplomacy_rating'];
            $this->battlesFought = $arr['battles_fought'];
            $this->battlesWon = $arr['battles_won'];
            $this->battlesLost = $arr['battles_lost'];
            $this->tradesSell = $arr['trades_sell'];
            $this->tradesBuy = $arr['trades_buy'];
        } else {
            dbquery("
                INSERT INTO
                    user_ratings
                (id)
                VALUES
                (" . $this->id . ")
                ");
        }
    }

    public function __set($key, $val)
    {
        try {
            if (!property_exists($this, $key)) {
                throw new EException("Property $key existiert nicht in der Klasse " . __CLASS__);
            }

            throw new EException("Property $key der Klasse " . __CLASS__ . " ist nicht änderbar!");
        } catch (EException $e) {
            echo $e;
        }
    }

    public function __get($key)
    {
        try {
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in " . __CLASS__);

            return $this->$key;
        } catch (EException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Add battle rating
     */
    function addBattleRating($rating, $reason = "")
    {
        if ($rating != 0) {
            dbquery("
                UPDATE
                    user_ratings
                SET
                    battle_rating=battle_rating+" . $rating . "
                WHERE
                    id=" . $this->id . ";");
            if ($reason != "")
                Log::add(17, Log::INFO, "KP: Der Spieler " . $this->id . " erhält " . $rating . " Kampfpunkte. Grund: " . $reason);
        }
    }

    /**
     * Add trade rating
     */
    function addTradeRating($rating, $sell = true, $reason = "")
    {
        $sell = $sell ? ',trades_sell=trades_sell+1' : ',trades_buy=trades_buy+1';
        dbquery("
            UPDATE
                user_ratings
            SET
                trade_rating=trade_rating+" . $rating . "
                " . $sell . "
            WHERE
                id=" . $this->id . ";");
        if ($reason != "")
            Log::add(17, Log::INFO, "HP: Der Spieler " . $this->id . " erhält " . $rating . " Handelspunkte. Grund: " . $reason);
    }

    /**
     * Add diplomacy rating
     */
    function addDiplomacyRating($rating, $reason = "")
    {
        dbquery("
            UPDATE
                user_ratings
            SET
                      diplomacy_rating=diplomacy_rating+" . intval($rating) . "
            WHERE
                id=" . $this->id . ";");
        if ($reason != "")
            Log::add(17, Log::INFO, "DP: Der Spieler " . $this->id . " erhält " . $rating . " Diplomatiepunke. Grund: " . $reason);
    }
}
