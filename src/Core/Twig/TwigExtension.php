<?php declare(strict_types=1);

namespace EtoA\Core\Twig;

use EtoA\Admin\AdminRoleManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
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
            new TwigFunction('BBCodeToHTML', [$this, 'BBCodeToHTML']),
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
                return ExternalUrl::FORUM;
            case 'helpcenter':
                return ExternalUrl::HELP_CENTER;
            case 'rules':
                return ExternalUrl::RULES;
            case 'teamspeak':
                return ExternalUrl::TEAMSPEAK;
            case 'bugreport':
                return ExternalUrl::DEV_CENTER;
            case 'chat':
                return ExternalUrl::CHAT;
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
                return ExternalUrl::HELP_CENTER_ON_CLICK;
            case 'rules':
                return ExternalUrl::RULES_ON_CLICK;
            case 'teamspeak':
                return ExternalUrl::TEAMSPEAK_ON_CLICK;
            case 'bugreport':
                return ExternalUrl::DEV_CENTER_ON_CLICK;
            case 'chat':
                return ExternalUrl::CHAT_ON_CLICK;
            default:
                throw new \InvalidArgumentException('Unknown in click ' . $id);
        }
    }

    public function BBCodeToHTML(string $string): string
    {
        return BBCodeUtils::toHTML($string);
    }

    public function getConfigValue(string $key): string
    {
        /** @var ConfigurationService */
        $config = $this->app[ConfigurationService::class];

        return $config->get($key);
    }

    /**
     * @param string[] $userRoles
     * @param string|string[] $required
     */
    public function isAdminAllowed(array $userRoles, $required): bool
    {
        return (new AdminRoleManager())->checkAllowedRoles($userRoles, $required);
    }

    public function renderTime(): float
    {
        return round(microtime(true) - $this->startTime, 3);
    }

    /**
     * @param string|int $timestamp
     */
    public function formatTimestamp($timestamp): string
    {
        return (new \DateTime('@' . $timestamp))->format('d.m.Y, H:i:s');
    }
}
