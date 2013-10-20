<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

if (trim($_POST["update"]) != "") {
	$logresult = write_log($_SESSION["username"] . " updated his status to: " . safe(trim($_POST["update"])));
	$sqldata = array(
		"studentKey"=>safe($_SESSION["studentKey"]),
		"statusDateUpdated"=>"NOW()",
		"statusText"=>'"' . safe(trim($_POST["update"])) . '"'		
	);
	if (time() - $_SESSION["lastPost"] > 3) mysql_insert_array("statusUpdates", $sqldata, FALSE, FALSE);
	$_SESSION["lastPost"] = time();

	redirect('home.php');
	die();
}

$logresult = write_log($_SESSION["username"] . " looked at the home news feed");

printhead();
startbody();

printmenu_start();

echo '<p style="margin: 0 0 1em 0;"><strong>MESSAGE FROM TNTLAB:</strong> Due to student requests, the text length limit in course discussion has been increased from 500 to 1000 characters.<br /><br />If you have encounter any errors or have any problems using socialPsych, remember to e-mail <a href="mailto:tntlab@odu.edu">tntlab@odu.edu</a> or <a href="http://social.tntlab.org/message.php?sid=13">message Rachel</a> instead of asking your instructor.  No question, comment, or suggestion is too small!</p>';

?>
<p><strong>Update Your Status</strong></p>
<form method="post" action="home.php">
<p style="text-align:right"><textarea class="status" name="update" onkeyup="textLimit(this, 255);"></textarea><br /><input value="Update My Status" type="submit" id="statussubmit"></p>
</form>
<p><strong>Updates From Your Current Classmates and Instructors</strong></p>
<?

$query = "SELECT DISTINCT su.`studentKey`, su.`statusText`, UNIX_TIMESTAMP(su.`statusDateUpdated`) as `unixDate`, s.`anon`, s.`anonNick`, s.`first`, s.`last`, s.`instructorFlag` FROM `studentsInClasses` AS sic1
LEFT JOIN `studentsInClasses` AS sic2 ON sic1.`classKey` = sic2.`classKey`
LEFT JOIN `statusUpdates` AS su ON sic2.`studentKey` = su.`studentKey`
LEFT JOIN `student` AS s ON sic2.`studentKey`=s.`studentKey` 
WHERE sic1.`studentKey`=" . $_SESSION["studentKey"] . " AND su.`statusText` IS NOT NULL
ORDER BY `statusDateUpdated` DESC LIMIT 50;";
$result = myquery($query);
if (mysql_num_rows($result) == 0) {
	$query = "SELECT `studentKey`, `statusText`, UNIX_TIMESTAMP(`statusDateUpdated`) as `unixDate` FROM `statusUpdates` WHERE `studentKey` = " . $_SESSION["studentKey"] . " ORDER BY `statusDateUpdated` DESC";
	$result = myquery($query);
}
echo '<table class="status">';
while ($row = mysql_fetch_array($result)) {
	echo '<tr><td class="';
	if ($row["instructorFlag"] == 1) echo 'inststatus';
	else echo 'statuspic';
	echo '"><img src="profilePic.php?thumb=1&id=' . $row["studentKey"] . '&t='. date("Gs") . 
		'"></td><td';
	if ($row["instructorFlag"] == 1) echo ' class="inst"';
	echo '><strong><a href="';
	if ($row["studentKey"] == $_SESSION["studentKey"]) echo 'myprofile.php">';
	else echo 'profile.php?id=' . $row["studentKey"] . '">';
	if ($row["anon"] == 1) echo $row["anonNick"] . "</a>";
	else echo $row["first"] . " " . $row["last"] . "</a>";
	if ($row["anon"] == 1 and $row["instructorFlag"] != "1") echo "*";
	echo "</strong> " . nl2br(htmlspecialchars($row["statusText"]));
	echo '<p class="time">';
	echo unixToText($row["unixDate"]);	
	echo '</p></td></tr>';
	echo '<tr><td colspan="2"><hr class="statushr" /></td></tr>';
}
echo "</table>";

printmenu_end();
endbody();
mysql_close();
?>
