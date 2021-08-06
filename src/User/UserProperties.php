<?php

declare(strict_types=1);

namespace EtoA\User;

class UserProperties
{
    public ?string $cssStyle;
    public ?string $imageUrl;
    public ?string $imageExt;
    public int $planetCircleWidth;
    public string $itemShow;
    public string $itemOrderShip;
    public string $itemOrderDef;
    public string $itemOrderBookmark;
    public string $itemOrderWay;
    public bool $imageFilter;
    public ?string $msgSignature;
    public bool $msgCreationPreview;
    public bool $msgPreview;
    public bool $helpBox;
    public bool $noteBox;
    public bool $msgCopy;
    public bool $msgBlink;
    public int $spyShipId;
    public int $spyShipCount;
    public int $analyzeShipId;
    public int $analyzeShipCount;
    public int $exploreShipId;
    public int $exploreShipCount;
    public bool $showCellreports;
    public bool $havenShipsButtons;
    public bool $showAdds;
    public bool $fleetRtnMsg;
    public bool $smallResBox;
    public bool $startUpChat;
    public string $chatColor;
    public bool $enableKeybinds;

    public function __construct(array $arr)
    {
        $this->cssStyle = filled($arr['css_style']) ? $arr['css_style'] : null;
        $this->imageUrl = filled($arr['image_url']) ? $arr['image_url'] : null;
        $this->imageExt = filled($arr['image_ext']) ? $arr['image_ext'] : null;
        $this->planetCircleWidth = (int) $arr['planet_circle_width'];
        $this->itemShow = $arr['item_show'];
        $this->itemOrderShip = $arr['item_order_ship'];
        $this->itemOrderDef = $arr['item_order_def'];
        $this->itemOrderBookmark = $arr['item_order_bookmark'];
        $this->itemOrderWay = $arr['item_order_way'];
        $this->imageFilter = (bool) $arr['image_filter'];
        $this->msgSignature = $arr['msgsignature'];
        $this->msgCreationPreview = (bool) $arr['msgcreation_preview'];
        $this->msgPreview = (bool) $arr['msg_preview'];
        $this->helpBox = (bool) $arr['helpbox'];
        $this->noteBox = (bool) $arr['notebox'];
        $this->msgCopy = (bool) $arr['msg_copy'];
        $this->msgBlink = (bool) $arr['msg_blink'];
        $this->spyShipId = (int) $arr['spyship_id'];
        $this->spyShipCount = (int) $arr['spyship_count'];
        $this->analyzeShipId = (int) $arr['analyzeship_id'];
        $this->analyzeShipCount = (int) $arr['analyzeship_count'];
        $this->exploreShipId = (int) $arr['exploreship_id'];
        $this->exploreShipCount = (int) $arr['exploreship_count'];
        $this->showCellreports = (bool) $arr['show_cellreports'];
        $this->havenShipsButtons = (bool) $arr['havenships_buttons'];
        $this->showAdds = (bool) $arr['show_adds'];
        $this->fleetRtnMsg = (bool) $arr['fleet_rtn_msg'];
        $this->smallResBox = (bool) $arr['small_res_box'];
        $this->startUpChat = (bool) $arr['startup_chat'];
        $this->chatColor = $arr['chat_color'];
        $this->enableKeybinds = (bool) $arr['keybinds_enable'];
    }
}
