<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\Support\BBCodeUtils;

class Tooltip
{
    const TOOLTIP_TEXT_COLOR = '#eef';
    const TOOLTIP_COMMENT_COLOR = '#FFD517';
    const TOOLTIP_TITLE_COLOR = '#fff';
    const TOOLTIP_COND_GOD_COLOR = '#0f0';
    const TOOLTIP_COND_BAD_COLOR = '#f00';

    private string $text = "";
    private string $title = "";

    private function add(string $obj): void
    {
        $this->text .= $obj;
    }

    public function toString(): string
    {
        return mTT($this->title, $this->text);
    }

    public function addIcon(string $path): void
    {
        $this->text = '<div style="float:right;"><img src="' . $path . '" alt="Icon" /></div>';
    }

    public function addText(string $text): void
    {
        $this->add(BBCodeUtils::toHTML($text) . "<br/>");
    }

    public function addHtml(string $text): void
    {
        $this->add($text . "<br/>");
    }

    public function addTitle(string $text): void
    {
        $this->title .= "<div style=\"color:" . self::TOOLTIP_TITLE_COLOR . "\"><b>" . $text . "</b></div>";
    }

    public function addGoodCond(string $text): void
    {
        $this->add("<div style=\"color:" . self::TOOLTIP_COND_GOD_COLOR . "\">" . $text . "</div>");
    }

    public function addBadCond(string $text): void
    {
        $this->add("<div style=\"color:" . self::TOOLTIP_COND_BAD_COLOR . "\">" . $text . "</div>");
    }

    public function addComment(string $text): void
    {
        $this->add("<div style=\"color:" . self::TOOLTIP_COMMENT_COLOR . "\">" . $text . "</div>");
    }

    public function addImage(string $path): void
    {
        $this->add("<img src=\"" . $path . "\" alt=\"TTImage\" style=\"background:#000;\" /><br/>");
    }
}
