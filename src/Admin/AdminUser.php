<?php

declare(strict_types=1);

namespace EtoA\Admin;

class AdminUser
{
	public ?int $id = null;
	public ?string $passwordString;
	public bool $forcePasswordChange = false;
	public string $nick = "";
	public string $name = "";
	public string $email = "";
	public string $tfaSecret = "";
	public int $playerId = 0;
	public string $boardUrl = "";
	public string $userTheme = "";
	public bool $ticketEmail = false;
	public bool $locked = false;
	public bool $isContact = true;
	public array $roles = [];

	function __toString()
	{
		return "[ADMIN]" . $this->nick;
	}

	function checkEqualPassword($newPassword)
	{
		return validatePasswort($newPassword, $this->passwordString);
	}

	function getRolesStr()
	{
		$rm = new AdminRoleManager();
		return $rm->getRolesStr($this->roles);
	}

	function hasRole($roles)
	{
		$rm = new AdminRoleManager();
		return ($rm->checkAllowed($roles, $this->roles));
	}
}
