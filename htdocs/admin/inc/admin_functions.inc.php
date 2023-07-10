<?PHP

/**
 * Parse value submitted by datepicker field
 */
function parseDatePicker(string $value): int
{
    try {
        $dt = new DateTime($value);
        return $dt->getTimestamp();
    } catch (Exception) {
        return 0;
    }
}

