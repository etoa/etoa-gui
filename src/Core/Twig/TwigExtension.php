<?php declare(strict_types=1);

namespace EtoA\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('serverTime', [$this, 'getServerTime']),
            new TwigFunction('serverTimeUnix', [$this, 'getServerTimeUnix']),
            new TwigFunction('version', [$this, 'getVersion']),
            new TwigFunction('etoaUrl', [$this, 'getUrl']),
            new TwigFunction('onClick', [$this, 'getOnClick']),
            new TwigFunction('text2Html', [$this, 'text2Html']),
            new TwigFunction('configValue', [$this, 'getConfigValue']),
        ];
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
}
