<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php?redirect=course&rID=' . $_GET["id"] . "&p=" . $_GET["p"]); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }
if (trim($_GET["id"]) == "") { redirect('courses.php'); die(); }

require('config.php');
if (trim($_POST["newClassText"]) != "" or trim($_POST["updateClassText"]) != "") {
	if (trim($_POST["newClassText"]) != "") {
		$logresult = write_log($_SESSION["username"] . " created a new class discussion for " . intval($_GET["id"]) . ": " . $_POST["newClassText"]);
		if (isset($_POST["private"])) $private = 1; else $private = 0;
		$statusText = $mailerText = safe($_POST["newClassText"]);
		$sqldata = array(
			"private"=>$private,
			"studentKey"=>$_SESSION["studentKey"],
			"classKey"=>intval($_GET["id"]),
			"dateUpdated"=>"NOW()",
			"statusText"=>"'" . $statusText . "'"
		);
		if (time() - $_SESSION["lastPost"] > 3) mysql_insert_array("classStatusUpdates", $sqldata, FALSE, FALSE);
		$_SESSION["lastPost"] = time();
		$pointKey = mysql_insert_id();
	} elseif (trim($_POST["updateClassText"]) != "") {
		$logresult = write_log($_SESSION["username"] . " commented on class discussion #" . intval($_POST["updateStatKey"]) . " for class #" . intval($_GET["id"]) . ": " . $_POST["updateClassText"]);
		$statusText = $mailerText = safe($_POST["updateClassText"]);
		$sqldata = array(
			"studentKey"=>$_SESSION["studentKey"],
			"classKey"=>intval($_GET["id"]),
			"dateUpdated"=>"NOW()",
			"keyRepliedTo"=>intval($_POST["updateStatKey"]),
			"statusText"=>"'" . $statusText . "'"
		);
		if (time() - $_SESSION["lastPost"] > 3) mysql_insert_array("classStatusUpdates", $sqldata, FALSE, FALSE);	
		$_SESSION["lastPost"] = time();
		$pointKey = intval($_POST["updateStatKey"]);
	}
	
	$query = "SELECT s.`first`,s.`last`,s.`studentEmail`,s.`emailClasses`,c.`classNumber`,c.`courseTitle` FROM `student` AS s INNER JOIN `studentsInClasses` AS sic ON s.`studentKey`=sic.`studentKey` LEFT JOIN `classes` AS c ON c.`classKey`=sic.`classKey` WHERE sic.`classKey` = '" . intval($_GET["id"]) . "' AND s.`studentKey` != " . $_SESSION["studentKey"] . " AND s.`emailClasses` > 0";
	$result = myquery($query);
	
	while ($row = mysql_fetch_array($result)) {
			$params = array(
				'signature'	=> "5cb012d3f4",
				'action'	=> "shorturl",
				'format'	=> "simple",
				'url'		=> "http://social.tntlab.org/course.php?id=" . intval($_GET["id"]) . "&p=" . $pointKey . "#" . $pointKey
			);
			$encoded_params = array();
			foreach ($params as $k => $v) $encoded_params[] = urlencode($k).'='.urlencode($v);
			$url = "http://social.tntlab.org/url/yourls-api.php?" . implode('&', $encoded_params);
			$link = file_get_contents($url);

			$message = "A new message was posted on " . date("F jS \a\\t g:ia", time()+7200) . " in your PSYC " . $row["classNumber"] . " (" . $row["courseTitle"] . ") course discussion area on socialPsych, the ODU Psychology Department Online Social Network!\n\n";
			if (strlen($mailerText) > 300) $message .= "The first 300 characters of the message follows:\n";
			else $message .= "The content of the message follows:\n";
			$message .= substr($mailerText, 0, 300);
			if (strlen($mailerText) > 300) $message .= "...";
			$message .= "\n\nTo read this message in socialPsych, please follow this link: " . $link . "\n\n";
	
			$to      = $row["first"] . " " . $row["last"] . " <" . $row["studentEmail"] . ">";
			$subject = 'New Message in PSYC ' . $row["classNumber"];

			mailer($to, $subject, $message, $row["emailClasses"]);
	}
	
	redirect('course.php?id=' . $_GET["id"]);
	die();
	
}

$query = "SELECT sic.`classKey`, i.`type` FROM `studentsInClasses` AS sic LEFT JOIN `instructors` AS i ON sic.`classKey` = i.`classKey` AND i.`studentKey` = " . $_SESSION["studentKey"] . " WHERE sic.`studentKey`=" . $_SESSION["studentKey"] . " AND sic.`pastStudent`=0 AND sic.`classKey`=" . intval($_GET["id"]);
$result = myquery($query);

if (mysql_num_rows($result) == 1) {
	$enrolled = TRUE; 
	$enrolledCheck = mysql_fetch_array($result);
	if (is_null($enrolledCheck["type"])) $instructorFlag = FALSE; else $instructorFlag = $enrolledCheck["type"];
} else {
	$instructorFlag = $enrolled = FALSE;
}

mysql_query("set sql_big_selects=1");
$query = "SELECT csu.`classStatusKey`,csu.`studentKey`,UNIX_TIMESTAMP(csu.`dateUpdated`) AS `dateUpdated`,csu.`statusText`,csu.`private`,
s1.`first` AS fromFirst,s1.`last` AS fromLast,s1.`anon` AS fromAnon, s1.`anonNick` AS fromNick, s1.`instructorFlag` AS fromInst,
rep.`statusText` AS replyStatusText,UNIX_TIMESTAMP(rep.`dateUpdated`) AS replyDateUpdated,rep.`studentKey` AS replyStudentKey,
s2.`first` AS replyFirst, s2.`last` AS replyLast, s2.`anon` AS replyAnon, s2.`anonNick` AS replyAnonNick, s2.`instructorFlag` AS toInst,
c.`courseTitle`, c.`classNumber`, c.`sectionNumber`, c.`instructor`, sic1.`studentInClassKey` AS sic1key, sic2.`studentInClassKey` AS sic2key, cs1.`rank` AS cs1rank, cs2.`rank` AS cs2rank, i1.`instructorKey` AS i1key, i1.`type` AS i1type, i2.`instructorKey` AS i2key, i2.`type` AS i2type
FROM `classStatusUpdates` AS csu 
LEFT JOIN `classStatusUpdates` AS rep ON csu.`classStatusKey`=rep.`keyRepliedTo` 
LEFT JOIN `classes` AS c ON csu.`classKey`=c.`classKey` 
LEFT JOIN `student` AS s1 ON csu.`studentKey`=s1.`studentKey` 
LEFT JOIN `student` AS s2 ON rep.`studentKey`=s2.`studentKey` 
LEFT JOIN `studentsInClasses` AS sic1 ON csu.`studentKey`=sic1.`studentKey` AND csu.`classKey`=sic1.`classKey`
LEFT JOIN `studentsInClasses` AS sic2 ON rep.`studentKey`=sic2.`studentKey` AND rep.`classKey`=sic2.`classKey`
LEFT JOIN `certifiedStudents` AS cs1 ON c.`classNumber`=cs1.`classNumber` AND csu.`studentKey`=cs1.`studentKey`
LEFT JOIN `certifiedStudents` AS cs2 ON c.`classNumber`=cs2.`classNumber` AND rep.`studentKey`=cs2.`studentKey`
LEFT JOIN `instructors` AS i1 ON i1.`studentKey`=csu.`studentKey` AND csu.`classKey`=i1.`classKey`
LEFT JOIN `instructors` AS i2 ON i2.`studentKey`=rep.`studentKey` AND rep.`classKey`=i2.`classKey`
WHERE csu.`keyRepliedTo` IS NULL AND csu.`classKey`=" . intval($_GET["id"]);
if ($enrolled == FALSE) $query .= " AND csu.`private`=0";
$query .= " ORDER BY csu.`dateUpdated` DESC, rep.`dateUpdated`";
//die($query);
$result = myquery($query);

printhead("p { margin: 0 0 1em 0; }");
startbody();

printmenu_start();

$lastClassStatusKey = 0;

echo '<table class="classtatus">';

echo '<tr>';
echo '<td class="statuspic"><img src="profilePic.php?thumb=1&id=' . $_SESSION["studentKey"] . '&t='. date("Gs") .'"></td>';
echo '<form method="post" action="course.php?id=' . intval($_GET["id"]) . '">';
echo '<td colspan="2"><textarea onkeyup="textLimit(this, 1000);" name="newClassText" class="classStatus"></textarea><br />';
if ($enrolled == TRUE) echo '<input type="checkbox" name="private"> Check to prevent students outside this class from seeing this post<br />';
echo '<input class="classStatusButton" type="submit" value="Create New Comment Thread"><br />';
echo '</td>';
echo '</form></tr>';
echo '<tr><td colspan="3"><hr /></td></tr>';	

function replyPrompt($key) {
	echo '<tr>';
	echo '<td class="statuspic">&nbsp;</td>';
	echo '<td class="statuspic"><img src="profilePic.php?thumb=1&id=' . $_SESSION["studentKey"] . '&t='. date("Gs") . '"></td>';
	echo '<form method="post" action="course.php?id=' . intval($_GET["id"]) . '"><input type="hidden" name="updateStatKey" value="' . $key . '">';
	echo '<td><textarea onkeyup="textLimit(this, 1000);" name="updateClassText" class="classStatus"></textarea><br /><input class="classStatusButton" type="submit" value="Add My Comment"></td>';
	echo '</form></tr>';	
	echo '<tr><td colspan="3"><hr /></td></tr>';	
}

$logresult = write_log($_SESSION["username"] . " opened class discussion #" . intval($_GET["id"]));

while ($row = mysql_fetch_array($result)) {
	if (!isset($pastFirstTime)) {
		echo '<p><strong>(' . $row["sectionNumber"] . ') PSYC ' . $row["classNumber"] . ' - ' . $row["courseTitle"] . ' (' . $row["instructor"] . ')</strong></strong><br />';
		echo 'Course Discussion</p>';	
		$pastFirstTime = TRUE;
	}
	
	if ($lastClassStatusKey != $row["classStatusKey"]) {
		if ($lastClassStatusKey != 0) replyPrompt($lastClassStatusKey);
		$lastClassStatusKey = $row["classStatusKey"];
		echo '<tr>';
		echo '<td';
		if (!is_null($row["i1key"])) echo ' class="instpic"'; else echo ' class="statuspic"';
		echo '>';
		echo '<a name="' . $row["classStatusKey"] . '"></a>';
		echo '<img src="profilePic.php?thumb=1&id=' . $row["studentKey"] . '&t='. date("Gs") . '"></td>';
		echo '<td';
		if (!is_null($row["i1key"])) echo ' class="instshort"'; else echo ' class="short"';
		echo ' colspan="2">';
		if (is_null($row["i1key"]) and !is_null($row["cs1rank"]) and $row["cs1rank"] > 0) echo '<img src="images/cert' . $row["cs1rank"] . '.gif" align="left"> ';
		if (is_null($row["sic1key"])) echo '<span class="nas">(NAS)</span> ';
		echo '<strong><a href="';
		if ($row["studentKey"] == $_SESSION["studentKey"]) echo 'myprofile.php">';
		else echo 'profile.php?id=' . $row["studentKey"] . '">';
		if ($row["fromAnon"] == 1) echo $row["fromNick"] . "</a>";
		else echo $row["fromFirst"] . " " . $row["fromLast"] . "</a>";
		if ($row["fromAnon"] == 1 and $row["fromInst"] != 1) echo '*';
		if ($row["fromAnon"] == 1 and $instructorFlag and $row["fromInst"] != 1) echo ' ('.$row["fromFirst"] . " " . $row["fromLast"].')';
		echo "</strong> " . linkify(nl2br(htmlspecialchars($row["statusText"]))) . '<p class="time">' . unixToText($row["dateUpdated"]);
		if ($row["private"] == "1") echo '<br /><strong>PRIVATE:</strong> This post and its replies are only visible to members of this class.';
		if (!is_null($row["i1key"])) {
			echo '<br /><strong>';
			if ($row["i1type"] == 1) echo "INSTRUCTOR:</strong> The poster above is the instructor of this course.";
			else echo "STAFF/TA:</strong> The poster above is part of the instructional staff for this course.";
		}
		if (is_null($row["sic1key"])) echo '<br /><span class="nas">NAS:</span> The poster above is <strong>not a student</strong> in this course.';
		echo '</p></td>';
		echo '</tr>';
	}
	if (!is_null($row["replyStudentKey"])) {
		echo '<tr>';
		echo '<td class="statuspic">&nbsp;</td>';
		echo '<td';
		if (!is_null($row["i2key"])) echo ' class="instpic"'; else echo ' class="statuspic"';
		echo '><img src="profilePic.php?thumb=1&id=' . $row["replyStudentKey"] . '&t='. date("Gs") . '"></td>';
		echo '<td';
		if (!is_null($row["i2key"])) echo ' class="instlong"'; else echo ' class="short"';
		echo '>';
		if (is_null($row["i2key"]) and !is_null($row["cs2rank"]) and $row["cs2rank"] > 0) echo '<img src="images/cert' . $row["cs2rank"] . '.gif" align="left"> ';
		if (is_null($row["sic2key"])) echo '<span class="nas">(NAS)</span> ';
		echo '<strong><a href="';
		if ($row["replyStudentKey"] == $_SESSION["studentKey"]) echo 'myprofile.php">';
		else echo 'profile.php?id=' . $row["replyStudentKey"] . '">';
		if ($row["replyAnon"] == 1) echo $row["replyAnonNick"] . "</a>";
		else echo $row["replyFirst"] . " " . $row["replyLast"] . "</a>";
		if ($row["replyAnon"] == 1 and $row["toInst"] != 1) echo '*';
		if ($row["replyAnon"] == 1 and $instructorFlag and $row["toInst"] != 1) echo ' ('.$row["replyFirst"] . " " . $row["replyLast"].')';
		echo "</strong> " . linkify(nl2br(htmlspecialchars($row["replyStatusText"]))) . '<p class="time">' . unixToText($row["replyDateUpdated"]);
		if (!is_null($row["i2key"])) {
			echo '<br /><strong>';
			if ($row["i2type"] == 1) echo "INSTRUCTOR:</strong> The poster above is the instructor of this course.";
			else echo "STAFF/TA:</strong> The poster above is part of the instructional staff for this course.";
		}
		if (is_null($row["sic2key"])) echo '<br /><span class="nas">NAS:</span> The poster above is <strong>not a student</strong> in this course.';
		echo '</p></td>';
		echo '</tr>';
	}
	$lastID = $row["classStatusKey"];	
}
if (mysql_num_rows($result) > 0) replyPrompt($lastID);
echo '</table>';


printmenu_end();
endbody();
mysql_close();
?>
