<?php

namespace EtoA\Image;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\GalaxyMap;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;

class GalaxyMapImageGenerator
{
    const IMG_BASE_DIR = "assets/images/imagepacks/Discovery";

    public function __construct(
        private readonly ConfigurationService         $config,
        private readonly CellRepository               $cellRepo,
        private readonly EntityRepository             $entityRepo,
        private readonly StarRepository               $starRepo,
        private readonly WormholeRepository           $whRepo,
        private readonly UserRepository               $userRepository,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly string                       $projectDir,
    )
    {
    }

    public function createMap(string $type, int $size, bool $showLegend, bool $showAll = false, int $userId = 0): void
    {
        $user = $userId > 0 ? $this->userRepository->getUser($userId) : null;

        $mim = new GalaxyMapImage(
            size: $size,
            showLegend: $showLegend,
            numSectorsX: $this->config->param1Int('num_of_sectors'),
            numSectorsY: $this->config->param2Int('num_of_sectors'),
            numCellsX: $this->config->param1Int('num_of_cells'),
            numCellsY: $this->config->param2Int('num_of_cells'),
            maxNumPlanets: $this->config->param2Int('num_planets'),
        );

        if ($type == "alliance") {
            $this->drawAllianceSystemsMap($user, $mim);
        } elseif ($type == "own") {
            $this->drawOwnedSystemsMap($user, $mim);
        } elseif ($type == "populated") {
            $this->drawPopulatedSystemsMap($mim);
        } else {
            $this->drawGalaxyMap($mim, $showAll, $user);
        }

        for ($x = ($mim->numCellsX * $mim->galaxyImageScale); $x < $mim->width; $x += ($mim->numCellsX * $mim->galaxyImageScale)) {
            ImageUtil::dashedLine($mim->image, $x, 0, $x, $mim->height - $mim->legendHeight, $mim->colorGrey, $mim->colorBlack);
        }
        for ($y = ($mim->numCellsY * $mim->galaxyImageScale); $y < $mim->height; $y += ($mim->numCellsY * $mim->galaxyImageScale)) {
            ImageUtil::dashedLine($mim->image, 0, $y, $mim->width, $y, $mim->colorGrey, $mim->colorBlack);
        }

        imagepng($mim->image);
    }

    public function createEmptyMessage(int $size, string $message): void
    {
        $im = imagecreatetruecolor($size, $size);
        $colWhite = imagecolorallocate($im, 255, 255, 255);
        imagestring($im, 5, 10, 10, $message, $colWhite);
        imagepng($im);
    }

    private function drawGalaxyMap(GalaxyMapImage $mim, bool $showAll, ?User $user): void
    {
        $entities = $this->entityRepo->searchEntities(EntitySearch::create()->pos(0));
        if (empty($entities)) {
            imagestring($mim->image, 3, 20, 20, "Universum existiert noch nicht!", $mim->colorWhite);
            return;
        }

        $imgDir = $this->projectDir . '/' . self::IMG_BASE_DIR;

        $starImageSrc = imagecreatefrompng($imgDir . "/stars/star4_small.png");
        $starImage = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
        imagecopyresampled($starImage, $starImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($starImageSrc), imagesy($starImageSrc));

        $nebulaImageSrc = imagecreatefrompng($imgDir . "/nebulas/nebula2_small.png");
        $nebulaImage = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
        imagecopyresampled($nebulaImage, $nebulaImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($nebulaImageSrc), imagesy($nebulaImageSrc));

        $asteroidImageSrc = imagecreatefrompng($imgDir . "/asteroids/asteroids1_small.png");
        $asteroidImage = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
        imagecopyresampled($asteroidImage, $asteroidImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($asteroidImageSrc), imagesy($asteroidImageSrc));

        $spaceImageSrc = imagecreatefrompng($imgDir . "/space/space1_small.png");
        $spaceImage = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
        imagecopyresampled($spaceImage, $spaceImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($spaceImageSrc), imagesy($spaceImageSrc));

        $wormholeImageSrc = imagecreatefrompng($imgDir . "/wormholes/wormhole1_small.png");
        $wormholeImage = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
        imagecopyresampled($wormholeImage, $wormholeImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($wormholeImageSrc), imagesy($wormholeImageSrc));

        $persistentWormholeImageSrc = imagecreatefrompng($imgDir . "/wormholes/wormhole_persistent1_small.png");
        $persistentWormholeImage = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
        imagecopyresampled($persistentWormholeImage, $persistentWormholeImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($persistentWormholeImageSrc), imagesy($persistentWormholeImageSrc));

        $unexploredImages = [];
        for ($i = 1; $i < 7; $i++) {
            $unexploredImageSrc = imagecreatefrompng($imgDir . "/unexplored/fog$i.png");
            $unexploredImages[$i] = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
            imagecopyresampled($unexploredImages[$i], $unexploredImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($unexploredImageSrc), imagesy($unexploredImageSrc));
        }

        $fogBorderImages = [];
        for ($i = 1; $i < 16; $i++) {
            $fogBorderImageSrc = imagecreatefrompng($imgDir . "/unexplored/fogborder$i.png");
            $fogBorderImages[$i] = imagecreatetruecolor($mim->galaxyImageScale, $mim->galaxyImageScale);
            imagecopyresampled($fogBorderImages[$i], $fogBorderImageSrc, 0, 0, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($fogBorderImageSrc), imagesy($fogBorderImageSrc));
        }

        foreach ($entities as $entity) {
            $x = ((($entity->sx - 1) * $mim->numCellsX + $entity->cx) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            $y = $mim->height - $mim->legendHeight + $mim->galaxyImageScale - ((($entity->sy - 1) * $mim->numCellsY + $entity->cy) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            $xe = $x - ($mim->galaxyImageScale / 2);
            $ye = $y - ($mim->galaxyImageScale / 2);

            $sx = $entity->sx;
            $sy = $entity->sy;
            if (($showAll && $user === null) || $user !== null && $this->userUniverseDiscoveryService->discovered($user, (($entity->sx - 1) * $mim->numCellsX) + $entity->cx, (($entity->sy - 1) * $mim->numCellsY) + $entity->cy)) {
                if ($entity->code == EntityType::STAR) {
                    $star = $this->starRepo->find($entity->id);
                    $starImageSrc = imagecreatefrompng($imgDir . "/stars/star" . $star->typeId . "_small.png");
                    imagecopyresampled($mim->image, $starImageSrc, $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, imagesx($starImageSrc), imagesy($starImageSrc));
                } elseif ($entity->code == EntityType::WORMHOLE) {
                    $wh = $this->whRepo->find($entity->id);
                    if ($wh->persistent) {
                        imagecopyresampled($mim->image, $persistentWormholeImage, $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                    } else {
                        imagecopyresampled($mim->image, $wormholeImage, $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                    }
                } elseif ($entity->code == EntityType::ASTEROID) {
                    imagecopyresampled($mim->image, $asteroidImage, $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                } elseif ($entity->code == EntityType::NEBULA) {
                    imagecopyresampled($mim->image, $nebulaImage, $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                } elseif ($entity->code == EntityType::EMPTY_SPACE || $entity->code == EntityType::MARKET) {
                    imagecopyresampled($mim->image, $spaceImage, $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                }
            } elseif ($user !== null) {
                $fogCode = 0;
                // Bottom
                $fogCode += $entity->cy > 1 && $this->userUniverseDiscoveryService->discovered($user, (($sx - 1) * $mim->numCellsX) + $entity->cx, (($sy - 1) * $mim->numCellsY) + $entity->cy - 1) ? 1 : 0;
                // Left
                $fogCode += $entity->cx > 1 && $this->userUniverseDiscoveryService->discovered($user, (($sx - 1) * $mim->numCellsX) + $entity->cx - 1, (($sy - 1) * $mim->numCellsY) + $entity->cy) ? 2 : 0;
                // Right
                $fogCode += $entity->cx < $mim->numCellsX && $this->userUniverseDiscoveryService->discovered($user, (($sx - 1) * $mim->numCellsX) + $entity->cx + 1, (($sy - 1) * $mim->numCellsY) + $entity->cy) ? 4 : 0;
                // Top
                $fogCode += $entity->cy < $mim->numCellsY && $this->userUniverseDiscoveryService->discovered($user, (($sx - 1) * $mim->numCellsX) + $entity->cx, (($sy - 1) * $mim->numCellsY) + $entity->cy + 1) ? 8 : 0;
                if ($fogCode > 0) {
                    imagecopyresampled($mim->image, $fogBorderImages[$fogCode], $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                } else {
                    imagecopyresampled($mim->image, $unexploredImages[mt_rand(1, 6)], $xe, $ye, 0, 0, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale, $mim->galaxyImageScale);
                }
            }
        }

        if ($mim->showLegend) {
            imagestring($mim->image, 3, 10, $mim->height - $mim->legendHeight + 10, "Galaxiekarte", $mim->colorWhite);
        }
    }

    private function drawPopulatedSystemsMap(GalaxyMapImage $mim): void
    {
        $col = [];
        for ($x = 1; $x <= $mim->maxNumPlanets; $x++) {
            $col[$x] = imagecolorallocate($mim->image, (255 / $mim->maxNumPlanets * $x), (255 / $mim->maxNumPlanets * $x), 0);
        }
        $cells = $this->cellRepo->getCellPopulation();
        foreach ($cells as $cell) {
            $x = ((($cell->sx - 1) * $mim->numCellsX + $cell->cx) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            $y = $mim->height - $mim->legendHeight + $mim->galaxyImageScale - ((($cell->sy - 1) * $mim->numCellsY + $cell->cy) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            imagefilledellipse($mim->image, $x, $y, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[max(3, $cell->count)]);
        }
        if ($mim->showLegend) {
            imagestring($mim->image, 3, 10, $mim->height - $mim->legendHeight + 10, "Legende:    Viel    Mittel    Wenig", $mim->colorWhite);
            imagefilledellipse($mim->image, 80, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$mim->maxNumPlanets]);
            imagefilledellipse($mim->image, 135, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[floor($mim->maxNumPlanets / 2)]);
            imagefilledellipse($mim->image, 205, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[3]);
        }
    }

    private function drawOwnedSystemsMap(?User $user, GalaxyMapImage $mim): void
    {
        if ($user === null) {
            imagestring($mim->image, 5, 10, 10, "User nicht gefunden!", $mim->colorWhite);
            return;
        }

        $col = [];
        for ($x = 1; $x <= $mim->maxNumPlanets; $x++) {
            $col[$x] = imagecolorallocate($mim->image, 105 + (150 / $mim->maxNumPlanets * $x), 105 + (150 / $mim->maxNumPlanets * $x), 0);
        }

        $cells = $this->cellRepo->getCellPopulationForUser($user->getId());
        foreach ($cells as $cell) {
            $x = ((($cell->sx - 1) * $mim->numCellsX + $cell->cx) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            $y = $mim->height - $mim->legendHeight + $mim->galaxyImageScale - ((($cell->sy - 1) * $mim->numCellsY + $cell->cy) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            imagefilledellipse($mim->image, $x, $y, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$cell->count]);
        }
        if ($mim->showLegend) {
            imagestring($mim->image, 3, 10, $mim->height - $mim->legendHeight + 10, "Legende:    Viel    Mittel    Wenig", $mim->colorWhite);
            imagefilledellipse($mim->image, 80, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$mim->maxNumPlanets]);
            imagefilledellipse($mim->image, 135, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[floor($mim->maxNumPlanets / 2)]);
            imagefilledellipse($mim->image, 205, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[3]);
        }
    }

    private function drawAllianceSystemsMap(?User $user, GalaxyMapImage $mim): void
    {
        if ($user === null) {
            imagestring($mim->image, 5, 10, 10, "User nicht gefunden!", $mim->colorWhite);
            return;
        }

        $col = [];
        for ($x = 1; $x <= $mim->maxNumPlanets; $x++) {
            $col[$x] = imagecolorallocate($mim->image, 105 + (150 / $mim->maxNumPlanets * $x), 105 + (150 / $mim->maxNumPlanets * $x), 0);
        }
        $cells = $this->cellRepo->getCellPopulationForUserAlliance($user->getId());
        foreach ($cells as $cell) {
            $x = ((($cell->sx - 1) * $mim->numCellsX + $cell->cx) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            $y = $mim->height - $mim->legendHeight + $mim->galaxyImageScale - ((($cell->sy - 1) * $mim->numCellsY + $cell->cy) * $mim->galaxyImageScale) - ($mim->galaxyImageScale / 2);
            imagefilledellipse($mim->image, $x, $y, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$cell->count]);
        }
        if ($mim->showLegend) {
            imagestring($mim->image, 3, 10, $mim->height - $mim->legendHeight + 10, "Legende:    Viel    Mittel    Wenig", $mim->colorWhite);
            imagefilledellipse($mim->image, 80, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$mim->maxNumPlanets]);
            imagefilledellipse($mim->image, 135, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[floor($mim->maxNumPlanets / 2)]);
            imagefilledellipse($mim->image, 205, $mim->height - $mim->legendHeight + 10 + GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[3]);
        }
    }
}
