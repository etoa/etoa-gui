<?php

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HavePoints implements InitProgressHandlerFunctionInterface
{
    const NAME = 'have-points';

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task)
    {
        return $this->userRepository->getPoints($quest->getUser());
    }
}
