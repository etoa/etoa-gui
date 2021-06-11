<?php
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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
    
    // Datenänderung übernehmen
  if (isset($_POST['data_submit']) && $_POST['data_submit']!="" && checker_verify())
  { 
    if (checkEmail($_POST['dual_email']))
    {
      if (isset($_POST['dual_name']))
        $cu->d_realName = $_POST['dual_name'];
      if (isset($_POST['dual_email']))
        $cu->d_email = $_POST['dual_email']; 
    }
    else
      echo "<b>Fehler!</b> Die E-Mail-Adresse ist nicht korrekt!<br/><br/>"; 
  }
  
    echo "<form action=\"?page=$page&mode=dual\" method=\"post\" enctype=\"multipart/form-data\">";
    $cstr = checker_init();
    tableStart("Spieloptionen");
    
    if ($cu->d_realName == trim(''))
    echo 
      "<tr>
      	<th width=\"35%\">Vollst&auml;ndiger Name:</th> 
        <td width=\"65%\"><input type=\"text\" name=\"dual_name\" maxlength=\"255\" size=\"30\" value=\"\"> [".ticketLink("&Auml;nderung beantragen",18)."]</td>
       </tr>";
    else
    echo 
      "<tr>
      	<th width=\"35%\">Vollst&auml;ndiger Name:</th> 
        <td width=\"65%\">".$cu->d_realName." [".ticketLink("&Auml;nderung beantragen",18)."]</td>
       </tr>";
       
    if ($cu->d_email == trim(''))     
    echo 
      "<tr>
      	<th width=\"35%\">E-Mail:</th>
        <td width=\"65%\"><input type=\"text\" name=\"dual_email\" maxlength=\"255\" size=\"30\" value=\"\"> [".ticketLink("&Auml;nderung beantragen",18)."]</td>
      </tr>";   
    else 
    echo     
      "<tr>
      	<th width=\"35%\">E-Mail:</th>
        <td width=\"65%\">".$cu->d_email." [".ticketLink("&Auml;nderung beantragen",18)."]</td>
      </tr>";   
    
    
    tableEnd();
    echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
    echo "</form><br/><br/>";    
   
?>
