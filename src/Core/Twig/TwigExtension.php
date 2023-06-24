<?php declare(strict_types=1);

namespace EtoA\Core\Twig;

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminUser;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
use EtoA\Support\GameVersionService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WhichBrowser\Parser;

class TwigExtension extends AbstractExtension
{
    private float $startTime;

    public function __construct(
        private ConfigurationService $config,
        private NetworkNameService $networkNameService,
        private RuntimeDataStore $runtimeDataStore,
        private GameVersionService $gameVersion,
    ) {
        $this->startTime = microtime(true);
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
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
            new TwigFunction('runtimeValue', [$this, 'getRuntimeValue']),
            new TwigFunction('isAdminAllowed', [$this, 'isAdminAllowed']),
            new TwigFunction('getAdminRoles', [$this, 'getAdminRoles']),
            new TwigFunction('renderTime', [$this, 'renderTime']),
            new TwigFunction('formatTimestamp', [$this, 'formatTimestamp']),
            new TwigFunction('formatTimespan', [$this, 'formatTimespan']),
            new TwigFunction('getGameIdentifier', [$this, 'getGameIdentifier']),
            new TwigFunction('isUnix', [$this, 'isUnix']),
            new TwigFunction('userMTT', [$this, 'userMTT']),
            new TwigFunction('cTT', [$this, 'cTT']),
            new TwigFunction('editButton', [$this, 'editButton']),
            new TwigFunction('delButton', [$this, 'delButton']),
            new TwigFunction('button', [$this, 'button']),
            new TwigFunction('ipGetHost', [$this, 'ipGetHost']),
            new TwigFunction('browser', [$this, 'browser']),
            new TwigFunction('formatNumber', [$this, 'formatNumber']),
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
                return '/show/?index=login';
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

    public function BBCodeToHTML(?string $string): string
    {
        return BBCodeUtils::toHTML($string);
    }

    public function getConfigValue(string $key): string
    {
        return $this->config->get($key);
    }

    public function getRuntimeValue(string $key): string
    {
        return (string) $this->runtimeDataStore->get($key);
    }

    /**
     * @param string[] $userRoles
     * @param string|string[] $required
     */
    public function isAdminAllowed(array $userRoles, $required): bool
    {
        return (new AdminRoleManager())->checkAllowedRoles($userRoles, $required);
    }

    public function getAdminRoles(AdminUser $admin): string
    {
        return (new AdminRoleManager())->getRolesStr($admin);
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
        return StringUtils::formatDate($timestamp);
    }

    public function formatTimespan(int $timespan): string
    {
        return StringUtils::formatTimespan($timespan);
    }

    public function getGameIdentifier(): string
    {
        return $this->gameVersion->getGameIdentifier();
    }

    public function isUnix(): bool
    {
        return isUnixOS();
    }

    public function userMTT(string $userNick, int $points): string
    {
        return mTT($userNick, StringUtils::formatNumber($points) . " Punkte");
    }

    public function cTT(string $title, string $content): string
    {
        return cTT($title, $content);
    }

    public function editButton(?string $url, string $ocl = ""): string
    {
        return edit_button($url, $ocl);
    }

    public function delButton(?string $url, string $ocl = ""): string
    {
        return del_button($url, $ocl);
    }

    public function button(string $label, string $target): string
    {
        return button($label, $target);
    }

    public function ipGetHost(?string $ip): string
    {
        return $this->networkNameService->getHost($ip);
    }

    public function formatNumber(float $number): string
    {
        return StringUtils::formatNumber($number);
    }

    public function browser(string $userAgent): Parser
    {
        return new Parser($userAgent);
    }
}
