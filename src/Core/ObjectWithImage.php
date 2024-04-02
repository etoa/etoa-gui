<?php declare(strict_types=1);

namespace EtoA\Core;

interface ObjectWithImage
{
    public const BASE_PATH = '/build/images/imagepacks/Discovery';

    public function getImagePath(): string;
}
