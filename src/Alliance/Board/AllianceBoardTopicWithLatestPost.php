<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\Entity\AllianceBoardPost;
use EtoA\Entity\AllianceBoardTopic;

class AllianceBoardTopicWithLatestPost extends AllianceBoardTopic
{
    public AllianceBoardPost $post;

    public function __construct(array $data)
    {
        $this->post = new AllianceBoardPost($data);
    }
}
