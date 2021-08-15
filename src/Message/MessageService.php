<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use Log;

class MessageService
{
    private MessageRepository $repository;
    private ConfigurationService $config;

    public function __construct(MessageRepository $repository, ConfigurationService $config)
    {
        $this->repository = $repository;
        $this->config = $config;
    }

    public function removeOld(int $threshold = 0, bool $onlyDeleted = false): int
    {
        $count = 0;

        if (!$onlyDeleted) {
            // Normal old messages
            $timestamp = $threshold > 0
                ? time() - $threshold
                : time() - (24 * 3600 * $this->config->getInt('messages_threshold_days'));

            $ids = $this->repository->findIdsOfReadNotArchivedOlderThan($timestamp);
            $count += $this->repository->removeBulk($ids);

            Log::add(LogFacility::SYSTEM, Log::INFO, "Unarchivierte Nachrichten die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        }

        // Deleted
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->param1Int('messages_threshold_days'));

        $ids = $this->repository->findIdsOfDeletedOlderThan($timestamp);
        $count += $this->repository->removeBulk($ids);

        Log::add(LogFacility::SYSTEM, Log::INFO, "Unarchivierte Nachrichten die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $count;
    }
}
