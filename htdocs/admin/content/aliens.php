<?php 
  echo "<h1>Alienconfig</h1>";
  $sql = 'select * from alien_class';
  while($arr = mysql_fetch_assoc($sql))
  {
    echo 'muh';
  }

?>