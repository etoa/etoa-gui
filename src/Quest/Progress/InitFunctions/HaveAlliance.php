<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveAlliance implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-alliance';

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        return (int)($this->userRepository->getAllianceId($quest->getUser()) > 0);
    }
}
