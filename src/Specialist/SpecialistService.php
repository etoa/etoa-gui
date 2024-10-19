<?php

declare(strict_types=1);

namespace EtoA\Specialist;

use EtoA\Entity\Specialist;
use EtoA\User\UserRepository;

class SpecialistService
{
    private UserRepository $userRepository;
    private SpecialistDataRepository $specialistRepository;

    public function __construct(
        UserRepository $userRepository,
        SpecialistDataRepository $specialistRepository
    ) {
        $this->userRepository = $userRepository;
        $this->specialistRepository = $specialistRepository;
    }

    public function getSpecialistOfUser(int $userId): ?Specialist
    {
        $user = $this->userRepository->getUser($userId);

        return $user !== null && $user->getSpecialistId() > 0 && $user->getSpecialistTime() > time()
            ? $this->specialistRepository->getSpecialist($user->getSpecialistId())
            : null;
    }
}
