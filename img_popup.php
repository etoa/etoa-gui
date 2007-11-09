<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: img_popup.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.03.2006
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Pop-Up window for images
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	
?>
<?PHP echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
';?>
<html xmlns="http://www.w3.org/1999/xhtml">	
	<head>
		<title>Bildanzeige</title>
		<link rel="stylesheet" type="text/css" href="general.css" />		
		<script type="text/javascript">
			function resizePopup()
			{
				if (document.all)
				{
					window.resizeTo(document.all.imageid.width+40,document.all.imageid.height+100)
				}
				else
				{
					window.resizeTo(document.getElementById('imageid').width+40,document.getElementById('imageid').height+100)
				}
			}	
			
			
		</script>
	</head>
	<body class="img_popup" style="text-align:center;margin:5px;background:#000;" onload="resizePopup()">
		<div style="margin:0px auto;text-align:center;padding-top:0px;">
			<?PHP
				if(isset($_GET['image_url']) && $_GET['image_url']!="")
				{
					echo "<img src=\"".$_GET['image_url']."\" alt=\"Bild\" id=\"imageid\" />";
				}		
			?>
			<br/><br/><input type="button" value="Schliessen" onclick="window.close();" />
		</div>	
	</body>
</html>
