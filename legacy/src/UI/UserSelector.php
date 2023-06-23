<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\User\UserRepository;

class UserSelector
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getHTML(string $name, int $userId = 0, bool $showEmptyOption = true): string
    {
        $str = "<select name=\"" . $name . "\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\" style=\"font-style:italic\">(niemand)</option>";
        }
        foreach ($this->userRepository->searchUserNicknames() as $id => $label) {
            $str .= "<option value=\"$id\"";
            if ($id === $userId) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">" . $label . "</option>";
        }
        $str .= "</select>";

        return $str;
    }
}
