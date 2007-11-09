<?PHP

		if (isset($_GET['error']) && md5(base64_decode($_GET['error']))==$_GET['hash'])
		{
			echo 'Error: '.base64_decode($_GET['error']).'<br/><br/>';
		}

		echo "<form action=\".\" method=\"post\">";
		echo "Nick: <input type=\"text\" name=\"login_nick\" />";
		echo "Password: <input type=\"password\" name=\"login_password\" />";
		echo "<input type=\"submit\" name=\"login_submit\" value=\"Login\" />";
		echo "</form>";

?>