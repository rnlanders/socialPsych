<?
	require('functionlib.php');
	require('config.php');

	$query = "UPDATE `certifiedStudents` SET `lastTested`=NOW(),`currentTesting`=NULL WHERE (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`currentTesting`)) > 720";
	$result = myquery($query);
	
	$logresult = write_log(mysql_affected_rows() . " cases repaired", "/home/filedraw/logs/social_repair.log");
	
	echo $logresult["message"];
?>
