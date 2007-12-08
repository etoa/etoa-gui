<?PHP
class Bookmarks
{
	private $items;
	
	function Bookmarks($userId)
	{
		$this->items = array();
		$pres = dbquery("
		SELECT
			bookmark_id,
			bookmark_comment,
			bookmark_planet_id,
			bookmark_cell_id
		FROM
    	target_bookmarks
		WHERE
	    target_bookmarks.bookmark_user_id=".$userId."
	    AND target_bookmarks.bookmark_planet_id=0
		ORDER BY
			bookmark_cell_id ASC;");
		if (mysql_num_rows($pres)>0)
		{
			while($parr = mysql_fetch_array($pres))
			{
				array_push($this->items,new Bookmark($parr));
			}
		}
	}
	
	function __toString()
	{
		return $this->items;
	}
}
?>