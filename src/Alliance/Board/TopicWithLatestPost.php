<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

class TopicWithLatestPost extends Topic
{
    public Post $post;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->post = new Post($data);
    }
}
