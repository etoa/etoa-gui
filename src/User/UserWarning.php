<?php declare(strict_types=1);

namespace EtoA\User;

class UserWarning
{
    public int $id;
    public int $userId;
    public int $date;
    public string $text;
    public int $adminId;
    public ?string $adminNick;

    public function __construct(array $data)
    {
        $this->id = (int) $data['warning_id'];
        $this->userId = (int) $data['warning_user_id'];
        $this->date = (int) $data['warning_date'];
        $this->text = $data['warning_text'];
        $this->adminId = (int) $data['warning_admin_id'];
        $this->adminNick = $data['admin_user_nick'];
    }
}
