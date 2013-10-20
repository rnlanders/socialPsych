<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php?redirect=mentor'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

if (isset($_POST["mentorUpdate"])) {
	$logresult = write_log($_SESSION["username"] . " updated mentoring preferences");
	$sqldata = array();

	$query = "DELETE FROM `mentorList` WHERE `studentKey` = " . $_SESSION["studentKey"];
	$result = myquery($query);
	
	$classes = array();
	
	foreach ($_POST as $key=>$value) if (substr($key, 0, 3) == "mnt") {
		$query = "SELECT DISTINCT `classNumber` FROM `classes` WHERE `classNumber`='" . safe(substr($key, 3)) . "'";
		$result = myquery($query);
		$class = mysql_fetch_array($result);
		
		if (mysql_num_rows($result) == 1 and intval($value) > 0 and intval($value) < 4) {
			array_push($classes, $class["classNumber"]);
			$sqldata = array(
				"`studentKey`"=>$_SESSION["studentKey"],
				"`classNumber`"=>$class["classNumber"],
				"`mentorRequestType`"=>intval($value)
			);
			mysql_insert_array("mentorList", $sqldata);
		}
	}
	
	$query = "SELECT DISTINCT s.`studentKey`,s.`first`,s.`last`,s.`studentEmail`,s.`emailMentoring` FROM `mentorList` AS m LEFT JOIN `student` AS s ON m.`studentKey`=s.`studentKey` WHERE s.`emailMentoring` > 0 AND m.`studentKey` != " . $_SESSION["studentKey"] . " AND (s.`lastMentorEmail` IS NULL OR UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( s.`lastMentorEmail` ) > 86400) AND m.`classNumber` IN ('" . implode("','", $classes) . "')";
	$result = myquery($query);
	
	$studentKeys = array();
	
	while ($row = mysql_fetch_array($result)) {
		array_push($studentKeys, $row["studentKey"]);
		$message = "You have a new match in the mentoring center on socialPsych, the ODU Psychology Department Online Social Network!";
		$message .= "\n\nTo check your mentoring center in socialPsych, please follow this link: http://social.tntlab.org/mentoring.php";
		$message .= "\n\nYou will receive mentor match e-mails up to once per 24 hours.  ";
	
		$to      = $row["first"] . " " . $row["last"] . " <" . $row["studentEmail"] . ">";
		$subject = 'New Mentoring Match';
	
		mailer($to, $subject, $message, $row["emailMentoring"]);
	}
	
	if (count($studentKeys) > 0) {
		$query = "UPDATE `student` SET `lastMentorEmail`=NOW() WHERE `studentKey` IN (" . implode(",", $studentKeys) . ")";
		$result = myquery($query);
	}
}

printhead("p { margin: 0 0 1em 0; }");
startbody();
printmenu_start();

echo '<p><strong>Mentoring Center</strong></p>';
echo '<p>The mentoring center connects students that want to help other students with those that want to be helped.  To connect with other students as either a mentor or mentee, you must first set your mentoring preferences at the bottom of this page.</p>';

$query = "SELECT DISTINCT ml.`classNumber`, ml.`mentorRequestType`, ml1.`studentKey`, c.`courseTitle`, s.`anon`, s.`anonNick`, s.`first`, s.`last`, cs.`rank` FROM `mentorList` AS ml LEFT JOIN `mentorList` AS ml1 ON ml.`mentorRequestType` != ml1.`mentorRequestType` AND ml.`classNumber` = ml1.`classNumber` LEFT JOIN `classes` AS c ON ml.`classNumber` = c.`classNumber` LEFT JOIN `student` AS s ON ml1.`studentKey` = s.`studentKey` LEFT JOIN `certifiedStudents` AS cs ON c.`classNumber`=cs.`classNumber` AND s.`studentKey`=cs.`studentKey` WHERE ml.`studentKey` = " . $_SESSION["studentKey"] . " ORDER BY ml.`mentorRequestType`, ml.`classNumber`, cs.`rank` DESC";
//die($query);
$result = myquery($query);

if (mysql_num_rows($result) > 0) {
	$lastReqType = 0;
	$lastClass = 0;
	echo '<p><strong>Mentor Matching</strong></p>';
	echo '<table class="matching">';
	while ($row = mysql_fetch_array($result)) {
		if ($lastReqType != $row["mentorRequestType"]) {
			if ($row["mentorRequestType"] == 1)
				echo '<tr><td class="head1" colspan="3">Students Who Want to Help You</td></tr>';
			else
				echo '<tr><td class="head1" colspan="3">Students Who You May Be Able to Help</td></tr>';
			$lastReqType = $row["mentorRequestType"];
		}
		if ($lastClass != $row["classNumber"]) {
			echo '<tr><td class="head2" colspan="3">' . $row["classNumber"] . ' - ' . $row["courseTitle"] . '</td></tr>';
			$lastClass = $row["classNumber"];
		}
		echo '<tr>';
		if (is_null($row["studentKey"])) {
			echo '<td class="pic" colspan="3"><em>';
			if ($row["mentorRequestType"] == 1) echo 'Sorry, no mentors are available at this time. Please check back later.';
			else echo 'Sorry, no one is seeking mentoring for this class at this time. Please check back later.';
			echo '</em></td>';
		} else {
			if (is_null($row["rank"])) $row["rank"] = 0;
			echo '<td class="pic"><img src="profilePic.php?thumb=1&id=' . $row["studentKey"] . '"></td>';
			echo '<td class="rank"><img src="images/cert' . $row["rank"] . '.gif"></td>';
			echo '<form method="get" action="message.php"><td><strong><a href="';
			echo 'profile.php?id=' . $row["studentKey"] . '">';
			if ($row["anon"] == 1) echo $row["anonNick"] . "</a>*";
			else echo $row["first"] . " " . $row["last"] . "</a>";
			echo '</strong><br /><input class="convom" type="submit" value="Message This Student">';
			echo '<input type="hidden" name="class" value="' . $row["classNumber"] . '">';
			echo '<input type="hidden" name="sid" value="' . $row["studentKey"] . '">';
			echo '<input type="hidden" name="mentor" value="' . $row["mentorRequestType"] . '"></td></form></tr>';
		}
		echo '</tr>';
	}	
	echo '</table>';
}

echo '<p style="margin-top: 1em;"><strong>Mentoring Preferences</strong></p>';
echo '<p>If you want to help other students, you want to be a <strong>mentor</strong>.<br />';
echo '<p>If you want help from other students, you want to be a <strong>mentee</strong>.</p>';

$query = "SELECT DISTINCT c.`classNumber`,c.`courseTitle`,ml.`mentorRequestType` FROM `classes` AS c LEFT JOIN `mentorList` AS ml ON ml.`studentKey`=" . $_SESSION["studentKey"] . " AND c.`classNumber`=ml.`classNumber` ORDER BY c.`classNumber`,c.`classNumber`";
$result = myquery($query);
echo '<form method="post" action="mentoring.php">';
echo '<table class="mentorList">';
echo '<tr><td class="header" colspan="3">I Want to Be A...<td class="header" colspan="2">&nbsp;</td></tr>';
echo '<tr><td class="header">Mentor</td><td class="header">Mentee</td><td class="header">Neither</td><td class="header" colspan="2">&nbsp;</td></tr>';
while ($row = mysql_fetch_array($result)) {
	echo '<tr>';
	echo '<td><input';
	if ($row["mentorRequestType"] == 2) echo " checked";
	echo ' type="radio" name="mnt' . $row["classNumber"] . '" value="2"></td>';
	echo '<td><input';
	if ($row["mentorRequestType"] == 1) echo " checked";
	echo ' type="radio" name="mnt' . $row["classNumber"] . '" value="1"></td>';
	echo '<td><input';
	if (is_null($row["mentorRequestType"])) echo " checked";
	echo ' type="radio" name="mnt' . $row["classNumber"] . '" value="0"></td>';	
	echo '<td class="class">' . $row["classNumber"] . "</td>";
	echo '<td class="left">' . trim($row["courseTitle"]) . '</td>';

	echo '<tr>';
}
echo '</table>';
echo '<input type="hidden" name="mentorUpdate" value="1">';
echo '<p><input type="submit" value="Update My Mentoring Preferences"></p>';
echo '</form>';

printmenu_end();
endbody();
mysql_close();
?>
