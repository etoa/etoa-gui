<?php

declare(strict_types=1);

namespace EtoA\Universe\EmptySpace;

class EmptySpace
{
    public int $id;
    public int $lastVisited;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->lastVisited = (int) $data['lastvisited'];
    }
}
