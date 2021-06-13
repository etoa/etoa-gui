<?php

declare(strict_types=1);

namespace EtoA\Text;

class Text
{
	public $id;
	public $label;
	public $description;
	public $content;
	public $updated;
	public $enabled = true;
	public $isOriginal = true;

	public function __construct($id, $content)
	{
		$this->id = $id;
		$this->content = $content;
	}
}
