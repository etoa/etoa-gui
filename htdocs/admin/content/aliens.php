<?php

  $exo_id = mysql_query("select type_id from planet_types where type_name='Exoplanet'");
  $planets = mysql_num_rows(mysql_query("SELECT * FROM planets WHERE planet_type_id=".$exo_id));
   
  echo "<h1>Alienconfig</h1>";
  $sql = dbquery('select * from alien_class');
  
  if (isset($_POST['save']))
  {  
    mysql_query("insert into alien_class (alien_class_name) values('".$_POST['name']."')"); 
  };
  
  if (isset($_POST['save_planets']))
  {  
    if($_POST['planets']>$planets)
    {
      for($x=1;$x <($_POST['planets']-$planets);$x++)
      {
         mysql_query("update planets set type_id =".$exo_id." WHERE id = (SELECT id FROM planets where planet_user_id=0 limit 1); "); 
      }
    }
    else
    {
      
    }  
  }
  
  echo'<form method="post">';
  tableStart('Vorhandene Alienklassen');
  echo'<tr><td>Klassenname</td><td>Flottenpunkte von</td><td>Flottenpunkte bis</td></tr>';
   
  while($arr = mysql_fetch_assoc($sql))
  {  
    echo "<tr><td>".$arr['alien_class_name']."</td><td>".$arr['alien_class_name']."</td><td>".$arr['alien_class_name']."</td></tr>"; 
  }
  tableEnd();
  tableStart('Neue Alienklasse anlegen');
  echo'<p>';
  echo'<tr><td>Klassenname</td><td>Flottenpunkte von</td><td>Flottenpunkte bis</td></tr>';
  echo'<tr><td><input type="text" name="name"><td><input type="number" name="min"></td><td><input type="number" name="max"></td></tr>';
  echo'</p><br>';
  tableEnd();
  echo'<br><input type="submit" value="Speichern" name="save">';
  echo' &nbsp<input type="submit" value="Aliens generieren" name="create">';
  echo'<p><br>';
  echo'Vorhandene Exoplanten: '.$planets;
  echo'Neue Anzahl: <input type="number" name="planets"><br>';
  echo'<input type="submit" value="Planeten speichern" name="save_planets">';
  echo'</p>';
  
  echo'</form>'
    
?>
