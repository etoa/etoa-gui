<?PHP
interface IPeriodicTask
{
    function run();
    public static function getDescription();
}
