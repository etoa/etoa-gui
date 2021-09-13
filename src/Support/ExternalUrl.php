<?php declare(strict_types=1);

namespace EtoA\Support;

class ExternalUrl
{
    // Helpcenter Link
    public const HELP_CENTER = "http://www.etoa.ch/help/?page=faq";
    public const HELP_CENTER_ON_CLICK = "window.open('" . self::HELP_CENTER . "','helpcenter','width=1024,height=700,scrollbars=yes');";

    // Forum Link
    public const FORUM = "http://forum.etoa.ch";

    // Chat
    public const CHAT = "chatframe.php";
    public const CHAT_ON_CLICK = "parent.top.location='chatframe.php';";

    // Teamspeak
    public const TEAMSPEAK = "https://discord.gg/7d2ndEU";
    public const TEAMSPEAK_ON_CLICK = "window.open('" . self::TEAMSPEAK . "','_blank');";

    // Game-Rules
    public const RULES = 'http://www.etoa.ch/regeln';
    public const RULES_ON_CLICK = "window.open('" . self::RULES . "','rules','width=auto,height=auto,scrollbars=yes');";

    // Privacy statement
    public const PRIVACY = 'http://www.etoa.ch/privacy';

    // URL for user banner HTML snippet
    public const USERBANNER_LINK = 'http://www.etoa.ch';

    // Entwickler Link
    public const DEV_CENTER = "http://dev.etoa.ch";

    // Entwickler Link (popup)
    public const DEV_CENTER_ON_CLICK = "window.open('" . self::DEV_CENTER . "','dev','width=1024,height=768,scrollbars=yes');";
}
