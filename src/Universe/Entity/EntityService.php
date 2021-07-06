<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserRepository;

class EntityService
{
    private UserRepository $userRepository;
    private PlanetRepository $planetRepository;
    private StarRepository $starRepository;

    public function __construct(
        UserRepository $userRepository,
        PlanetRepository $planetRepository,
        StarRepository $starRepository
    ) {
        $this->userRepository = $userRepository;
        $this->planetRepository = $planetRepository;
        $this->starRepository = $starRepository;
    }

    public function formattedString(Entity $entity): string
    {
        $str = $entity->toString();

        $extra = [];

        if ($entity->code == EntityType::PLANET) {
            $planet = $this->planetRepository->find($entity->id);
            if (filled($planet->name)) {
                $extra[] = $planet->name;
            }
            if ($planet->userId > 0) {
                $nick = $this->userRepository->getNick($planet->userId);
                if (filled($nick)) {
                    $extra[] = $nick;
                }
            }
        }

        if ($entity->code == EntityType::STAR) {
            $star = $this->starRepository->find($entity->id);
            if (filled($star->name)) {
                $extra[] = $star->name;
            }
        }

        if (count($extra) > 0) {
            $str .= ' (' . implode(', ', $extra) . ')';
        }

        return $str;
    }
}
