<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\PeriodicTask\Result\ResultInterface;
use EtoA\PeriodicTask\Result\SkipResult;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\SetHolidayModeUsersInactiveTask;
use EtoA\User\UserHolidayService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SetHolidayModeUsersInactiveHandler implements MessageHandlerInterface
{
    private ConfigurationService $config;
    private UserHolidayService $userHolidayService;

    public function __construct(ConfigurationService $config, UserHolidayService $userHolidayService)
    {
        $this->config = $config;
        $this->userHolidayService = $userHolidayService;
    }

    public function __invoke(SetHolidayModeUsersInactiveTask $task): ResultInterface
    {
        if ($this->config->param2Boolean('hmode_days')) {
            $count = $this->userHolidayService->setUmodeToInactive();

            return SuccessResult::create("$count User aus Urlaubsmodus in Inaktivität gesetzt");
        }

        return new SkipResult();
    }
}
