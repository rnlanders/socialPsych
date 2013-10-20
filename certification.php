<?
$certdays = 4;

require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();
printmenu_start();

$logresult = write_log($_SESSION["username"] . " looked at the cert center");

echo '<p><strong>Certification Center</strong></p>';
if (isset($_GET["rank"])) {
	if ($_GET["rank"] == 0) echo '<p style="color:red"><strong>EXAM RESULTS:</strong> Unfortunately, you did not meet the scoring threshold on your certification exam to increase in rank.  Please try again in ' . $certdays . ' days.</p>';
	elseif ($_GET["rank"] == 1) echo '<p style="color:red"><strong>EXAM RESULTS:</strong> Congratulations!  You passed your certification exam and increased in rank.</p>';
}
echo "<p>You can be certified in any course, even if you aren't enrolled in that course.  Whenever you post a comment in a discussion that you are certified in, you will be marked using one of the certification graphics below.</p>";
echo '<p>You can take up to one exam per subject area every ' . $certdays . ' days.  You can only rise one cerification rank at a time, and you cannot lose ranks.  At each level, you must get a certain percentage of questions correct on your certification exam.</p>';
echo '<p><strong>PLEASE NOTE: </strong>Certification exams are not required for your classes, and your scores on these exams will never be reported to your instructors.  They will only be used to display badges on your profile and mark you as an expert in discussions.';
echo '<table class="certtable">';
echo '<tr><td>&nbsp;</td><td class="head">Certification Rank</td><td class="head">Requirements</td></tr>';
echo '<tr><td><img src="images/cert1.gif"></td><td>Certified Newbie</td><td>30% correct on certification exam</td></tr>';
echo '<tr><td><img src="images/cert2.gif"></td><td>Certified Novice</td><td>50% correct on exam while a Certified Newbie</td></tr>';
echo '<tr><td><img src="images/cert3.gif"></td><td>Certified Intermediate</td><td>70% correct on exam while a Certified Novice</td></tr>';
echo '<tr><td><img src="images/cert4.gif"></td><td>Certified Expert</td><td>90% correct on exam while a Certified Intermediate</td></tr>';
echo '<tr><td><img src="images/cert5.gif"></td><td>Certified Master</td><td>100% correct on exam while a Certified Expert</td></tr>';
echo "</table>";

$query = "SELECT DISTINCT cql.`classNumber`, c.`courseTitle`, cs.`rank`, cs.`lastTested` AS lastTested, (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(cs.`lastTested`)) AS nextTest FROM `classQuestionList` AS cql LEFT JOIN `classes` AS c ON cql.`classNumber`=c.`classNumber` LEFT JOIN `certifiedStudents` AS cs ON cql.`classNumber`=cs.`classNumber` AND cs.`studentKey`=" . $_SESSION["studentKey"] . " ORDER BY cql.`classNumber`";
//die($query);
$result = myquery($query);
echo '<p><strong>Certifications:</strong></p>';
echo '<table class="certtable">';
echo '<tr><td class="head">Rank</td><td class="head">Course</td><td class="head">Availability</td></tr>';
while ($row = mysql_fetch_array($result)) {
	if (!is_null($row["courseTitle"])) {
		echo '<tr><td>';
		if (is_null($row["rank"])) $row["rank"] = 0;
		echo '<img src="images/cert' . $row["rank"] . '.gif">';
		echo '</td>';
		echo '<td>' . $row["classNumber"] . ' - ' . $row["courseTitle"] . '</td>';
		if ($row["rank"] == 5) {
			echo '<td>Maximum rank achieved</td>';
		} elseif (is_null($row["lastTested"]) or ($row["nextTest"] > (86400 * $certdays))) {
			echo '<form method="post" action="certexam.php"><input type="hidden" name="classID" value="' . $row["classNumber"] . '">';
			echo '<td>';
			echo '<input type="submit" value="Take ' . $row["classNumber"] . ' Certification Exam">';
			echo '</td>';
			echo '</form>';
		} else {
			echo '<td>Next available ' . date('l F jS g:ia', time()+(86400 * $certdays)+7200-$row["nextTest"]) . '</td>';
		}
		echo '</tr>';
	}
}

echo '</table>';

printmenu_end();
endbody();
mysql_close();
?>
