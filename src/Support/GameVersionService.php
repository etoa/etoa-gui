<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;

class GameVersionService
{
    public function __construct(
        private ConfigurationService $config,
    ) {
    }

    function getGameIdentifier(): string
    {
        return AppName::NAME . ' ' . $this->getAppVersion() . ' ' . $this->config->get('roundname');
    }

    function getAppVersion()
    {
        require_once __DIR__ . '/../version.php';
        return APP_VERSION;
    }
}