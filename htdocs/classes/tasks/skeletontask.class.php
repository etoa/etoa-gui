<?PHP

/**
 * USE AS AN EXAMPLE
 */
class SkeletonTask implements IPeriodicTask
{
    function run()
    {
        return "Done";
    }

    public static function getDescription()
    {
        return "";
    }
}
