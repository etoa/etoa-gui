<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Message\Event\MessageSend;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class SendMessageTest extends AbstractProgressFunctionTestCase
{
    /** @var SendMessage */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new SendMessage();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new MessageSend());
        }, $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
