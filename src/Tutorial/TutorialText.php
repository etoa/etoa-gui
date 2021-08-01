<?php

declare(strict_types=1);

namespace EtoA\Tutorial;

class TutorialText
{
    public int $id;
    public int $tutorialId;
    public string $title;
    public string $content;
    public int $step = 0;
    public ?int $prev = null;
    public ?int $next = null;

    public static function createFromArray(array $data): TutorialText
    {
        $text = new TutorialText();
        $text->id = (int) $data['text_id'];
        $text->tutorialId = (int) $data['text_tutorial_id'];
        $text->title = $data['text_title'];
        $text->content = $data['text_content'];
        $text->step = (int) $data['text_step'];

        return $text;
    }
}
