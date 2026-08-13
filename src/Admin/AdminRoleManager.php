<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Entity\AdminUser;
use EtoA\Support\FileUtils;

class AdminRoleManager
{
    /** @var array<string, string>|null */
    private static ?array $roles = null;

    public function __construct(
        private readonly FileUtils $fileUtils,
    )
    {
    }

    public function getRoleName(string $name): string
    {
        return $this->getRoles()[$name];
    }

    public function getRolesStr(AdminUser $user): string
    {
        $rs = array();
        foreach ($user->getRoles() as $role) {
            $rs[] = $this->getRoleName($role);
        }

        return implode(', ', $rs);
    }

    /**
     * @return array<string,string>
     */
    public function getRoles(): array
    {
        // read once per process, the config does not change at runtime
        if (self::$roles === null) {
            self::$roles = $this->fileUtils->fetchJsonConfig("admin-security.json")['roles'];
        }

        return self::$roles;
    }

    /**
     * @param string|string[] $rolesToCheck
     */
    public function checkAllowed(AdminUser $user, $rolesToCheck): bool
    {
        return $this->checkAllowedRoles($user->getRoles(), $rolesToCheck);
    }

    /**
     * @param string[] $userRoles
     * @param string|string[] $rolesToCheck
     */
    public function checkAllowedRoles(array $userRoles, $rolesToCheck): bool
    {
        if (!is_array($rolesToCheck)) {
            $rolesToCheck = explode(",", $rolesToCheck);
        }

        return count(array_intersect($rolesToCheck, $userRoles)) > 0;
    }
}
