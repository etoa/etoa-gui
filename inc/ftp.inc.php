<?PHP


$file="etoatest-2007-06-14-04-47.sql.gz";
$destination_file="sql_test/$file";
$source_file="/home/etoa/test/backup/$file";

$ftp_server="88.198.42.35";
$ftp_user="13274";
$ftp_pw="NZnYtarU";

function put_ftp($ftp_server,$ftp_user,$ftp_pw,$file,$dest_path,$src_path)
{
$conn_id = ftp_connect($ftp_server); 
$login_result = ftp_login($conn_id, $ftp_user, $ftp_pw); 

// Verbindung überprüfen
if ((!$conn_id) || (!$login_result)) 
{
        echo "FTP Verbindung ist fehlgeschlagen!";
        echo "Verbindungasufbau zu $ftp_server mit Username $ftp_user_name versucht.";
        exit;
} 
else 
{
       echo "Verbunden zu $ftp_server mit Username $ftp_user_name";
}

// Datei hochladen
$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

// Upload überprüfen
if (!$upload) 
{
	echo "FTP-Upload ist fehlgeschlagen!";
} 
else 
{
       echo "Datei $source_file auf Server $ftp_server als $destination_file hochgeladen";
}

// FTP Verbidung schließen
ftp_close($conn_id);
}

?>
