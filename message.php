<?
require_once('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php?redirect=message&rID=' . $_GET["id"]); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }
if (!isset($_GET["sid"]) and !isset($_GET["id"])) { redirect('inbox.php'); die(); }

require_once('config.php');

if (isset($_GET["sid"])) {
	$query = "SELECT `conversationKey` FROM `conversations` WHERE (`fromStudentKey` = " . $_SESSION["studentKey"] . " AND `toStudentKey` = " . intval($_GET["sid"]) . ") OR (`fromStudentKey` = " . intval($_GET["sid"]) . " AND `toStudentKey` = " . $_SESSION["studentKey"] . ")";
	$result = myquery($query);
	if (mysql_num_rows($result) > 0) {
		$con = mysql_fetch_array($result);
		if (isset($_GET["mentor"])) {
			$class = $_GET["class"];
			$mentor = $_GET["mentor"];
			redirect('message.php?id=' . $con["conversationKey"] . '&class=' . $class . '&mentor=' . $mentor);
		} else redirect('message.php?id=' . $con["conversationKey"]);
		die();
	}
}

if (trim($_POST["update"]) != "") {
	$query = "UPDATE `conversations` SET `lastMessage`=NOW() WHERE `conversationKey` = " . intval($_GET["id"]) . " AND (`toStudentKey` = " . $_SESSION["studentKey"] . " OR `fromStudentKey` = " . $_SESSION["studentKey"] . ")";
	$result = mysql_query($query);
	if (mysql_affected_rows() == 1) {
		$query = "SELECT c.`fromStudentKey`,c.`toStudentKey`,s1.`first` AS s1first,s1.`last` AS s1last,s1.`studentEmail` AS s1studentEmail,s1.`emailDirect` AS s1emailDirect, s2.`first` AS s2first,s2.`last` AS s2last,s2.`studentEmail` AS s2studentEmail,s2.`emailDirect` AS s2emailDirect FROM `conversations` AS c LEFT JOIN `student` AS s1 ON c.`toStudentKey`=s1.`studentKey` LEFT JOIN `student` AS s2 ON c.`fromStudentKey`=s2.`studentKey` WHERE c.`conversationKey` = " . intval($_GET["id"]) . " AND (c.`toStudentKey` = " . $_SESSION["studentKey"] . " OR c.`fromStudentKey` = " . $_SESSION["studentKey"] . ")";
		$result = mysql_query($query);
		$convo = mysql_fetch_array($result);
		if ($convo["fromStudentKey"] == $_SESSION["studentKey"]) {
			$toStudentKey = $convo["toStudentKey"]; 
			$to = $convo["s1first"] . " " . $convo["s1last"] . " <" . $convo["s1studentEmail"] . ">";
			$emailDirect = intval($convo["s1emailDirect"]);
		} else {
			$toStudentKey = $convo["fromStudentKey"];
			$to = $convo["s2first"] . " " . $convo["s2last"] . " <" . $convo["s2studentEmail"] . ">";
			$emailDirect = intval($convo["s2emailDirect"]);
		}
		$sqldata = array(
			"conversationKey"=>intval($_GET["id"]),
			"fromStudentKey"=>$_SESSION["studentKey"],
			"toStudentKey"=>$toStudentKey,
			"messageSent"=>"NOW()",
			"messageContent"=>'"' . safe($_POST["update"]) . '"'
		);
		if (time() - $_SESSION["lastPost"] > 3) mysql_insert_array("conversationContent", $sqldata, FALSE, FALSE);
		else $cancelIt = TRUE;
		$_SESSION["lastPost"] = time();
		
		if ($emailDirect > 0 and !isset($cancelIt)) {
			$params = array(
				'signature'	=> "5cb012d3f4",
				'action'	=> "shorturl",
				'format'	=> "simple",
				'url'		=> "http://social.tntlab.org/message.php?id=" . intval($_GET["id"])
			);
			$encoded_params = array();
			foreach ($params as $k => $v) $encoded_params[] = urlencode($k).'='.urlencode($v);
			$url = "http://social.tntlab.org/url/yourls-api.php?" . implode('&', $encoded_params);
			$link = file_get_contents($url);
			
			$message =  date("F jS \a\\t g:ia", time()+7200) . ", you received a new direct message on socialPsych, the ODU Psychology Department Social Network!\n\n";
			if (strlen($_POST["update"]) > 300) $message .= "The first 300 characters of the message follows:\n";
			else $message .= "The content of the message follows:\n";
			$message .= substr($_POST["update"], 0, 300);
			if (strlen($_POST["update"]) > 300) $message .= "...";
			$message .= "\n\nTo read this message in socialPsych, please follow this link: " . $link . "\n\n";
	
			$subject = 'New Direct Message';

			mailer($to, $subject, $message, $emailDirect);
		}
		
		if (!isset($cancelIt)) $logresult = write_log($_SESSION["username"] . " added to conversation ID # " . intval($_GET["id"]));
		
		redirect('message.php?id=' . $_GET["id"]);
		die();
	} else {
		if (!isset($_GET["sid"])) die('Malformed URL.');
		$sqldata = array(
			"fromStudentKey"=>$_SESSION["studentKey"],
			"toStudentKey"=>intval($_GET["sid"]),
			"lastMessage"=>"NOW()",
			"lastFromRead"=>"NOW()"
		);
		if (time() - $_SESSION["lastPost"] > 3) mysql_insert_array("conversations", $sqldata, FALSE, FALSE);
		else $cancelIt = TRUE;
		$_SESSION["lastPost"] = time();

		$conversationKey = mysql_insert_id();
		$sqldata = array(
			"conversationKey"=>$conversationKey,
			"fromStudentKey"=>$_SESSION["studentKey"],
			"toStudentKey"=>intval($_GET["sid"]),
			"messageSent"=>"NOW()",
			"messageContent"=>'"' . safe($_POST["update"]) . '"'
		);		
		if (!isset($cancelIt)) mysql_insert_array("conversationContent", $sqldata, FALSE, FALSE);
		$query = "SELECT `first`,`last`,`studentEmail`,`emailDirect` FROM `student` WHERE `studentKey` = " . intval($_GET["sid"]);
		$result = myquery($query);
		$convo = mysql_fetch_array($result);
		
		if (intval($emailDirect) > 0 and !isset($cancelIt)) {
			
			$params = array(
				'signature'	=> "5cb012d3f4",
				'action'	=> "shorturl",
				'format'	=> "simple",
				'url'		=> "http://social.tntlab.org/message.php?id=" . $conversationKey
			);
			$encoded_params = array();
			foreach ($params as $k => $v) $encoded_params[] = urlencode($k).'='.urlencode($v);
			$url = "http://social.tntlab.org/url/yourls-api.php?" . implode('&', $encoded_params);
			$link = file_get_contents($url);
			
			$message =  "On " . date("F jS \a\\t g:ia", time()+7200) . ", you received a new direct message on socialPsych, the ODU Psychology Department Social Network!\n\n";
			if (strlen($_POST["update"]) > 300) $message .= "The first 300 characters of the message follows:\n";
			else $message .= "The content of the message follows:\n";
			$message .= substr($_POST["update"], 0, 300);
			if (strlen($_POST["update"]) > 300) $message .= "...";
			$message .= "\n\nTo read this message in socialPsych, please follow this link: " . $link . "\n\n";
	
			$subject = 'New Direct Message';
	
			mailer($to, $subject, $message, intval($emailDirect));
		}

		if (!isset($cancelIt)) $logresult = write_log($_SESSION["username"] . " created a new conversation with convo ID # " . $conversationKey);
		redirect('message.php?id=' . $conversationKey);
		die();
	}
} elseif (isset($_GET["sid"])) {
	if ($_GET["sid"] == $_SESSION["studentKey"]) die();
	$query = "SELECT `first`,`last`,`anon`,`anonNick` FROM `student` WHERE `studentKey` = " . intval($_GET["sid"]);
	$result = myquery($query);
	$recipient = mysql_fetch_array($result);
	
	if ($recipient["anon"] == 1) $recipientName = htmlspecialchars($recipient["anonNick"]);
	else $recipientName = $recipient["first"] . " " . $recipient["last"];
}

if (isset($_GET["id"])) {
	$logresult = write_log($_SESSION["username"] . " read conversation ID # " . intval($_GET["id"]));
	$query = "UPDATE `conversations` SET `lastToRead`=NOW() WHERE `conversationKey` = " . intval($_GET["id"]) . " AND `toStudentKey` = " . $_SESSION["studentKey"] . "; ";
	$result = myquery($query);
	$query = "UPDATE `conversations` SET `lastFromRead`=NOW() WHERE `conversationKey` = " . intval($_GET["id"]) . " AND `fromStudentKey` = " . $_SESSION["studentKey"] . "; ";
	$result = myquery($query);
}

printhead("p { margin: 0 0 1em 0; }");
startbody();

printmenu_start();

if (isset($recipientName)) echo '<p style="font-weight: bold; margin: 0 0 1em 0;">Begin a Conversation with ' . $recipientName . '</p>';

?>
<form method="post" action="message.php?<? 
	if (isset($_GET["id"])) echo "id=" . intval($_GET["id"]); 
	elseif (isset($_GET["sid"])) echo "sid=" . intval($_GET["sid"]);
?>">
<p style="text-align:right"><textarea class="status" name="update" onkeyup="textLimit(this, 1000);"><? 

if (isset($_GET["mentor"])) {
	if ($_GET["mentor"] == 1) {
		echo "Hi - I'm looking for mentor in PSYC " . $_GET["class"] . '.  Could you help me?';	
		$logresult = write_log($_SESSION["username"] . " started messaging a potential mentor in " . $_GET["class"]);
	} else {
		echo "Hi - I'm interested in mentoring students that would like help with PSYC " . $_GET["class"] . '.  Would you like some help?';	
		$logresult = write_log($_SESSION["username"] . " started messaging a potential mentee in " . $_GET["class"]);
	}
}

?></textarea><br /><input value="Send Message" type="submit" id="statussubmit"></p>
</form>
<?

$logresult = write_log($_SESSION["username"] . " opened conversation ID# " . $_GET["id"]);

$query = "SELECT cc.`fromStudentKey`,cc.`toStudentKey`,UNIX_TIMESTAMP(cc.`messageSent`) AS unixDate,cc.`messageContent`,s1.`first` AS fromFirst,s1.`last` AS fromLast,s1.`anon` AS fromAnon,s1.`anonNick` AS fromNick,s2.`first` AS toFirst,s2.`last` AS toLast,s2.`anon` AS toAnon,s2.`anonNick` AS toNick FROM `conversationContent` AS cc LEFT JOIN `student` AS s1 ON cc.`fromStudentKey` = s1.`studentKey` LEFT JOIN `student` AS s2 ON cc.`toStudentKey` = s2.`studentKey` WHERE cc.`conversationKey` = " . intval($_GET["id"]) . " AND (cc.`fromStudentKey` = " . $_SESSION["studentKey"] . " OR cc.`toStudentKey` = " . $_SESSION["studentKey"] . ") ORDER BY cc.`messageSent` DESC";
$result = myquery($query);

echo '<table class="status">';

while ($row = mysql_fetch_array($result)) {
	if (!isset($started)) {
		echo '<p><strong>Conversation with ';
		if ($row["toStudentKey"] == $_SESSION["studentKey"]) {
			if ($row["fromAnon"] == 0) echo $row["fromFirst"] . " " . $row["fromLast"];
			else echo $row["fromNick"];
		} else {
			if ($row["toAnon"] == 0) echo $row["toFirst"] . " " . $row["toLast"];
			else echo $row["toNick"];
		}
		echo  '</strong></p>';
		$started = TRUE;
	}
	echo '<tr><td class="statuspic"><img src="profilePic.php?thumb=1&id=' . $row["fromStudentKey"] . 
		'"></td><td><strong><a href="';
	if ($row["fromStudentKey"] == $_SESSION["studentKey"]) echo 'myprofile.php">';
	else echo 'profile.php?id=' . $row["fromStudentKey"] . '">';
	if ($row["fromAnon"] == 1) echo $row["fromNick"] . "</a>*";
	else echo $row["fromFirst"] . " " . $row["fromLast"] . "</a>";
	echo "</strong> " . nl2br(htmlspecialchars($row["messageContent"]));
	echo '<p class="time">';
	echo unixToText($row["unixDate"]);
	echo '</td></tr>';
	echo '<tr><td colspan="2"><hr class="statushr" /></td></tr>';
}

echo '</table>';

printmenu_end();
endbody();
mysql_close();
?>
