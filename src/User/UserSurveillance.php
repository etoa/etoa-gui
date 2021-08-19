<?php declare(strict_types=1);

namespace EtoA\User;

class UserSurveillance
{
    public int $id;
    public int $timestamp;
    public int $userId;
    public string $page;
    public string $request;
    public string $requestRaw;
    public string $post;
    public string $session;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->timestamp = (int) $data['timetstamp'];
        $this->userId = (int) $data['user_id'];
        $this->page = $data['page'];
        $this->request = $data['request'];
        $this->requestRaw = $data['request_raw'];
        $this->post = $data['post'];
        $this->session = $data['session'];
    }
}
