<?php declare(strict_types=1);

namespace EtoA\Admin;

class AdminNote
{
    public int $id;
    public int $adminId;
    public string $title;
    public string $text;
    public int $date;

    public function __construct(array $data)
    {
        $this->id = (int) $data['notes_id'];
        $this->adminId = (int) $data['admin_id'];
        $this->title = $data['titel'];
        $this->text = $data['text'];
        $this->date = (int) $data['date'];
    }
}
