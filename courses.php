<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();

$logresult = write_log($_SESSION["username"] . " browsed courses");

printmenu_start();
?>
<p><strong>Browse Current Courses</strong></p>
<p>To open the discussion from one of your current courses, use the links under <strong>My Class Discussions</strong> on the left.</p>
<p>To read the public discussion from another course, click on that cousre.</p>
<p><strong>Current Courses:</strong></p>
<?
$query = "SELECT `classKey`,`classNumber`,`sectionNumber`,`courseTitle`,`instructor` FROM `classes` WHERE `sectionNumber` > 0 ORDER BY `classNumber`,`instructor`,`sectionNumber`";
$result = myquery($query);

echo '<table class="courseList">';
while ($row = mysql_fetch_array($result)) {
	echo '<tr>';
	echo '<td class="statnum">' . $row["classNumber"] . '</td>';
	echo '<td>(' . $row["sectionNumber"] . ') <a href="course.php?id=' . $row["classKey"] . '">' . trim($row["courseTitle"]) . '</a></td>';
	echo '<td class="inst">' . $row["instructor"] . '</td>';
	echo '<tr>';
}
echo '</table>';

printmenu_end();
endbody();
mysql_close();
?>
