<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceNews
{
    public int $id;
    public string $title;
    public string $text;
    public int $date;
    public int $authorAllianceId;
    public ?string $authorAllianceTag;
    public ?string $authorAllianceName;
    public ?int $authorUserId;
    public ?string $authorUserNick;
    public int $toAllianceId;
    public ?string $toAllianceTag;
    public ?string $toAllianceName;

    public function __construct(array $data)
    {
        $this->id = (int) $data['alliance_news_id'];
        $this->title = $data['alliance_news_title'];
        $this->text = $data['alliance_news_text'];
        $this->date = (int) $data['alliance_news_date'];
        $this->authorAllianceId = (int) $data['alliance_news_alliance_id'];
        $this->authorAllianceTag = $data['alliance_tag'];
        $this->authorAllianceName = $data['alliance_name'];
        $this->authorUserId = (int) $data['user_id'];
        $this->authorUserNick = $data['user_nick'];
        $this->toAllianceId = (int) $data['alliance_news_alliance_to_id'];
        $this->toAllianceTag = $data['to_alliance_tag'];
        $this->toAllianceName = $data['to_alliance_name'];
    }
}
