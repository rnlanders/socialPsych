<h2>socialPsych, the ODU Department of Psychology's Online Social Network</h2>
<table class="main">
<tr><td class="left">
<img style="margin-bottom: 2px;" src="profilePic.php?thumb=0&id=<? echo $_SESSION["studentKey"]; ?>"><br />
<p class="menuitem"><strong>Main Menu</strong><br />
<a href="home.php">News Feed</a><br />
<a href="myprofile.php">My Profile</a><br />
<?
require_once('config.php');
require_once('functionlib.php');
$query = "SELECT c.`toStudentKey`,UNIX_TIMESTAMP(c.`lastMessage`) AS `lastMessage`,UNIX_TIMESTAMP(c.`lastToRead`) AS `lastToRead`,UNIX_TIMESTAMP(c.`lastFromRead`) AS `lastFromRead` FROM `conversations` AS c LEFT JOIN `student` AS s1 ON c.`fromStudentKey` = s1.`studentKey` LEFT JOIN `student` AS s2 ON c.`toStudentKey` = s2.`studentKey` WHERE c.`fromStudentKey` = " . $_SESSION["studentKey"] . " OR c.`toStudentKey` = " . $_SESSION["studentKey"];
$result = myquery($query);

$messageCount = 0;

while ($row = mysql_fetch_array($result)) {
	if ($row["toStudentKey"] == $_SESSION["studentKey"]) {
		$lastRead = $row["lastToRead"];
	} else {
		$lastRead = $row["lastFromRead"];
	}
	if (is_null($lastRead) OR $lastRead < $row["lastMessage"]) $messageCount++;
}
if ($messageCount > 0) echo '<strong>';
echo '<a href="inbox.php">Inbox</a>';
if ($messageCount > 0) echo '</strong> [' . $messageCount . ' New Incoming]';
echo '<br />';
?><br />
<a href="certification.php">Certification Center</a><br />
<a href="mentoring.php">Mentoring Center</a><br /><br />
<?
	if ($_SESSION["instructorFlag"] == "1") echo '<a href="instructors.php">Instructor Tools</a><br /><br />';
?>
<a href="index.php?logoff=1">Logoff</a></p>
<p class="menuitem"><strong>My Class Discussions</strong><br />
<? 
	$query = "SELECT `studentsInClasses`.`classKey`,`classes`.`courseTitle` FROM `studentsInClasses` LEFT JOIN `classes` ON `studentsInClasses`.`classKey` = `classes`.`classKey` WHERE `studentsInClasses`.`pastStudent` = 0 and `studentsInClasses`.`studentKey` = " . $_SESSION["studentKey"];
	$result = myquery($query);
	
	while ($row = mysql_fetch_array($result)) {
		echo '<a href="course.php?id=' . $row["classKey"] . '">' . $row["courseTitle"] . '</a><br /><br />';
	}
?>
<a href="courses.php">Browse Courses</a></p>
<p class="menuitem"><strong>Search Tools</strong><br />
<a href="search.php?s=student">Find Another Student</a><br />
<a href="search.php?s=classes">Search Class Discussion</a></p>
<p class="menuitem"><a href="help.php">Help!</a><br />

</td><td>
