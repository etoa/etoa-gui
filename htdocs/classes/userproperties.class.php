<?PHP
class UserProperties
{
    private $id;

    private $cssStyle;
    private $imageUrl;
    private $imageExt;
    private $planetCircleWidth;
    private $itemShow;
    private $itemOrderShip;
    private $itemOrderDef;
    private $itemOrderBookmark;
    private $itemOrderWay;
    private $imageFilter;
    private $msgSignature;
    private $msgCreationPreview;
    private $msgPreview;
    private $helpBox;
    private $noteBox;
    private $msgCopy;
    private $msgBlink;
    private $spyShipId;
    private $spyShipCount;
    private $analyzeShipId;
    private $analyzeShipCount;
    private $exploreShipId;
    private $exploreShipCount;
    private $showCellreports;
    private $havenShipsButtons;
    private $showAdds;
    private $fleetRtnMsg;
    private $smallResBox;
    private $startUpChat;
    private $chatColor;
    private $enableKeybinds;

    private $changedFields;

    public function __construct($id)
    {
        $this->id = $id;
        $this->changedFields = array();

        $res = dbQuerySave("
			SELECT
				*
			FROM
				user_properties
			WHERE
				id=?
			LIMIT 1;", [$this->id]);
        if (mysql_num_rows($res) > 0) {
            $arr = mysql_fetch_assoc($res);

            $this->cssStyle = $arr['css_style'];
            $this->imageUrl = $arr['image_url'];
            $this->imageExt = $arr['image_ext'];
            $this->planetCircleWidth = $arr['planet_circle_width'];
            $this->itemShow = $arr['item_show'];
            $this->itemOrderShip = $arr['item_order_ship'];
            $this->itemOrderDef = $arr['item_order_def'];
            $this->itemOrderBookmark = $arr['item_order_bookmark'];
            $this->itemOrderWay = $arr['item_order_way'];
            $this->imageFilter = $arr['image_filter'];
            $this->msgSignature = $arr['msgsignature'];
            $this->msgCreationPreview = $arr['msgcreation_preview'];
            $this->msgPreview = $arr['msg_preview'];
            $this->helpBox = $arr['helpbox'];
            $this->noteBox = $arr['notebox'];
            $this->msgCopy = $arr['msg_copy'];
            $this->msgBlink = $arr['msg_blink'];
            $this->spyShipId = $arr['spyship_id'];
            $this->spyShipCount = $arr['spyship_count'];
            $this->analyzeShipId = $arr['analyzeship_id'];
            $this->analyzeShipCount = $arr['analyzeship_count'];
            $this->exploreShipId = $arr['exploreship_id'];
            $this->exploreShipCount = $arr['exploreship_count'];
            $this->showCellreports = $arr['show_cellreports'];
            $this->havenShipsButtons = $arr['havenships_buttons'];
            $this->showAdds = $arr['show_adds'];
            $this->fleetRtnMsg = $arr['fleet_rtn_msg'];
            $this->smallResBox = $arr['small_res_box'];
            $this->startUpChat = $arr['startup_chat'];
            $this->chatColor = $arr['chat_color'];
            $this->enableKeybinds = $arr['keybinds_enable'];
        } else {
            dbquery("
				INSERT INTO
					user_properties
				(id)
				VALUES
				(" . $this->id . ")
				");
        }
    }

    function __destruct()
    {
        $cnt = count($this->changedFields);
        if ($cnt > 0) {
            $i = 0;
            $sql = "UPDATE
					`user_properties`
				SET ";
            foreach ($this->changedFields as $cf => $df) {
                $sql .= " `" . $df . "`='" . $this->$cf . "'";
                $i++;
                if ($i < $cnt)
                    $sql .= ",";
            }
            $sql .= " WHERE
				    	id=" . $this->id . ";";
            dbquery($sql);
        }
        unset($this->changedFields);
    }


    public function __set($key, $val)
    {
        try {
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in der Klasse " . __CLASS__);

            if ($key == "cssStyle") {
                $this->$key = $val;
                $this->changedFields[$key] = "css_style";
            } elseif ($key == "startUpChat") {
                $this->$key = $val;
                $this->changedFields[$key] = "startup_chat";
            } elseif ($key == "smallResBox") {
                $this->$key = $val;
                $this->changedFields[$key] = "small_res_box";
            } elseif ($key == "imageUrl") {
                $this->$key = $val;
                $this->changedFields[$key] = "image_url";
            } elseif ($key == "imageExt") {
                $this->$key = $val;
                $this->changedFields[$key] = "image_ext";
            } elseif ($key == "planetCircleWidth") {
                $this->$key = $val;
                $this->changedFields[$key] = "planet_circle_width";
            } elseif ($key == "itemShow") {
                $this->$key = $val;
                $this->changedFields[$key] = "item_show";
            } elseif ($key == "itemOrderShip") {
                $this->$key = $val;
                $this->changedFields[$key] = "item_order_ship";
            } elseif ($key == "itemOrderDef") {
                $this->$key = $val;
                $this->changedFields[$key] = "item_order_def";
            } elseif ($key == "itemOrderBookmark") {
                $this->$key = $val;
                $this->changedFields[$key] = "item_order_bookmark";
            } elseif ($key == "itemOrderWay") {
                $this->$key = $val;
                $this->changedFields[$key] = "item_order_way";
            } elseif ($key == "imageFilter") {
                $this->$key = $val;
                $this->changedFields[$key] = "image_filter";
            } elseif ($key == "msgSignature") {
                $this->$key = $val;
                $this->changedFields[$key] = "msgsignature";
            } elseif ($key == "msgCreationPreview") {
                $this->$key = $val;
                $this->changedFields[$key] = "msgcreation_preview";
            } elseif ($key == "msgPreview") {
                $this->$key = $val;
                $this->changedFields[$key] = "msg_preview";
            } elseif ($key == "helpBox") {
                $this->$key = $val;
                $this->changedFields[$key] = "helpbox";
            } elseif ($key == "noteBox") {
                $this->$key = $val;
                $this->changedFields[$key] = "notebox";
            } elseif ($key == "msgCopy") {
                $this->$key = $val;
                $this->changedFields[$key] = "msg_copy";
            } elseif ($key == "msgBlink") {
                $this->$key = $val;
                $this->changedFields[$key] = "msg_blink";
            } elseif ($key == "spyShipId") {
                $this->$key = $val;
                $this->changedFields[$key] = "spyship_id";
            } elseif ($key == "spyShipCount") {
                $this->$key = max(1, intval($val));
                $this->changedFields[$key] = "spyship_count";
            } elseif ($key == "analyzeShipId") {
                $this->$key = $val;
                $this->changedFields[$key] = "analyzeship_id";
            } elseif ($key == "analyzeShipCount") {
                $this->$key = max(1, intval($val));
                $this->changedFields[$key] = "analyzeship_count";
            } elseif ($key == "exploreShipId") {
                $this->$key = $val;
                $this->changedFields[$key] = "exploreship_id";
            } elseif ($key == "exploreShipCount") {
                $this->$key = max(1, intval($val));
                $this->changedFields[$key] = "exploreship_count";
            } elseif ($key == "showCellreports") {
                $this->$key = $val;
                $this->changedFields[$key] = "show_cellreports";
            } elseif ($key == "havenShipsButtons") {
                $this->$key = $val;
                $this->changedFields[$key] = "havenships_buttons";
            } elseif ($key == "showAdds") {
                $this->$key = $val;
                $this->changedFields[$key] = "show_adds";
            } elseif ($key == "fleetRtnMsg") {
                $this->$key = $val;
                $this->changedFields[$key] = "fleet_rtn_msg";
            } elseif ($key == "chatColor") {
                $this->$key = $val;
                $this->changedFields[$key] = "chat_color";
            } elseif ($key == "enableKeybinds") {
                $this->$key = $val;
                $this->changedFields[$key] = "keybinds_enable";
            } else {
                throw new EException("Property $key hat keine UPDATE-Instruktion in der Klasse " . __CLASS__);
            }
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
}
