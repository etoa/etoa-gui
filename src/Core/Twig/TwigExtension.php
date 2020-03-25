<?php declare(strict_types=1);

namespace EtoA\Core\Twig;

use AdminRoleManager;
use TextManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{

    /** @var float */
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('serverDate', [$this, 'getServerDate']),
            new TwigFunction('serverTime', [$this, 'getServerTime']),
            new TwigFunction('serverTimeUnix', [$this, 'getServerTimeUnix']),
            new TwigFunction('version', [$this, 'getVersion']),
            new TwigFunction('etoaUrl', [$this, 'getUrl']),
            new TwigFunction('onClick', [$this, 'getOnClick']),
            new TwigFunction('text2Html', [$this, 'text2Html']),
            new TwigFunction('configValue', [$this, 'getConfigValue']),
            new TwigFunction('popupLink', [$this, 'getPopupLink']),
            new TwigFunction('isAdminAllowed', [$this, 'isAdminAllowed']),
            new TwigFunction('renderTime', [$this, 'renderTime']),
            new TwigFunction('adminText', [$this, 'getAdminText']),
            new TwigFunction('formatTimestamp', [$this, 'formatTimestamp']),
        ];
    }

    public function getServerDate(): \DateTime
    {
        return new \DateTime();
    }

    public function getServerTime(): string
    {
        return date('H:i:s');
    }

    public function getServerTimeUnix(): int
    {
        return time();
    }

    public function getVersion(): string
    {
        return getAppVersion();
    }

    public function getUrl(string $id): string
    {
        switch ($id) {
            case 'forum':
                return FORUM_URL;
            case 'helpcenter':
                return HELPCENTER_URL;
            case 'rules':
                return RULES_URL;
            case 'teamspeak':
                return TEAMSPEAK_URL;
            case 'bugreport':
                return DEVCENTER_PATH;
            case 'chat':
                return CHAT_URL;
            case 'login':
                return '/show.php?index=login';
            default:
                throw new \InvalidArgumentException('Unknown url ' . $id);
        }
    }

    public function getOnClick(string $id): string
    {
        switch ($id) {
            case 'helpcenter':
                return HELPCENTER_ONCLICK;
            case 'rules':
                return RULES_ONCLICK;
            case 'teamspeak':
                return TEAMSPEAK_ONCLICK;
            case 'bugreport':
                return DEVCENTER_ONCLICK;
            case 'chat':
                return CHAT_ONCLICK;
            default:
                throw new \InvalidArgumentException('Unknown in click ' . $id);
        }
    }

    public function text2Html(string $string): string
    {
        return text2html($string);
    }

    public function getConfigValue(string $config)
    {
        return \Config::getInstance()->value($config);
    }

    public function getPopupLink(string $type, string $title, ?string $class = 'popuplink'): string
    {
        return sprintf(
            '<a href="#" class="%s" onclick="window.open(\'popup.php?page=%s\',\'%s\',\'width=600, height=500, status=no, scrollbars=yes\')">%s</a>',
            $class,
            $type,
            $title,
            $title
        );
    }

    public function isAdminAllowed($userRoles, $required): bool
    {
        return (new AdminRoleManager())->checkAllowed($required, $userRoles);
    }

    public function renderTime(): float
    {
        return round(microtime(true) - $this->startTime,3);
    }

    public function getAdminText(string $key): string
    {
        $tm = new TextManager();
        $text = $tm->getText($key);
        if ($text !== null) {
            if ($text->enabled && $text->content) {
                return $text->content;
            }

            return '';
        }

        throw new \RuntimeException('Admin text for key not found: ' . $key);
    }

    public function formatTimestamp($timestamp): string
    {
        return (new \DateTime('@' . $timestamp))->format('d.m.Y, H:i:s');
    }
}
