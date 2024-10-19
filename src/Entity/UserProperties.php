<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserPropertiesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPropertiesRepository::class)]
class UserProperties
{
    #[ORM\Column(type: "string")]
    private ?string $cssStyle;

    #[ORM\Column(type: "int")]
    private int $planetCircleWidth;

    #[ORM\Column(type: "string")]
    private string $itemShow;

    #[ORM\Column(type: "string")]
    private string $itemOrderShip;

    #[ORM\Column(type: "string")]
    private string $itemOrderDef;

    #[ORM\Column(type: "string")]
    private string $itemOrderBookmark;

    #[ORM\Column(type: "string")]
    private string $itemOrderWay;

    #[ORM\Column(type: "boolean")]
    private bool $imageFilter;

    #[ORM\Column(type: "string")]
    private ?string $msgSignature;

    #[ORM\Column(type: "boolean")]
    private bool $msgCreationPreview;

    #[ORM\Column(type: "boolean")]
    private bool $msgPreview;

    #[ORM\Column(type: "boolean")]
    private bool $msgCopy;

    #[ORM\Column(type: "boolean")]
    private bool $msgBlink;

    #[ORM\Column(type: "int")]
    private int $spyShipId;

    #[ORM\Column(type: "int")]
    private int $spyShipCount;

    #[ORM\Column(type: "int")]
    private int $analyzeShipId;

    #[ORM\Column(type: "int")]
    private int $analyzeShipCount;

    #[ORM\Column(type: "int")]
    private int $exploreShipId;

    #[ORM\Column(type: "int")]
    private int $exploreShipCount;

    #[ORM\Column(type: "boolean")]
    private bool $showCellreports;

    #[ORM\Column(type: "boolean")]
    private bool $havenShipsButtons;

    #[ORM\Column(type: "boolean")]
    private bool $showAdds;

    #[ORM\Column(type: "boolean")]
    private bool $fleetRtnMsg;

    #[ORM\Column(type: "boolean")]
    private bool $smallResBox;

    #[ORM\Column(type: "boolean")]
    private bool $startUpChat;

    #[ORM\Column(type: "string")]
    private string $chatColor;

    #[ORM\Column(type: "boolean")]
    private bool $enableKeybinds;

    public function getCssStyle(): ?string
    {
        return $this->cssStyle;
    }

    public function setCssStyle(string $cssStyle): static
    {
        $this->cssStyle = $cssStyle;

        return $this;
    }

    public function getPlanetCircleWidth()
    {
        return $this->planetCircleWidth;
    }

    public function setPlanetCircleWidth($planetCircleWidth): static
    {
        $this->planetCircleWidth = $planetCircleWidth;

        return $this;
    }

    public function getItemShow(): ?string
    {
        return $this->itemShow;
    }

    public function setItemShow(string $itemShow): static
    {
        $this->itemShow = $itemShow;

        return $this;
    }

    public function getItemOrderShip(): ?string
    {
        return $this->itemOrderShip;
    }

    public function setItemOrderShip(string $itemOrderShip): static
    {
        $this->itemOrderShip = $itemOrderShip;

        return $this;
    }

    public function getItemOrderDef(): ?string
    {
        return $this->itemOrderDef;
    }

    public function setItemOrderDef(string $itemOrderDef): static
    {
        $this->itemOrderDef = $itemOrderDef;

        return $this;
    }

    public function getItemOrderBookmark(): ?string
    {
        return $this->itemOrderBookmark;
    }

    public function setItemOrderBookmark(string $itemOrderBookmark): static
    {
        $this->itemOrderBookmark = $itemOrderBookmark;

        return $this;
    }

    public function getItemOrderWay(): ?string
    {
        return $this->itemOrderWay;
    }

    public function setItemOrderWay(string $itemOrderWay): static
    {
        $this->itemOrderWay = $itemOrderWay;

        return $this;
    }

    public function isImageFilter(): ?bool
    {
        return $this->imageFilter;
    }

    public function setImageFilter(bool $imageFilter): static
    {
        $this->imageFilter = $imageFilter;

        return $this;
    }

    public function getMsgSignature(): ?string
    {
        return $this->msgSignature;
    }

    public function setMsgSignature(string $msgSignature): static
    {
        $this->msgSignature = $msgSignature;

        return $this;
    }

    public function isMsgCreationPreview(): ?bool
    {
        return $this->msgCreationPreview;
    }

    public function setMsgCreationPreview(bool $msgCreationPreview): static
    {
        $this->msgCreationPreview = $msgCreationPreview;

        return $this;
    }

    public function isMsgPreview(): ?bool
    {
        return $this->msgPreview;
    }

    public function setMsgPreview(bool $msgPreview): static
    {
        $this->msgPreview = $msgPreview;

        return $this;
    }

    public function isMsgCopy(): ?bool
    {
        return $this->msgCopy;
    }

    public function setMsgCopy(bool $msgCopy): static
    {
        $this->msgCopy = $msgCopy;

        return $this;
    }

    public function isMsgBlink(): ?bool
    {
        return $this->msgBlink;
    }

    public function setMsgBlink(bool $msgBlink): static
    {
        $this->msgBlink = $msgBlink;

        return $this;
    }

    public function getSpyShipId()
    {
        return $this->spyShipId;
    }

    public function setSpyShipId($spyShipId): static
    {
        $this->spyShipId = $spyShipId;

        return $this;
    }

    public function getSpyShipCount()
    {
        return $this->spyShipCount;
    }

    public function setSpyShipCount($spyShipCount): static
    {
        $this->spyShipCount = $spyShipCount;

        return $this;
    }

    public function getAnalyzeShipId()
    {
        return $this->analyzeShipId;
    }

    public function setAnalyzeShipId($analyzeShipId): static
    {
        $this->analyzeShipId = $analyzeShipId;

        return $this;
    }

    public function getAnalyzeShipCount()
    {
        return $this->analyzeShipCount;
    }

    public function setAnalyzeShipCount($analyzeShipCount): static
    {
        $this->analyzeShipCount = $analyzeShipCount;

        return $this;
    }

    public function getExploreShipId()
    {
        return $this->exploreShipId;
    }

    public function setExploreShipId($exploreShipId): static
    {
        $this->exploreShipId = $exploreShipId;

        return $this;
    }

    public function getExploreShipCount()
    {
        return $this->exploreShipCount;
    }

    public function setExploreShipCount($exploreShipCount): static
    {
        $this->exploreShipCount = $exploreShipCount;

        return $this;
    }

    public function isShowCellreports(): ?bool
    {
        return $this->showCellreports;
    }

    public function setShowCellreports(bool $showCellreports): static
    {
        $this->showCellreports = $showCellreports;

        return $this;
    }

    public function isHavenShipsButtons(): ?bool
    {
        return $this->havenShipsButtons;
    }

    public function setHavenShipsButtons(bool $havenShipsButtons): static
    {
        $this->havenShipsButtons = $havenShipsButtons;

        return $this;
    }

    public function isShowAdds(): ?bool
    {
        return $this->showAdds;
    }

    public function setShowAdds(bool $showAdds): static
    {
        $this->showAdds = $showAdds;

        return $this;
    }

    public function isFleetRtnMsg(): ?bool
    {
        return $this->fleetRtnMsg;
    }

    public function setFleetRtnMsg(bool $fleetRtnMsg): static
    {
        $this->fleetRtnMsg = $fleetRtnMsg;

        return $this;
    }

    public function isSmallResBox(): ?bool
    {
        return $this->smallResBox;
    }

    public function setSmallResBox(bool $smallResBox): static
    {
        $this->smallResBox = $smallResBox;

        return $this;
    }

    public function isStartUpChat(): ?bool
    {
        return $this->startUpChat;
    }

    public function setStartUpChat(bool $startUpChat): static
    {
        $this->startUpChat = $startUpChat;

        return $this;
    }

    public function getChatColor(): ?string
    {
        return $this->chatColor;
    }

    public function setChatColor(string $chatColor): static
    {
        $this->chatColor = $chatColor;

        return $this;
    }

    public function isEnableKeybinds(): ?bool
    {
        return $this->enableKeybinds;
    }

    public function setEnableKeybinds(bool $enableKeybinds): static
    {
        $this->enableKeybinds = $enableKeybinds;

        return $this;
    }
}
