<?php

namespace EtoA\Legacy;

class LegacyController
{
    public function catchAllAction()
    {
        return include dirname(__DIR__) . '/../htdocs/index.php';
    }
}
