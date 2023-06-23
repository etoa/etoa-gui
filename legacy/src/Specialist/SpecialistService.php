<?php

declare(strict_types=1);

namespace EtoA\Specialist;

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

        return $user !== null && $user->specialistId > 0 && $user->specialistTime > time()
            ? $this->specialistRepository->getSpecialist($user->specialistId)
            : null;
    }
}
