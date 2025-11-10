<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;

class LogSearch extends AbstractSearch
{
    public static function create(): LogSearch
    {
        return new LogSearch();
    }

    public function messageLike(string $message): self
    {
        $this->parts[] = 'q.message LIKE :message';
        $this->parameters['message'] = '%' . $message . '%';

        return $this;
    }

    public function severity(int $severity): self
    {
        $this->parts[] = 'q.severity >= :severity';
        $this->parameters['severity'] = $severity;

        return $this;
    }

    public function facility(int $facility): self
    {
        $this->parts[] = 'q.facility = :facility';
        $this->parameters['facility'] = $facility;

        return $this;
    }
}
