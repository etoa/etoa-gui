<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Database\AbstractSort;

class ReportSort extends AbstractSort
{
    public static function timestamp(string $order = 'ASC'): ReportSort
    {
        return new ReportSort(['timestamp' => $order]);
    }
}
