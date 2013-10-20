<?
	if ($argc <> 2) die();

	if ($argv[1] == "daily") $type = 2;
	elseif ($argv[1] == "halfhour") $type = 1;
	else die();

	require('config.php');
	require('functionlib.php');
	
	$query = "SELECT `mailQueueKey`,`to`,`subject`,`message`,`headers` FROM `mailQueue` WHERE `type`=" . $type . " AND `dateSent` IS NULL ORDER BY `to`";
	$result = myquery($query);

	$firstKeys = array();
	$incKeys = array();

	while ($mail = mysql_fetch_array($result)) {
		set_time_limit(60);
		if (!isset($to)) {
			$to = $mail["to"];
			$subject = $mail["subject"];
			$message = stripslashes($mail["message"]);
			$headers = $mail["headers"];
			array_push($firstKeys, $mail["mailQueueKey"]);
		} elseif ($to == $mail["to"]) {
			$subject = "Multiple New Messages";
			$message = $message . "\n\n-------------------------------------------------\n\n" . stripslashes($mail["message"]);
			array_push($incKeys, $mail["mailQueueKey"]);
		} else {
			$message .= "\n\nIf you no longer wish to receive these messages, please change your e-mail preferences in My Profile: http://social.tntlab.org/myprofile.php";
			//echo $to . '<br />' . $subject . '<br />' . $message . '<br />' . $headers . '<br /><br />';
			$mailresult = mail($to, $subject, $message, $headers);
			$to = $mail["to"];
			$subject = $mail["subject"];
			$message = stripslashes($mail["message"]);
			$headers = $mail["headers"];
			array_push($firstKeys, $mail["mailQueueKey"]);
		}
	}
	
	if (count($firstKeys) > 0) {
		$message = $message . "\n\nIf you no longer wish to receive these messages, please change your e-mail preferences in My Profile: http://social.tntlab.org/myprofile.php";
		$mailresult = mail($to, $subject, $message, $headers);

		$query = "UPDATE `mailQueue` SET `dateSent`=NOW() WHERE `type`=" . $type . " AND `mailQueueKey` IN (" . implode(",", array_merge($firstKeys, $incKeys)) . ")";
		$result = myquery($query);
		$logresult = write_log($argv[1] . ": " . count($firstKeys) + count($incKeys) . " mailed to " . count($firstKeys), "/home/filedraw/logs/social_automailer.log");
	} else {
		$logresult = write_log("no mail to send " . $argv[1], "/home/filedraw/logs/social_automailer.log");
	}

?>
