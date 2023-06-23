<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\HostCache\NetworkNameService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\ClearIPHostnameCacheTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearIPHostnameCacheHandler implements MessageHandlerInterface
{
    private NetworkNameService $networkNameService;

    public function __construct(NetworkNameService $networkNameService)
    {
        $this->networkNameService = $networkNameService;
    }

    public function __invoke(ClearIPHostnameCacheTask $task): SuccessResult
    {
        $this->networkNameService->clearCache();

        return SuccessResult::create("IP/Hostname Cache gelöscht");
    }
}
