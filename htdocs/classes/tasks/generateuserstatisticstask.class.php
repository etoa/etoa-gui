<?PHP

/**
 * Update user statistics
 */
class GenerateUserStatisticsTask implements IPeriodicTask
{
    function run()
    {
        $rres = dbquery("SELECT COUNT(user_id) FROM users;");
        $rarr = mysql_fetch_row($rres);
        $gres = dbquery("SELECT COUNT(user_id) FROM user_sessions;");
        $garr = mysql_fetch_row($gres);
        dbquery("INSERT INTO
			user_onlinestats (
				stats_timestamp,
				stats_count,
				stats_regcount
			) VALUES (
				UNIX_TIMESTAMP(),
				" . $garr[0] . ",
				" . $rarr[0] . ")
			;");
        UserStats::generateImage(USERSTATS_OUTFILE);
        UserStats::generateXml(XML_INFO_FILE);
        return "User-Statistik: " . $garr[0] . " User online, " . $rarr[0] . " User registriert";
    }

    function getDescription()
    {
        return "User Statistik aktualisieren";
    }
}
