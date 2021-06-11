<?PHP
	session_start();
?>
<html>
	<head>
		<title>Frameset</title>
	</head>
	<frameset cols="*,220" frameborder="0">
		<?PHP
			$idx = "index.php?page=".$_GET['page']."&amp;sub=".$_GET['sub'];

			$_SESSION['clipboard']=1;

			echo "<frame src=\"".$idx."\" border=\"0\" name=\"main\" />";
		?>
		<frame src="clipboard.php" border="0" name="clipboard" />
	</frameset>
</html>
