<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

class Ticket
{
	public int $id = 0;
	public string $solution;
	public string $status;
	public int $catId;
	public int $userId;
	public ?int $adminId = null;
	public int $timestamp;
	public ?string $adminComment = null;

	/**
	 * Workflow status of ticket
	 */
	const STATUS_ITEMS = [
		"new" => "Neu",
		"assigned" => "Zugeteilt",
		"closed" => "Abgeschlossen",
	];

	/**
	 * Solution type
	 */
	const SOLUTION_ITEMS = [
		"open" => "Offen",
		"solved" => "Behoben",
		"duplicate" => "Duplikat",
		"invalid" => "Ungültig",
	];

	public function getIdString(): string
	{
		return "#" . sprintf("%'.06d", $this->id);
	}

	public function getStatusName(): string
	{
		if ($this->status == "closed" && isset(self::SOLUTION_ITEMS[$this->solution])) {
			return self::STATUS_ITEMS[$this->status] . ": " . self::SOLUTION_ITEMS[$this->solution];
		}
		return self::STATUS_ITEMS[$this->status];
	}
}
