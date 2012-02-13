<?PHP
	echo "<h1>Admin-Liste</h1>";
		
	$res = dbquery("
	SELECT
		user_id,
		user_nick,
		user_name,
		group_name,
		user_email,
		user_board_url
	FROM
		admin_users
	LEFT JOIN
		admin_groups
		ON user_admin_rank=group_id
	ORDER BY
		user_nick ASC
	");
	echo "<table class=\"tb\">
	<tr>
		<th>Nick</th>
		<th>Name</th>
		<th>E-Mail</th>
		<th>Gruppe</th>
		<th>Foren-Profil</th>
	</tr>";
	while ($arr=mysql_fetch_Array($res))
	{
		echo "<tr>
			<td>".$arr['user_nick']."</td>
			<td>".$arr['user_name']."</td>
			<td><a href=\"mailto:".$arr['user_email']."\">".$arr['user_email']."</a></td>
			<td>".$arr['group_name']."</td>
			<td>".($arr['user_board_url'] ? "<a href=\"".$arr['user_board_url']."\" target=\"_blank\">Profil</a>" : "")."</td>
		</tr>";
	}		
	echo "</table><br/> ";
?>