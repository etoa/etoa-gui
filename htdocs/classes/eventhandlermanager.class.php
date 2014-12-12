<?php
class EventHandlerManager
{
	public static function checkDaemonRunning($pidfile)
	{
		if ($fh = @fopen($pidfile,"r"))
		{
			$pid = intval(fread($fh,50));
			fclose($fh);
			if ($pid > 0)
			{
				$cmd = "ps $pid";
				exec($cmd, $output);
				if(count($output) >= 2)
				{
					return $pid;   
				}
			}
		}
		return false;
	}
	
	public static function start($executable, $instance, $pidfile)
	{
		$cmd = $executable." ".$instance." -d -c /etc/etoad/instances-enabled/".$instance.".conf -p ".$pidfile;
		exec($cmd, $output);
		return $output;
	}
	
	public static function stop($executable, $instance, $pidfile)
	{
		$cmd = $executable." ".$instance." -d -s -c /etc/etoad/instances-enabled/".$instance.".conf -p ".$pidfile;
		exec($cmd, $output);
		return $output;
	}
}
?>