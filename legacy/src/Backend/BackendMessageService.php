<?php

declare(strict_types=1);

namespace EtoA\Backend;

class BackendMessageService
{
    private BackendMessageRepository $repository;

    public function __construct(BackendMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Sends a command to the backend to update the given planet
     */
    public function updatePlanet(int $id): void
    {
        $this->repository->addMessage('planetupdate', (string) $id);
    }

    /**
     * Sends a message to the backend to reload the config table
     */
    public function reloadConfig(): void
    {
        $this->repository->addMessage('configupdate');
    }
}
