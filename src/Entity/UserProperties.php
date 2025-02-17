<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\User\UserPropertiesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPropertiesRepository::class)]
class UserProperties
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private ?string $cssStyle;

    #[ORM\Column(type: "smallint")]
    private int $planetCircleWidth = 450;

    #[ORM\Column(type: "string")]
    private string $itemShow = 'full';

    #[ORM\Column(type: "string")]
    private string $itemOrderShip = 'name';

    #[ORM\Column(type: "string")]
    private string $itemOrderDef = 'name';

    #[ORM\Column(type: "string")]
    private ?string $itemOrderBookmark = 'bookmarks.id';

    #[ORM\Column(type: "string")]
    private ?string $itemOrderWay = 'ASC';

    #[ORM\Column(type: "boolean")]
    private bool $imageFilter = true;

    #[ORM\Column(name:"msgsignature", type: "string")]
    private ?string $msgSignature;

    #[ORM\Column(name:"msgcreation_preview", type: "boolean")]
    private bool $msgCreationPreview = true;

    #[ORM\Column(type: "boolean")]
    private bool $msgPreview = true;

    #[ORM\Column(type: "boolean")]
    private bool $msgCopy = true;

    #[ORM\Column(type: "boolean")]
    private bool $msgBlink = true;

    #[ORM\JoinColumn(name: 'spyship_id', referencedColumnName: 'ship_id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    protected Ship|null $spyShip = null;

    #[ORM\Column(name:"spyship_count", type: "integer")]
    private int $spyShipCount = 0;

    #[ORM\JoinColumn(name: 'analyzeship_id', referencedColumnName: 'ship_id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    protected Ship|null $analyzeShip = null;

    #[ORM\Column(name:"analyzeship_count", type: "integer")]
    private int $analyzeShipCount = 1;

    #[ORM\JoinColumn(name: 'exploreship_id', referencedColumnName: 'ship_id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    protected Ship|null $exploreShip = null;

    #[ORM\Column(name:"exploreship_count", type: "integer")]
    private int $exploreShipCount = 1;

    #[ORM\Column(type: "boolean")]
    private bool $showCellreports = true;

    #[ORM\Column(name:"havenships_buttons",type: "boolean")]
    private bool $havenShipsButtons = true;

    #[ORM\Column(type: "boolean")]
    private bool $showAdds = true;

    #[ORM\Column(type: "boolean")]
    private bool $fleetRtnMsg = false;

    #[ORM\Column(type: "boolean")]
    private bool $smallResBox = false;

    #[ORM\Column(name:"startup_chat",type: "boolean")]
    private bool $startUpChat = false;

    #[ORM\Column(type: "string")]
    private string $chatColor = 'ffffff';

    #[ORM\Column(name:"keybinds_enable",type: "boolean")]
    private bool $enableKeybinds = true;

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

    public function setItemOrderBookmark(?string $itemOrderBookmark): static
    {
        $this->itemOrderBookmark = $itemOrderBookmark;

        return $this;
    }

    public function getItemOrderWay(): ?string
    {
        return $this->itemOrderWay;
    }

    public function setItemOrderWay(?string $itemOrderWay): static
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpyShip(): ?Ship
    {
        return $this->spyShip;
    }

    public function setSpyShip(?Ship $spyShip): static
    {
        $this->spyShip = $spyShip;

        return $this;
    }

    public function getAnalyzeShip(): ?Ship
    {
        return $this->analyzeShip;
    }

    public function setAnalyzeShip(?Ship $analyzeShip): static
    {
        $this->analyzeShip = $analyzeShip;

        return $this;
    }

    public function getExploreShip(): ?Ship
    {
        return $this->exploreShip;
    }

    public function setExploreShip(?Ship $exploreShip): static
    {
        $this->exploreShip = $exploreShip;

        return $this;
    }
}
