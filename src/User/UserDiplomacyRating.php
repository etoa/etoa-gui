<?php declare(strict_types=1);

namespace EtoA\User;

class UserDiplomacyRating extends UserRating
{
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->rating = (int) $data['diplomacy_rating'];
    }
}
