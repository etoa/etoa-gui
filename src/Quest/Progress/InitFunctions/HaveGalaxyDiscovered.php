<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveGalaxyDiscovered implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-galaxy-discovered';

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        $discoverMask = $this->userRepository->getDiscoverMask($quest->getUser());

        return (int)(strlen($discoverMask) > 0 && substr_count($discoverMask, '0') === 0);
    }
}
