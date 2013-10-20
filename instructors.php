<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }
if ($_SESSION["instructorFlag"] != 1) { redirect('home.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();

$logresult = write_log($_SESSION["username"] . " opened the instructor tools panel");

printmenu_start();

echo '<p><strong>Instructor Tools</strong></p>';

$query = "SELECT i.`classKey`, c.`sectionNumber`, c.`classNumber`, c.`courseTitle` FROM `instructors` AS i LEFT JOIN `classes` AS c ON i.`classKey`=c.`classKey` WHERE i.`studentKey` = " . $_SESSION["studentKey"];
$result = myquery($query);
if (mysql_num_rows($result) == 0) die();

echo '<table>';

while ($row = mysql_fetch_array($result)) {
	echo '<tr>';
	echo '<td><strong>' . $row["classNumber"] . '</strong></td>';
	echo '<td><strong>' . $row["courseTitle"] . ' (' . $row["sectionNumber"] . ')</strong></td>';
	echo '</tr>';
	echo '<tr><td>&nbsp;</td><td><a href="report.php?type=1&id=' . $row["classKey"] . '">Display List of Students and Number of Comments</a></td></tr>';
	echo '<tr><td>&nbsp;</td><td><a href="report.php?type=2&id=' . $row["classKey"] . '">Display Each Student And The Content of Their Comments</a></td></tr>';
	echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
}

echo '</table>';

printmenu_end();
endbody();
mysql_close();
?>
