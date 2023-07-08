<?php

namespace EtoA\Components\Admin\Widgets;

use EtoA\Support\DB\DatabaseManagerRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('admin:widgets:systemInfo')]
class SystemInfoWidget
{
    public array $data = [];

    public function __construct(
        private readonly DatabaseManagerRepository $databaseManager,
    )
    {
    }

    public function mount(): void
    {
        $this->data = [
            [
                'label' => 'PHP',
                'value' => phpversion(),
            ],
            [
                'label' => 'Datenbank',
                'value' => $this->databaseManager->getDatabasePlatform(),
            ],
            [
                'label' => 'Webserver',
                'value' => $_SERVER['SERVER_SOFTWARE'] ?? '',
            ],
            [
                'label' => 'OS Kernel / Architektur',
                'value' => $this->getOsString(),
            ],
            [
                'label' => 'Systemlast',
                'value' => $this->getSysLoad(),
            ],
        ];
    }

    public function getOsString(): ?string
    {
        if (isUnixOS()) {
            $unix = posix_uname();
            return $unix != null ? $unix['sysname'] . ' ' . $unix['release'] . ' ' . $unix['version'] : null;
        }
        return null;
    }

    public function getSysLoad(): ?string
    {
        if (isUnixOS()) {
            exec("cat /proc/cpuinfo | grep processor | wc -l", $out);
            $load = sys_getloadavg();
            $systemLoad = round($load[2] / intval($out[0]) * 100, 2);
            return "" . $systemLoad;
        }
        return null;
    }
}