<?PHP

	include("image.inc.php");
	
	$w = 468;
	$h = 60;
	$im = imagecreatetruecolor($w,$h);
	$im = imagecreatefrompng("images/userbanner/userbanner1.png");
	
	if (!isset($_GET['save']))
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1	
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit	
		header ("Content-type: image/png");
	}
	$colBlack = imagecolorallocate($im,0,0,0);
	$colGrey = imagecolorallocate($im,120,120,120);
	$colYellow = imagecolorallocate($im,255,255,0);
	$colOrange = imagecolorallocate($im,255,100,0);
	$colWhite = imagecolorallocate($im,255,255,255);
	$colGreen = imagecolorallocate($im,0,255,0);
	$colBlue = imagecolorallocate($im,150,150,240);
	$colViolett = imagecolorallocate($im,200,0,200);
	$colRe = imagecolorallocate($im,200,0,200);
	
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if ($id > 0)
	{
		$res=dbquery("
		SELECT
			u.user_nick,
			a.alliance_name,
			a.alliance_tag,
			r.race_name,
			u.user_points
		FROM
			users u
		LEFT JOIN
			alliances a
			ON u.user_alliance_id=a.alliance_id
		LEFT JOIN
			races r
			On u.user_race_id=r.race_id
		WHERE
			user_id=".$id."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			
			$font = "images/userbanner/calibri.ttf";
			
			$nsize = imagettfbbox(16,0,$font,$arr[0]);
			
			ImageTTFText ($im, 16, 0, 6, 21, $colBlack, $font,$arr[0]);
			ImageTTFText ($im, 16, 0, 5, 20, $colWhite, $font,$arr[0]);
			ImageTTFText ($im, 11, 0, $nsize[2]-$nsize[0] + 16, 21, $colBlack, $font,$arr[3]);
			ImageTTFText ($im, 11, 0, $nsize[2]-$nsize[0] + 15, 20, $colWhite, $font,$arr[3]);
			
			if ($arr[2]!="")
			{
				ImageTTFText ($im, 9, 0, 9, 39, $colBlack, $font,"<".$arr[2]."> ".$arr[1]);
				ImageTTFText ($im, 9, 0, 8, 38, $colWhite, $font,"<".$arr[2]."> ".$arr[1]);
			}
			ImageTTFText ($im, 9, 0, 9, 54, $colBlack, $font,ROUNDID."  -  ".nf($arr[4])." Punkte");
			ImageTTFText ($im, 9, 0, 8, 53, $colWhite, $font,ROUNDID."  -  ".nf($arr[4])." Punkte");
		}
		else
		{
			imagestring($im,10,10,3,"Benutzer $id nicht vorhanden!",$colWhite);
		}
	}
	if (isset($_GET['save']))
		imagepng($im,"../cache/userbanner/".md5("user".$id).".png");
	else
		imagepng($im);
	

?>