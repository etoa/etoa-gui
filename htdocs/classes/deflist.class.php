<?PHP

class DefList
{
    /**
     * Remove empty data
     */
    static function cleanUp()
    {
        dbquery("DELETE FROM
                        `deflist`
                    WHERE
                        `deflist_count`='0'
                        ;");
        $nr = mysql_affected_rows();
        Log::add("4", Log::INFO, "$nr leere Verteidigungsdatensätze wurden gelöscht!");
        return $nr;
    }
}
