<?php

declare(strict_types=1);

namespace EtoA\Admin;

class AdminRoleManager
{
    /** @var array<string, string> */
    private static $roles;

    public function __construct()
    {
        if (self::$roles === null) {
            $securityConfig = fetchJsonConfig("admin-security.conf");
            self::$roles = $securityConfig['roles'];
        }
    }

    public function getRoleName(string $name): string
    {
        return self::$roles[$name];
    }

    public function getRolesStr(AdminUser $user): string
    {
        $rs = array();
        foreach ($user->roles as $role) {
            $rs[] = $this->getRoleName($role);
        }

        return implode(', ', $rs);
    }

    /**
     * @return array<string,string>
     */
    public function getRoles(): array
    {
        return self::$roles;
    }

    /**
     * @param string|string[] $rolesToCheck
     */
    public function checkAllowed(AdminUser $user, $rolesToCheck): bool
    {
        return $this->checkAllowedRoles($user->roles, $rolesToCheck);
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
