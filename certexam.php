<?
$certdays = 4;

require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }
if (!isset($_POST["classID"]) and !isset($_SESSION["classID"])) { redirect('certification.php?e=1'); die(); } 

require('config.php');

if (isset($_POST["classID"])) $classID = safe($_POST["classID"]); else $classID = $_SESSION["classID"];
$query = "SELECT `currentTesting`,UNIX_TIMESTAMP(`currentTesting`) as `unixCurrentTesting`,(UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`currentTesting`)) AS sinceStarted,`classNumber`,(UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`lastTested`)) AS `lastTest` FROM `certifiedStudents` WHERE `studentKey`=" . $_SESSION["studentKey"] . " AND `classNumber` = '" . $classID . "'";
$result = myquery($query);

if (mysql_num_rows($result) == 0) {
	$sqldata = array(
		"`studentKey`"=>$_SESSION["studentKey"],
		"`classNumber`"=>$classID
	);
	mysql_insert_array("certifiedStudents",$sqldata);
	$query = "SELECT `currentTesting`,UNIX_TIMESTAMP(`currentTesting`) as `unixCurrentTesting`,(UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`currentTesting`)) AS sinceStarted,`classNumber`,(UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`lastTested`)) AS `lastTest` FROM `certifiedStudents` WHERE `studentKey`=" . $_SESSION["studentKey"] . " AND `classNumber` = '" . $classID . "'";
	$result = myquery($query);

}

$class = mysql_fetch_array($result);

$_SESSION["classID"] = $class["classNumber"];

if ($class["lastTest"] < (86400 * $certdays) and is_null($class["currentTesting"]) and (!isset($_POST["classID"]) and !isset($_SESSION["classID"]))) { redirect('certification.php?e=2'); die(); }
if ($class["lastTest"] < (86400 * $certdays) and !is_null($class["lastTest"])) { redirect('certification.php?e=3'); die(); }

if ($class["sinceStarted"] > 630) {
	$query = "UPDATE `certifiedStudents` SET `currentTesting`=NULL,`lastTested`=NOW() WHERE `studentKey` = " . $_SESSION["studentKey"] . " AND `classNumber` = '" . $class["classNumber"] . "'";
	$result = myquery($query);
	redirect('certification.php?e=4'); die();
}
if (isset($_SESSION["classID"]) and isset($_POST["certsubmit"])) {
	$logresult = write_log($_SESSION["username"] . " submitted cert exam for " . $_SESSION["classID"]);
	
	$qs = array();
	
	foreach ($_POST as $field=>$value) if (substr($field, 0, 1) == "q") $qs[intval(substr($field,1))] = intval($value); 
	//foreach ($qs as $field=>$value) echo $field . ":" . $value . " ";

	$query = "SELECT `questionKey`,`correctOption` FROM `classQuestionList` WHERE `questionKey` IN (" . implode(array_keys($qs), ",") . ")";
	$result = myquery($query);
	
	$numcorrect = 0;
	$inserttime = date ("Y-m-d H:i:s");
	
	while($row = mysql_fetch_array($result)) {
		if ($row["correctOption"] == $qs[$row["questionKey"]]) $numcorrect++;
		$sqldata = array(
			"`itemRecorded`"=>"'" . $inserttime . "'",
			"`studentKey`"=>$_SESSION["studentKey"],
			"`questionKey`"=>$row["questionKey"],
			"`response`"=>$qs[$row["questionKey"]],
			"`correct`"=>$row["correctOption"]
		);
		mysql_insert_array("surveyAnswers",$sqldata,FALSE,FALSE);
	}
	$query = "SELECT `rank` FROM `certifiedStudents` WHERE `studentKey` = " . $_SESSION["studentKey"] . " AND `classNumber` = '" . $_SESSION["classID"] . "'";
	$result = myquery($query);
	
	$row = mysql_fetch_array($result);
	$rank = $row["rank"];
	
	if ($rank == 0) $threshold = 3;
	elseif ($rank == 1) $threshold = 5;
	elseif ($rank == 2) $threshold = 7;
	elseif ($rank == 3) $threshold = 9;
	elseif ($rank == 4) $threshold = 10;
	else die();

	if (intval($numcorrect) >= intval($threshold))
		$sqldata = array(
			"`lastTested`"=>"NOW()",
			"`currentTesting`"=>"NULL",
			"`rank`"=>"`rank`+1"
		);
	else 
		$sqldata = array(
			"`lastTested`"=>"NOW()",
			"`currentTesting`"=>"NULL",
		);
	
	mysql_update_array("certifiedStudents", $sqldata, "`studentKey`=" . $_SESSION["studentKey"] . " AND `classNumber`='" . $_SESSION["classID"] . "'", FALSE, FALSE); 
	unset($_SESSION["classID"]);
	if ($numcorrect >= $threshold) redirect('certification.php?rank=1');
	else redirect('certification.php?rank=0');
	die();
}

$query = "UPDATE `certifiedStudents` SET `currentTesting`=NOW() WHERE `studentKey` = " . $_SESSION["studentKey"] . " AND `currentTesting` IS NULL AND `classNumber` = '" . $class["classNumber"] . "'";
$result = myquery($query);

$logresult = write_log($_SESSION["username"] . " started taking cert exam for " . $class["classNumber"]);

printhead("p { margin: 0 0 1em 0; }");
startbody();
printmenu_start();

echo '<p><strong>Certification Exam - PSYC 	' . $class["classNumber"] . '</strong></p>';
echo '<p>You have 10 minutes to complete this exam.  If you do not submit your exam within 10 minutes, you cannot gain a certification rank.</p>';
if (is_null($class["currentTesting"])) echo '<form name="cd">Time Remaining: <input id="txt" readonly="true" type="text" value="10:00" border="0" name="disp"></form>';
else echo '<strong>Time When Page Opened:</strong> ' . date("F jS g:ia", time()+7200) . '<br><strong>Must Submit By:</strong> ' . date("F jS g:ia", $class["unixCurrentTesting"]+7200+600) . '</p>';

$query = "SELECT `questionKey`,`classNumber`,`questionText`,`option1`,`option2`,`option3`,`option4`,`option5` FROM `classQuestionList` WHERE `classNumber` = '" . $class["classNumber"] . "' ORDER BY RAND() LIMIT 10";
$result = myquery($query);

echo '<form method="post" action="certexam.php">';

while ($question = mysql_fetch_array($result)) {
	echo '<div class="questions"><p>' . ++$i . ". " . $question["questionText"] . '</p></div>';
	echo '<div class="responses"><p><input type="radio" name="q' . $question["questionKey"] . '" value="1">' . $question["option1"] . '<br />';
	echo '<input type="radio" name="q' . $question["questionKey"] . '" value="2">' . $question["option2"] . '<br />';
	echo '<input type="radio" name="q' . $question["questionKey"] . '" value="3">' . $question["option3"] . '<br />';
	echo '<input type="radio" name="q' . $question["questionKey"] . '" value="4">' . $question["option4"] . '<br />';
	if (trim($question["option5"]) != "") echo '<input type="radio" name="q' . $question["questionKey"] . '" value="5">' . $question["option5"] . '<br />';
	echo '<input type="radio" name="q' . $question["questionKey"] . '" value="9" checked><strong>Don\'t Know/Give Up</strong></p></div>';
}
echo '<div class="questions"><input type="hidden" name="certsubmit" value="1"><input type="submit" value="Submit My Certification Exam">';

echo '</form>';

printmenu_end();
endbody();
mysql_close();
?>
