<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }
if (!isset($_GET["id"])) { redirect('home.php'); die(); }

require('config.php');

$query = "SELECT s.`signupStage`,s.`anon`,s.`anonNick`,s.`first`,s.`last`,s.`instructorFlag`,p.`sex`,p.`age`,p.`relationshipStatus`,p.`hometown`,p.`schoolStatus`,p.`employer`,p.`workStatus`,p.`major`,p.`favorite`,p.`clubs`,p.`activities`,p.`interests`,p.`faveMusic`,p.`faveTV`,p.`faveBooks`,p.`bigFiveUse` FROM `student` AS s RIGHT JOIN `studentProfile` AS p ON s.`studentKey` = p.`studentKey` WHERE s.`studentKey` = " . intval($_GET["id"]);
$result = myquery($query);

if (mysql_num_rows($result) == 0) { redirect('home.php'); die(); }
$profile = mysql_fetch_array($result);

if ($profile["signupStage"] != 9) { mysql_close(); redirect('home.php'); die(); }

printhead();
startbody();

printmenu_start();
echo '<p><strong>';	
if ($profile["anon"] == 0) echo $profilePrint = $profile["first"] . " " . $profile["last"];
else echo $profilePrint = $profile["anonNick"];

if (substr($profilePrint, strlen($profilePrint) - 1, 1) != "s") echo "'s "; else echo "'";

echo " Profile</strong>";

if ($_GET["id"] == $_SESSION["studentKey"]) $logresult = write_log($_SESSION["username"] . " checked how others see his/her profile");
else $logresult = write_log($_SESSION["username"] . " checked the profile of " . $_GET["id"]);

if ($profile["instructorFlag"] == 1) echo " [ODU Instructor/Staff]";
elseif ($profile["anon"] == 1) echo " [Anonymous Student]";
?>
</p>
<p><img style="margin-right: 125px" src="profilePic.php?id=<? echo intval($_GET["id"]); ?>" /><?
if ($profile["bigFiveUse"] == 1) echo '<img src="personality.php?id=' . intval($_GET["id"]) . '" /></p>';
if ($_GET["id"] != $_SESSION["studentKey"]) {
	echo '<form style="text-align: center" method="post" action="message.php?sid=' . intval($_GET["id"]) . '"><input class="convo" type="submit" value="Enter a Conversation with this ';
	if ($profile["instructorFlag"] == 1) echo 'Instructor/Staff Member'; else echo 'Student';
	echo '"></form>';
}
?>
<p style="margin-top: 1em;"><strong>Five Most Recent Updates</strong></p>
<?

$query = "SELECT `studentKey`, `statusText`, UNIX_TIMESTAMP(`statusDateUpdated`) as `unixDate` FROM `statusUpdates` WHERE `studentKey` = " . intval($_GET["id"]) . " ORDER BY `statusDateUpdated` DESC LIMIT 5";
$result = myquery($query);

echo '<table class="status">';
while ($row = mysql_fetch_array($result)) {
	echo '<tr><td class="statuspic"><img src="profilePic.php?thumb=1&id=' . $row["studentKey"] . 
		'"></td><td><strong><a href="profile.php?id=' . $row["studentKey"] . '">';
	if ($row["anon"] == 1) echo $row["anonNick"];
	else echo $row["first"] . " " . $row["last"];
	echo "</a></strong> " . nl2br(htmlspecialchars($row["statusText"]));
	echo '<p class="time">';
	if ($row["unixDate"] - time() > -30) echo 'A few seconds ago';
	elseif ($row["unixDate"] - time() > -90) echo 'About a minute ago';
	elseif ($row["unixDate"] - time() > -150) echo 'About two minutes ago';
	elseif ($row["unixDate"] - time() > -300) echo 'Less than five minutes ago';
	elseif ($row["unixDate"] - time() > -600) echo 'Less than ten miunutes ago';
	elseif ($row["unixDate"] - time() > -1800) echo 'Less than half an hour ago';
	elseif ($row["unixDate"] - time() > -3600) echo 'Less than an hour ago';
	elseif ($row["unixDate"] - time() > -5400) echo 'About an hour ago';
	elseif ($row["unixDate"] - time() > -43200) echo 'About ' . round(-($row["unixDate"] - time()) / 3600) . ' hours ago';
	elseif ($row["unixDate"] > strtotime("-1 day")) echo 'Yesterday';
	elseif ($row["unixDate"] > strtotime("-2 days")) echo 'Two days ago';
	elseif ($row["unixDate"] > strtotime("-3 days")) echo 'Three days ago';
	elseif ($row["unixDate"] > strtotime("-4 days")) echo 'Four days ago';
	elseif ($row["unixDate"] > strtotime("-5 days")) echo 'Five days ago';
	elseif ($row["unixDate"] > strtotime("-6 days")) echo 'Six days ago';
	elseif ($row["unixDate"] > strtotime("-2 weeks")) echo 'About a week ago';
	elseif ($row["unixDate"] > strtotime("-3 weeks")) echo 'About two weeks ago';
	elseif ($row["unixDate"] > strtotime("-3 weeks")) echo 'About three weeks ago';
	elseif ($row["unixDate"] > strtotime("-1 month")) echo 'About a month ago';
	else echo date('F jS, Y', $row["unixDate"]);
	echo '</td></tr>';
	echo '<tr><td colspan="2"><hr class="statushr" /></td></tr>';
}
echo "</table>";

$infos = array("sex"=>"Sex", "age"=>"Age", "relationshipStatus"=>"Relationship Status", "hometown"=>"Hometown", "schoolStatus"=>"School Status", "employer"=>"Employer", "workStatus"=>"Work Status", "major"=>"Major", "favorite"=>"Favorite Class", "clubs"=>"Clubs", "activities"=>"Activities", "interests"=>"Interests", "faveMusic"=>"Favorite Music", "faveTV"=>"Favorite Movies/TV Shows", "faveBooks"=>"Favorite Books");

$query = "SELECT i.`classKey`,i.`type`,c.`classNumber`,c.`courseTitle`,c.`sectionNumber` FROM `instructors` AS i LEFT JOIN `classes` AS c ON i.`classKey`=c.`classKey` WHERE i.`studentKey` = " . intval($_GET["id"]) . " ORDER BY `classNumber`";
$result = myquery($query);

echo '<table class="info">';

$rank = array("","Certified Newbie","Certified Novice","Certified Intermediate","Certified Expert","Certified Master");

if (mysql_num_rows($result) > 0) {
	echo '<tr><td class="header" colspan="2">Course Responsibilities</td></tr>';
	while ($row = mysql_fetch_array($result)) {
		echo '<tr><td class="left">';
		if ($row["type"] == "1") echo 'Instructor'; else echo 'Staff';
		echo '</td><td>PSYC ' . $row["classNumber"] . ' - <a href="course.php?id=' . $row["classKey"] . '">' . $row["courseTitle"] . "</a> (Section " . $row["sectionNumber"] . ')</td></tr>'; 
	}
} else {
	echo '<tr><td class="header" colspan="2">Course Certifications</td></tr>';
	
	$query = "SELECT DISTINCT cs.`classNumber`,c.`courseTitle`,cs.`rank` FROM `certifiedStudents` AS cs LEFT JOIN `classes` AS c ON c.`classNumber` = cs.`classNumber` WHERE `studentKey` = " . $_GET["id"] . " AND cs.`rank` > 0 ORDER BY `classNumber`, `rank` DESC";
	$result = myquery($query);

	if (mysql_num_rows($result) == 0) {
		echo '<tr><td colspan="2">This student is not certified in any courses yet!</td></tr>';
	} else while ($row = mysql_fetch_array($result)) {
		echo '<tr><td class="left"><img align="left" src="images/cert' . $row["rank"] . '.gif"> ' . $rank[$row["rank"]] . '</td><td>PSYC ' . $row["classNumber"] . ' - ' . $row["courseTitle"] . '</td></tr>';
	}
}

foreach ($infos as $db=>$formatted) {
	if ($db == "sex") echo '<tr><td class="header" colspan="2">Basic Information</td></tr>';
	elseif ($db == "schoolStatus") echo '<tr><td class="header" colspan="2">School Details</td></tr>';
	elseif ($db == "activities") echo '<tr><td class="header" colspan="2">Personal Details</td></tr>';
	if (trim($profile[$db]) != '' and $profile[$db] != 9) {
		echo '<tr><td class="left">' . $formatted . ':</td><td>';
		if ($db == "sex") {
			if ($profile[$db] == 1) echo 'Female';
			elseif ($profile[$db] == 2) echo 'Male';
			elseif ($profile[$db] == 3) echo 'Other';
		} elseif ($db == "relationshipStatus") {
			if ($profile[$db] == 1) echo 'Single';
			elseif ($profile[$db] == 2) echo 'In a relationship';
			elseif ($profile[$db] == 3) echo 'Married';
		} elseif ($db == "schoolStatus") {
			if ($profile[$db] == 1) echo 'Freshman';
			elseif ($profile[$db] == 2) echo 'Sophomore';
			elseif ($profile[$db] == 3) echo 'Junior';
			elseif ($profile[$db] == 4) echo 'Senior';		
			elseif ($profile[$db] == 5) echo 'Non-degree-seeking';
			elseif ($profile[$db] == 6) echo 'Returning/Non-traditional';
		} elseif ($db == "workStatus") {
			if ($profile[$db] == 1) echo 'Not currently employed';
			elseif ($profile[$db] == 2) echo '1-10 hours per week';
			elseif ($profile[$db] == 3) echo '11-20 hours per week';
			elseif ($profile[$db] == 4) echo '21-30 hours per week';		
			elseif ($profile[$db] == 5) echo '31-40 hours per week';
			elseif ($profile[$db] == 6) echo '41+ hours per week';
		} else echo nl2br(htmlspecialchars($profile[$db]));
	}
}

echo '</table>';

printmenu_end();
endbody();
mysql_close();
?>
