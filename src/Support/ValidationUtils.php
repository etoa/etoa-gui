<?php

namespace EtoA\Support;

use Countable;
use EtoA\Entity\User;

class ValidationUtils
{
    /**
     * Checks for a valid mail address
     */
    public static function checkEmail($email): bool|int
    {
        return preg_match('/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/', $email);
    }

    /**
     * Checks vor a valid name
     */
    public static function checkValidName($name): bool|int
    {
        return preg_match(User::NAME_PATTERN, $name);
    }

    /**
     * Checks for a valid nick
     */
    public static function checkValidNick($name): bool|int
    {
        return preg_match(User::NICK_PATTERN, $name);
    }

    /**
     * Determine if the given value is "blank".
     *
     * @param mixed $value
     * @return bool
     * @see https://github.com/illuminate/support/blob/master/helpers.php
     */
    public static function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }

    /**
     * Determine if a value is "filled".
     *
     * @param mixed $value
     * @return bool
     * @see https://github.com/illuminate/support/blob/master/helpers.php
     */
    public static function filled(mixed $value): bool
    {
        return !self::blank($value);
    }
}