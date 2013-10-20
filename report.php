<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }
if ($_SESSION["instructorFlag"] != 1) { redirect('home.php'); die(); }
if (!($_GET["type"] == "1" or $_GET["type"] == "2")) { redirect('instructors.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();

$logresult = write_log($_SESSION["username"] . " opened the instructor panel to examine id " . $_GET["id"] . " of type " . $_GET["type"]);

echo '<p><strong>Class Report - ';
if ($_GET["type"] == "1") echo 'Display List of Students and Number of Comments';
elseif ($_GET["type"] == "2") echo 'Display Each Student And The Content of Their Comments';
echo '</strong></p>';
echo '<p><a href="instructors.php">Click here to return to the previous page.</a></p>';
if ($_GET["type"] == 1) {
	$query = "SELECT s.`first`, s.`last`, s.`username`, s.`anonNick`, s.`studentEmail`, i.`type`, COUNT(csu.`statusText`) AS comments FROM `classStatusUpdates` AS csu LEFT JOIN `student` AS s ON csu.`studentKey` = s.`studentKey` LEFT JOIN `instructors` AS i ON csu.`classKey`=i.`classKey` AND i.`studentKey`=" . $_SESSION["studentKey"] .	 " WHERE csu.`classKey`='" . intval($_GET["id"]) . "' GROUP BY csu.`studentKey` ORDER BY s.`username`"; 
	$result = myquery($query);
	if (mysql_num_rows($result) > 0) {
		echo '<table class="report">';
		echo '<tr><th>Username</th><th>Nickname</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Comments</th></tr>';
		while ($row = mysql_fetch_array($result)) {
			if (is_null($row["type"])) die();
			echo '<tr>';
			echo '<td>' . $row["username"] . '</td>';
			echo '<td>' . $row["anonNick"] . '</td>';
			echo '<td>' . $row["first"] . '</td>';
			echo '<td>' . $row["last"] . '</td>';
			echo '<td>' . $row["studentEmail"] . '</td>';
			echo '<td>' . $row["comments"] . '</td>';
			echo '</tr>';
		}	
		echo '</table>';
		echo '<p><br /><i>Note: </i>Nicknames only appear in this table if the student has chosen to appear anonymously.</p>';
	} else echo '<p><i>No comments found.</i></p>';
} elseif ($_GET["type"] == 2) {
	$query = "SELECT csu.`statusText`,UNIX_TIMESTAMP(csu.`dateUpdated`) AS `dateUpdated`, s.`username`, s.`first`, s.`last`, s.`anonNick`, s.`studentEmail`, i.`type` FROM `classStatusUpdates` AS csu LEFT JOIN `student` AS s ON csu.`studentKey` = s.`studentKey` LEFT JOIN `instructors` AS i ON csu.`classKey`=i.`classKey` AND i.`studentKey`=" . $_SESSION["studentKey"] .	 " WHERE csu.`classKey` = '" . $_GET["id"] . "' ORDER BY s.`username`,csu.`dateUpdated`";
	$result = myquery($query);
	if (mysql_num_rows($result) > 0) {
		$lastUser = "";
		
		echo '<table class="report">';
		while ($row = mysql_fetch_array($result)) {
			if (is_null($row["type"])) die();
			if ($row["username"] != $lastUser) {
				echo '<tr>';
				echo '<th>' . $row["username"] . '</th>';
				echo '<th>' . $row["first"] . " " . $row["last"];
				if (!is_null($row["anonNick"])) echo " (" . $row["anonNick"] . ')';
				echo '</th>';
				echo '<th>' . $row["studentEmail"] . '</th>';
				echo '</tr>';
				$lastUser = $row["username"];
			}
			echo '<tr>';
			echo '<td>&nbsp;</td>';
			echo '<td>' . date("n/d g:ia", $row["dateUpdated"]) . '</td>';
			echo '<td>' . htmlspecialchars($row["statusText"]) . '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<p><br /><i>Note: </i>Nicknames only appear in this table if the student has chosen to appear anonymously.</p>';
	} else echo '<p><i>No comments found.</i></p>';

}
endbody();
mysql_close();
?>
