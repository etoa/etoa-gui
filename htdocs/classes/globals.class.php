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

	/**
	 * @return string[]
	 */
	public static function getResIcons()
	{
		return [
			RES_ICON_METAL,
			RES_ICON_CRYSTAL,
			RES_ICON_PLASTIC,
			RES_ICON_FUEL,
			RES_ICON_FOOD,
		];
	}
}
