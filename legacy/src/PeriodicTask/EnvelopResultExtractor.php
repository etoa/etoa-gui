<?php declare(strict_types=1);

namespace EtoA\PeriodicTask;

use EtoA\PeriodicTask\Result\ResultInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class EnvelopResultExtractor
{
    public static function extract(Envelope $envelope): ResultInterface
    {
        $handledStamp = $envelope->last(HandledStamp::class);
        $result = null;
        if ($handledStamp instanceof HandledStamp) {
            $result = $handledStamp->getResult();
        }

        if (!$result instanceof ResultInterface) {
            throw new \RuntimeException('Invalid result received');
        }

        return $result;
    }
}
