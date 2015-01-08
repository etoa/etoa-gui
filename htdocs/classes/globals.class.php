<?php

/**
 * The only reason this class exists is to help remove the usage of the global keyword.
 * This class should not(!) be used for any new code.
 */
class Globals
{
	/**
	 * @return string[]
	 */
	public static function getResNames()
	{
		return [
			RES_METAL,
			RES_CRYSTAL,
			RES_PLASTIC,
			RES_FUEL,
			RES_FOOD,
		];
	}
}
