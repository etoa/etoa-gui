<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\UpdateSittingDaysTask;
use EtoA\User\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateSittingDaysHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;
    private ConfigurationService $config;

    public function __construct(UserRepository $userRepository, ConfigurationService $config)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
    }

    public function __invoke(UpdateSittingDaysTask $task): SuccessResult
    {
        $this->userRepository->addSittingDays($this->config->param1Int("user_sitting_days"));

        return SuccessResult::create("Sittertage aller User wurden aktualisiert");
    }
}
