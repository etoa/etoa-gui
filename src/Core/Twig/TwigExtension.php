<?php declare(strict_types=1);

namespace EtoA\Core\Twig;

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminUser;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Image\ImageUtil;
use EtoA\Ranking\UserBannerService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
use EtoA\Support\GameVersionService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\UI\Tooltip;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WhichBrowser\Parser;

class TwigExtension extends AbstractExtension
{
    private float $startTime;

    public function __construct(
        private readonly ConfigurationService  $config,
        private readonly NetworkNameService    $networkNameService,
        private readonly RuntimeDataStore      $runtimeDataStore,
        private readonly GameVersionService    $gameVersion,
        private readonly UrlGeneratorInterface $router,
        private readonly Tooltip $tooltip,
    )
    {
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
            new TwigFunction('param1', [$this, 'getParam1']),
            new TwigFunction('param2', [$this, 'getParam2']),
            new TwigFunction('runtimeValue', [$this, 'getRuntimeValue']),
            new TwigFunction('isAdminAllowed', [$this, 'isAdminAllowed']),
            new TwigFunction('getAdminRoles', [$this, 'getAdminRoles']),
            new TwigFunction('renderTime', [$this, 'renderTime']),
            new TwigFunction('formatTimestamp', [$this, 'formatTimestamp']),
            new TwigFunction('formatTimespan', [$this, 'formatTimespan']),
            new TwigFunction('iso8601date', [$this, 'iso8601date']),
            new TwigFunction('getGameIdentifier', [$this, 'getGameIdentifier']),
            new TwigFunction('isUnix', [$this, 'isUnix']),
            new TwigFunction('userMTT', [$this, 'userMTT']),
            new TwigFunction('cTT', [$this, 'cTT']),
            new TwigFunction('button', [$this, 'button']),
            new TwigFunction('ipGetHost', [$this, 'ipGetHost']),
            new TwigFunction('browser', [$this, 'browser']),
            new TwigFunction('formatNumber', [$this, 'formatNumber']),
            new TwigFunction('base64', [$this, 'base64']),
            new TwigFunction('tm', [$this, 'tm']),
            new TwigFunction('icon', [$this, 'icon']),
            new TwigFunction('banner', [$this, 'getBannerValues']),
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

    public function getBannerValues(string $info):int
    {
        return match ($info) {
            'width' => UserBannerService::BANNER_WIDTH,
            'height' => UserBannerService::BANNER_HEIGHT,
            default => throw new \InvalidArgumentException('Unknown value ' . $info),
        };
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
                return $this->router->generate('external.login');
            case 'banner':
                return ExternalUrl::USERBANNER_LINK;
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

    public function getParam1(string $key): string
    {
        return $this->config->param1($key);
    }

    public function getParam2(string $key): string
    {
        return $this->config->param2($key);
    }

    public function icon(string $key): string
    {
        return ImageUtil::icon($key);
    }

    public function getRuntimeValue(string $key): string
    {
        return (string)$this->runtimeDataStore->get($key);
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

    public function iso8601date(int $timestamp, bool $seconds = false): string
    {
        if ($timestamp == 0) {
            return '';
        }
        return date('Y-m-d\TH:i' . ($seconds ? ':s' : ''), $timestamp);
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
        return $this->tooltip->mTT($userNick, StringUtils::formatNumber($points) . " Punkte");
    }

    public function cTT(string $title, string $content): string
    {
        return $this->tooltip->cTT($title, $content);
    }

    public function tm(string $title, string $text): string
    {
        return $this->tooltip->mTT($title, $text);
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

    public function base64(string $value): string
    {
        return base64_encode($value);
    }
}
