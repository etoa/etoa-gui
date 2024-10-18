<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

class Topic
{
    public int $id;
    public int $categoryId;
    public int $bndId;
    public int $userId;
    public string $subject;
    public int $count;
    public int $timestamp;
    public bool $top;
    public bool $closed;

    public function __construct(array $data)
    {
        $this->id = (int) $data['topic_id'];
        $this->categoryId = (int) $data['topic_cat_id'];
        $this->bndId = (int) $data['topic_bnd_id'];
        $this->userId = (int) $data['topic_user_id'];
        $this->subject = $data['topic_subject'];
        $this->count = (int) $data['topic_count'];
        $this->timestamp = (int) $data['topic_timestamp'];
        $this->top = (bool) $data['topic_top'];
        $this->closed = (bool) $data['topic_closed'];
    }

    public function getTopicId(): int
    {
        return $this->id;
    }

    public function setTopicId(int $id): void
    {
        $this->id = $id;
    }

    public function getTopicCatId(): int
    {
        return $this->categoryId;
    }

    public function setTopicCatId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getTopicBndId(): int
    {
        return $this->bndId;
    }

    public function setTopicBndId(int $bndId): void
    {
        $this->bndId = $bndId;
    }

    public function getTopicUserId(): int
    {
        return $this->userId;
    }

    public function setTopicUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTopicSubject(): string
    {
        return $this->subject;
    }

    public function setTopicSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getTopicCount(): int
    {
        return $this->count;
    }

    public function setTopicCount(int $count): void
    {
        $this->count = $count;
    }

    public function getTopicTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTopicTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function isTopicTop(): bool
    {
        return $this->top;
    }

    public function setTopicTop(bool $top): void
    {
        $this->top = $top;
    }

    public function isTopicClosed(): bool
    {
        return $this->closed;
    }

    public function setTopicClosed(bool $closed): void
    {
        $this->closed = $closed;
    }
}
