<?php declare(strict_types=1);

namespace EtoA\Alliance;

class UserAlliancePermission
{
    private bool $isFounder;
    /** @var string[] */
    private array $rights;

    /**
     * @param string[] $rights
     */
    public function __construct(bool $isFounder, array $rights)
    {
        $this->isFounder = $isFounder;
        $this->rights = $rights;
    }

    /**
     * @param AllianceRights::* $action
     */
    public function checkHasRights(string $action, string $page): bool
    {
        if ($this->hasRights($action)) {
            return true;
        }

        error_msg("Keine Berechtigung!");
        echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";

        return false;
    }

    /**
     * @param AllianceRights::* $action
     */
    public function hasRights(string $action): bool
    {
        return $this->isFounder || in_array($action, $this->rights, true);
    }
}
