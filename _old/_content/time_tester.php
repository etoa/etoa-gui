<?php
echo "<a href=\"?page=time_tester\">Nochmal</a><br>";

echo "<br>Test welches echo schneller ist, mit \"text\" oder 'text'<br><br>";
$start1 = microtime();
for ($i = 0; $i < 10000; $i++) { $test = "Dies ist ein Test $i"; }
$ende1 = microtime();
echo "Verbrauchte Zeit mit \" : ".($ende1 - $start1);

$start2 = microtime();
for ($i = 0; $i < 10000; $i++) { $test = 'Dies ist ein Test'.$i; }
$ende2 = microtime();
echo "<br>Verbrauchte Zeit mit ' : ".($ende2 - $start2);


echo "<br><br><br>Mysql Test<br>";
$start3 = microtime();
for ($i = 0; $i < 1; $i++)
{
	$res = mysql_query("SELECT * FROM ".$db_table['planets'].";");
}
$ende3 = microtime();

echo "<br>Verbrauchte Zeit mit radikaler Auslesung (SELECT * FROM): ".($ende3 - $start3);
$start4 = microtime();
for ($i = 0; $i < 10; $i++)
{
	$res = mysql_query("SELECT planet_id FROM ".$db_table['planets'].";");
}
$ende4 = microtime();
echo "<br>Verbrauchte Zeit mit rationioneller Auslesung ( ".$i."x SELECT xy FROM): ".($ende4 - $start4);
?>