<?php

declare(strict_types=1);

namespace EtoA\Admin;

class AdminRoleManager
{
	private static $roles;

	public function __construct()
	{
		if (self::$roles == null) {
			$securityConfig = fetchJsonConfig("admin-security.conf");
			self::$roles = $securityConfig['roles'];
		}
	}

	function getRoleName($name)
	{
		return self::$roles[$name];
	}

	function getRolesStr($roles)
	{
		$rs = array();
		foreach ($roles as $r) {
			$rs[] = $this->getRoleName($r);
		}
		return implode(', ', $rs);
	}

	function getRoles()
	{
		return self::$roles;
	}

	function checkAllowed($rolesToCheck, $allowedRoles)
	{
		if (!is_array($rolesToCheck)) {
			$rolesToCheck = explode(",", $rolesToCheck);
		}
		if (!is_array($allowedRoles)) {
			$allowedRoles = explode(",", $allowedRoles);
		}
		return count(array_intersect($rolesToCheck, $allowedRoles)) > 0;
	}
}
