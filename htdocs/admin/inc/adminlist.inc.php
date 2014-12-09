<?PHP
	echo "<h1>Admin-Liste</h1>";
		
	echo "<table class=\"tb\">
	<tr>
		<th>Nick</th>
		<th>Name</th>
		<th>E-Mail</th>
		<th>Gruppe</th>
		<th>Foren-Profil</th>
	</tr>";
	foreach (AdminUser::getAll() as $arr) {
		echo "<tr>
			<td>".$arr->nick."</td>
			<td>".$arr->name."</td>
			<td><a href=\"mailto:".$arr->email."\">".$arr->email."</a></td>
			<td>".$arr->getRolesStr()."</td>
			<td>".($arr->boardUrl ? "<a href=\"".$arr->boardUrl."\" target=\"_blank\">Profil</a>" : "")."</td>
		</tr>";
	}		
	echo "</table><br/> ";
?>