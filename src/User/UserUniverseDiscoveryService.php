<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;

class UserUniverseDiscoveryService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
    }

    private function loadDiscoveryMask(int $userId): string
    {
        $discoveryMask = $this->userRepository->getDiscoverMask($userId);
        if (!filled($discoveryMask) || strlen($discoveryMask) < 3) {
            $sx_num = $this->config->param1Int('num_of_sectors');
            $cx_num = $this->config->param1Int('num_of_cells');
            $sy_num = $this->config->param2Int('num_of_sectors');
            $cy_num = $this->config->param2Int('num_of_cells');

            $mask = str_repeat('0', $sx_num * $cx_num * $sy_num * $cy_num);
            $this->userRepository->saveDiscoveryMask($userId, $mask);

            return $mask;
        }

        return $discoveryMask;
    }

    public function discovered(int $userId, int $absX, int $absY): bool
    {
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cy_num = $this->config->param2Int('num_of_cells');

        $mask = $this->loadDiscoveryMask($userId);

        $pos = $absX + ($cy_num * $sy_num) * ($absY - 1) - 1;

        return ($pos < strlen($mask) && $mask[$pos] > 0);
    }

    public function getDiscoveredPercent(int $userId): float
    {
        $mask = $this->loadDiscoveryMask($userId);

        $len = strlen($mask);
        if ($len > 0) {
            return substr_count($mask, "1") / $len * 100;
        }

        return 0;
    }

    public function setDiscovered(int $userId, int $absX, int $absY, int $radius = 1): void
    {
        $mask = $this->loadDiscoveryMask($userId);

        $sx_num = $this->config->param1Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cy_num = $this->config->param2Int('num_of_cells');

        for ($x = $absX - $radius; $x <= $absX + $radius; $x++) {
            for ($y = $absY - $radius; $y <= $absY + $radius; $y++) {
                if ($x > 0 && $y > 0 && $x <= $sx_num * $cx_num && $y <= $sy_num * $cy_num) {
                    $pos = $x + ($cy_num * $sy_num) * ($y - 1) - 1;
                    if ($pos >= 0 && $pos <= $sx_num * $sy_num * $cx_num * $cy_num) {
                        $mask[$pos] = '1';
                    }
                }
            }
        }

        $this->userRepository->saveDiscoveryMask($userId, $mask);
    }

    public function setDiscoveredAll(int $userId, bool $discovered): void
    {
        $mask = $this->loadDiscoveryMask($userId);

        $sx_num = $this->config->param1Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cy_num = $this->config->param2Int('num_of_cells');

        for ($x = 1; $x <= $sx_num * $cx_num; $x++) {
            for ($y = 1; $y <= $sy_num * $cy_num; $y++) {
                $pos = $x + ($cy_num * $sy_num) * ($y - 1) - 1;
                $mask[$pos] = $discovered ? '1' : '0';
            }
        }

        $this->userRepository->saveDiscoveryMask($userId, $mask);
    }
}
