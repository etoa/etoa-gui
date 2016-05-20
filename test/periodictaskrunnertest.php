<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="utf-8">
	<style type="text/css">
		body {
			font-family: sans-serif;
		}
		table {
			border-collapse: collapse;
		}
		td, th {
			border: 1px solid #aaa;
			padding: 3px;
		}
		.success {
			color: #0a0;
		}
		.error {
			color: #a00;
		}
	</style>
</head>
<body>
<?PHP
	chdir(realpath(dirname(__FILE__)."/../htdocs/"));
	require_once("inc/bootstrap.inc.php");

	function testSchedule($sched, $time, $succeed = true) {
		echo "<tr>";
		echo "<td><code>$sched</code></td><td>$time</td>";
		if ($succeed) {
			echo "<td>OK</td>";
		} else {
			echo "<td>FAILED</td>";
		}
		$result = PeriodicTaskRunner::shouldRun($sched, strtotime($time));
		if ($result && $succeed || !$result && !$succeed) {
			$class = 'success';
		} else {
			$class = 'error';
		}
		if ($result) {
			echo "<td class=\"$class\">OK</td>";
		} else {
			echo "<td class=\"$class\">FAILED</td>";
		}
		echo "</tr>";
	}
	
	echo "<table>";
	echo "<tr><th>Schedule</th><th>Date</th><th>Expected</th><th>Result</th></tr>";
	testSchedule('* * * * *', '22.12.1979 17:30', true);
	testSchedule('* * * *', '22.12.1979 17:30', false);
	testSchedule('* * * * * asdkfjjaksjdkf', '22.12.1979 17:30', true);
	testSchedule('* * * * *', '18.10.2014 10:05', true);
	
	testSchedule('4 * * * *', '18.10.2014 10:05', false);
	testSchedule('5 * * * *', '18.10.2014 10:05', true);
	testSchedule('6 * * * *', '18.10.2014 10:05', false);
	testSchedule('5 9 * * *', '18.10.2014 10:05', false);
	testSchedule('5 10 * * *', '18.10.2014 10:05', true);
	testSchedule('4 10 * * *', '18.10.2014 10:05', false);
	testSchedule('* 10 * * *', '18.10.2014 10:05', true);
	testSchedule('* * 17 * *', '18.10.2014 10:05', false);
	testSchedule('* * 18 * *', '18.10.2014 10:05', true);
	testSchedule('* * * 9 *', '18.10.2014 10:05', false);
	testSchedule('* * * 10 *', '18.10.2014 10:05', true);
	testSchedule('* * 18 10 *', '18.10.2014 10:05', true);
	testSchedule('* * * * 5', '18.10.2014 10:05', false);
	testSchedule('* * * * 6', '18.10.2014 10:05', true);
	testSchedule('* * * * 0', '19.10.2014 10:05', true);
	testSchedule('* * * * 7', '19.10.2014 10:05', true);
	testSchedule('* * * * 8', '20.10.2014 10:05', false);
	
	testSchedule('4,7 * * * *', '18.10.2014 10:05', false);
	testSchedule('5,6,7 * * * *', '18.10.2014 10:05', true);
	testSchedule('5 10 * * 6,7', '17.10.2014 10:05', false);
	testSchedule('5 10 * * 6,7', '18.10.2014 10:05', true);
	testSchedule('5 10 * * 6,7', '19.10.2014 10:05', true);
	
	testSchedule('*/5 * * * *', '18.10.2014 10:05', true);
	testSchedule('*/5 * * * *', '18.10.2014 10:15', true);
	testSchedule('*/3 * * * *', '18.10.2014 10:00', true);
	testSchedule('*/3 * * * *', '18.10.2014 10:03', true);
	testSchedule('*/3 * * * *', '18.10.2014 10:14', false);
	testSchedule('*/3 * * * *', '18.10.2014 10:15', true);
	
	testSchedule('10-20 * * * *', '18.10.2014 10:15', true);
	testSchedule('7-14 * * * *', '18.10.2014 10:15', false);
	
	testSchedule('@weekly', '19.10.2014 0:00', true);
	
	// Jeden Tag um zehn Minuten nach Mitternacht
	testSchedule('10 0 * * *', '18.10.2014 00:10', true);

	// Jeden Mittwoch um zehn Minuten nach Mitternacht
	testSchedule('10 0 * * 3', '15.10.2014 00:10', true);
 
	// Jeden Wochentag um zehn Minuten 13,14 und 15 Uhr
	testSchedule('10 13-15 * * 1-5', '17.10.2014 13:10', true);
	
	// Jedes Jahr zu Silvester
	testSchedule('0 0 31 12 *', '31.12.2014 0:0', true);

	// alle 15 Minuten zwischen 4 und 16 Uhr aber nur Samstags und Sonntags:
	testSchedule('*/15 4-16 * * 6,7', '18.10.2014 10:15', true);
	testSchedule('*/15 4-16 * * 6,7', '17.10.2014 10:15', false);
	testSchedule('*/15 4-16 * * 6,7', '18.10.2014 17:15', false);
	testSchedule('*/15 4-16 * * 6,7', '18.10.2014 10:16', false);
 	
	echo "</table>";

?>
</body>
</html>