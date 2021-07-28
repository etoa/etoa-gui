<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Cell\Cell;

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

    private function ensureDiscoveryMaskExists(User $user): void
    {
        if (!filled($user->discoveryMask) || strlen($user->discoveryMask) < 3) {
            $sx_num = $this->config->param1Int('num_of_sectors');
            $cx_num = $this->config->param1Int('num_of_cells');
            $sy_num = $this->config->param2Int('num_of_sectors');
            $cy_num = $this->config->param2Int('num_of_cells');

            $user->discoveryMask = str_repeat('0', $sx_num * $cx_num * $sy_num * $cy_num);
            $this->userRepository->saveDiscoveryMask($user->id, $user->discoveryMask);
        }
    }

    public function discovered(User $user, int $absX, int $absY): bool
    {
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cy_num = $this->config->param2Int('num_of_cells');

        $this->ensureDiscoveryMaskExists($user);

        $pos = $absX + ($cy_num * $sy_num) * ($absY - 1) - 1;

        return ($pos < strlen($user->discoveryMask) && $user->discoveryMask[$pos] > 0);
    }

    public function getDiscoveredPercent(User $user): float
    {
        $this->ensureDiscoveryMaskExists($user);

        $len = strlen($user->discoveryMask);
        if ($len > 0) {
            return substr_count($user->discoveryMask, "1") / $len * 100;
        }

        return 0;
    }

    public function setDiscovered(User $user, Cell $cell, int $radius = 1): void
    {
        $this->ensureDiscoveryMaskExists($user);

        $sx_num = $this->config->param1Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cy_num = $this->config->param2Int('num_of_cells');

        [$absX, $absY] = $cell->getAbsoluteCoordinates($cx_num, $cy_num);

        for ($x = $absX - $radius; $x <= $absX + $radius; $x++) {
            for ($y = $absY - $radius; $y <= $absY + $radius; $y++) {
                if ($x > 0 && $y > 0 && $x <= $sx_num * $cx_num && $y <= $sy_num * $cy_num) {
                    $pos = $x + ($cy_num * $sy_num) * ($y - 1) - 1;
                    if ($pos >= 0 && $pos <= $sx_num * $sy_num * $cx_num * $cy_num) {
                        $user->discoveryMask[$pos] = '1';
                    }
                }
            }
        }

        $this->userRepository->saveDiscoveryMask($user->id, $user->discoveryMask);
    }

    public function setDiscoveredAll(User $user, bool $discovered): void
    {
        $this->ensureDiscoveryMaskExists($user);

        $sx_num = $this->config->param1Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cy_num = $this->config->param2Int('num_of_cells');

        for ($x = 1; $x <= $sx_num * $cx_num; $x++) {
            for ($y = 1; $y <= $sy_num * $cy_num; $y++) {
                $pos = $x + ($cy_num * $sy_num) * ($y - 1) - 1;
                $user->discoveryMask[$pos] = $discovered ? '1' : '0';
            }
        }

        $this->userRepository->saveDiscoveryMask($user->id, $user->discoveryMask);
    }
}
