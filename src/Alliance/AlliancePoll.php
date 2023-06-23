<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AlliancePoll
{
    public int $id;
    public int $allianceId;
    public string $title;
    public string $question;
    public int $timestamp;
    public ?string $answer1;
    public ?string $answer2;
    public ?string $answer3;
    public ?string $answer4;
    public ?string $answer5;
    public ?string $answer6;
    public ?string $answer7;
    public ?string $answer8;
    public int $answer1Count;
    public int $answer2Count;
    public int $answer3Count;
    public int $answer4Count;
    public int $answer5Count;
    public int $answer6Count;
    public int $answer7Count;
    public int $answer8Count;
    public bool $active;

    public function __construct(array $data)
    {
        $this->id = (int) $data['poll_id'];
        $this->allianceId = (int) $data['poll_alliance_id'];
        $this->title = $data['poll_title'];
        $this->question = $data['poll_question'];
        $this->timestamp = (int) $data['poll_timestamp'];
        $this->answer1 = $data['poll_a1_text'];
        $this->answer2 = $data['poll_a2_text'];
        $this->answer3 = $data['poll_a3_text'];
        $this->answer4 = $data['poll_a4_text'];
        $this->answer5 = $data['poll_a5_text'];
        $this->answer6 = $data['poll_a6_text'];
        $this->answer7 = $data['poll_a7_text'];
        $this->answer8 = $data['poll_a8_text'];
        $this->answer1Count = (int) $data['poll_a1_count'];
        $this->answer2Count = (int) $data['poll_a2_count'];
        $this->answer3Count = (int) $data['poll_a3_count'];
        $this->answer4Count = (int) $data['poll_a4_count'];
        $this->answer5Count = (int) $data['poll_a5_count'];
        $this->answer6Count = (int) $data['poll_a6_count'];
        $this->answer7Count = (int) $data['poll_a7_count'];
        $this->answer8Count = (int) $data['poll_a8_count'];
        $this->active = (bool) $data['poll_active'];
    }
}
