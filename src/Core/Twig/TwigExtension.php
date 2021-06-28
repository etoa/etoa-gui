<?php declare(strict_types=1);

namespace EtoA\Core\Twig;

use EtoA\Admin\AdminRoleManager;
use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private float $startTime;

    private Container $app;

    public function __construct(Container $app)
    {
        $this->startTime = microtime(true);
        $this->app = $app;
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
            new TwigFunction('isAdminAllowed', [$this, 'isAdminAllowed']),
            new TwigFunction('renderTime', [$this, 'renderTime']),
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

    public function getConfigValue(string $key): string
    {
        /** @var ConfigurationService */
        $config = $this->app['etoa.config.service'];

        return $config->get($key);
    }

    public function isAdminAllowed(array $userRoles, $required): bool
    {
        return (new AdminRoleManager())->checkAllowedRoles($userRoles, $required);
    }

    public function renderTime(): float
    {
        return round(microtime(true) - $this->startTime, 3);
    }

    public function formatTimestamp($timestamp): string
    {
        return (new \DateTime('@' . $timestamp))->format('d.m.Y, H:i:s');
    }
}
