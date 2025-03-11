<?php

declare(strict_types=1);

namespace EtoA\Specialist;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Specialist;
use EtoA\User\UserRepository;

class SpecialistService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SpecialistDataRepository $specialistRepository,
        private readonly ConfigurationService $configurationService
    ) {}

    public function getTotalUsed(Specialist $specialist): int
    {
       return min($this->userRepository->count(['specialist'=>$specialist]),$this->getTotalAvailable() );
    }

    public function getAvailable(Specialist $specialist): int
    {
        return $this->getTotalAvailable() - $this->getTotalUsed($specialist);
    }

    public function getTotalAvailable(): int
    {
        return intval(ceil($this->userRepository->count([]) * $this->configurationService->getFloat('specialistconfig')));
    }

    public function getFactor(Specialist $specialist): float|int
    {
        if ($this->getTotalAvailable())
            $factor = 1 + ($this->configurationService->param1Float('specialistconfig') / $this->getTotalAvailable() * $this->getTotalUsed($specialist));
        else
            $factor = 1;

        return $factor;
    }

    public function getSpecialistOfUser(int $userId): ?Specialist
    {
        $user = $this->userRepository->getUser($userId);

        return $user !== null && $user->getSpecialistId() > 0 && $user->getSpecialistTime() > time()
            ? $this->specialistRepository->getSpecialist($user->getSpecialistId())
            : null;
    }
}
