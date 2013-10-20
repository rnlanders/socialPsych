<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 2) { redirect('index.php'); die(); }

require('config.php');

$query = "SELECT `classKey`,`classNumber`,`courseTitle` FROM `classes` WHERE `instructor`='X' ORDER BY `classNumber`" ;
$classListResult = myquery($query);

$errors = array();

if (isset($_POST["check2"])) {
	if ($_POST["gpa"] == "") array_push($errors, "GPA");
	if ($_POST["sat"] == "") array_push($errors, "SAT Scores");
	if ($_POST["act"] == "") array_push($errors, "ACT Scores");	
	if ($_POST["age"] == "") array_push($errors, "Current Age");	
	if ($_POST["year"] == "") array_push($errors, "Current Year in School");	
	if ($_POST["major"] == "") array_push($errors, "Major");	
	if (!isset($_POST["hours"]) or !intval($_POST["hours"])) array_push($errors, "Work Status");	
	if (!isset($_POST["sex"]) or !intval($_POST["sex"])) array_push($errors, "Sex");	
	if (!isset($_POST["race"]) or !intval($_POST["race"])) array_push($errors, "Race");	
	if (!isset($_POST["ethnicity"]) or !intval($_POST["ethnicity"])) array_push($errors, "Ethnicity");	
	for ($i = 1; $i <= 60; $i++) if (!isset($_POST["commit" . $i]) or !intval($_POST["commit" . $i])) array_push($errors, "Commitment Q" . $i);
	
	if (count($errors) == 0) {
		while ($row = mysql_fetch_array($classListResult)) {
			$classname = "course" . $row["classKey"];
			if (isset($_POST[$classname])) {
				$query = "INSERT INTO `studentsInClasses` (`studentKey`,`pastStudent`,`classKey`) VALUES (" . $_SESSION["studentKey"] . ",1," . $row["classKey"] . ")";
				$result = myquery($query);
			}
		}
		$sqldata = array(
			"signupStage"=>3,
			"gpa"=>$_POST["gpa"],
			"sat"=>$_POST["sat"],
			"act"=>$_POST["act"],
			"age"=>$_POST["age"],
			"hours"=>$_POST["hours"],
			"major"=>$_POST["major"],
			"year"=>intval($_POST["year"]),
			"sex"=>intval($_POST["sex"]),
			"race"=>intval($_POST["race"]),
			"ethnicity"=>intval($_POST["ethnicity"])
		);
		for ($i = 1; $i <= 60; $i++) {
			$name = "commit" . $i;
			$commitsqls[$name] = intval($_POST[$name]);
		}
		$sqldata = array_merge($sqldata, $commitsqls);
		
		mysql_update_array("student", $sqldata, "`studentKey` = " . $_SESSION["studentKey"]);
		$result = myquery($query);

		mysql_close();
		$_SESSION["signupStage"] = 3;
		redirect("signup3.php");
	}
}

printhead();
startbody();

$commits = array(
//affective
"I would be very happy to spend the rest of my time in college at Old Dominion University.", //1
"I enjoy discussing Old Dominion with people outside it.", //2
"I really feel as if Old Dominion's problems are my own.", //3
"I think that I could easily become as attached to another school as I am to this one.", //R 4
"I do not feel like 'part of the family' at Old Dominion.", //R 5
"I do not feel 'emotionally attached' to Old Dominion.", //R 6
"Old Dominion has a great deal of personal meaning for me.", //7
"I do not feel a strong sense of belonging to Old Dominion.", //R 8
//continuance
"I am not afraid of what might happen if I left Old Dominion without being accepted at another school first.", //R 9
"It would be very hard for me to leave Old Dominion right now, even if I wanted to.", //10
"Too much in my life would be disrupted if I decided I wanted to leave Old Dominion now.", //11
"It wouldn't be too costly for me to leave Old Dominion now.", //R 12
"Right now, staying with Old Dominion is a matter of necessity as much as desire.", //13
"I feel that I have too few options to consider leaving Old Dominion.", //14
"One of the few serious consequences of leaving Old Dominion would be the scarcity of available alternatives.", //15
"One of the major reasons I continue to go to school at Old Dominion is that leaving would require considerable personal sacrifice - another school may not match the overall benefits I have here.", //16
//normative
"I think that people these days move from school to school too often.", //17
"I do not believe that a person must always be loyal to his or her school.", //R 18
"Jumping from school to school does not seem at all unethical to me.", //R 19
"One of the major reasons I continue to go to Old Dominion is that I believe that loyalty is important and therefore feel a sense of moral obligation to remain.", //20
"If a better school asked me to transfer, I would not feel it was right to leave Old Dominion.", //21
"I was taught to believe in the value of remaining loyal to one organization.", //22
"Things were better in the days when people stayed with one school for their entire time in college.", //23
"I do not think that wanting to be the 'big man/woman on campus' (popular and highly involved in campus activities) is sensible anymore.", //R 24
//evaluation of department
"I enjoy course offerings from the Psychology department.", //25
"The instructors in the Psychology department are effective teachers.", //26
"I like the Psychology department.", //27
"The Psychology department is supportive of me.", //28
"I enjoy taking courses in Psychology.", //29
//psychology self-concept
"I am just not good in Psychology.", //R 30
"I get good grades in Psychology.", //31
"I learn Psychology concepts quickly.", //32
"I have always believed Psychology was one of my best subjects.", //33
"In my Psychology courses, I understand even the most difficult concepts.", //34
//psychology interest
"I enjoy reading about Psychology.", //35
"I look forward to Psychology courses.", //36
"I take courses in Psychology because I enjoy it.", //37
"I am interested in the things I learn in Psychology.", //38
//anxiety from Ferla Valcke Cai 2009 from PISA based on Wigfield and Meece
"I often worry that it will be difficult for me in Psychology classes.", //39
"I get very tense when I have to do Psychology homework.", //40
"I get very nervous working on Psychology homework.", //41
"I feel helpless when working on Psychology homework.", //42
"I worry that I will get poor grades in Psychology courses.", //43
//utrecht work engagement scale
"When I get up in the morning, I feel like going to class.", //vigor 44
"When I'm doing my work as a student, I feel bursting with energy.", //vigor 45
"As far as my studies are concerned I always persevere, even when things are not going well.", //vigor 46
"I can continue studying for long periods of time.", ///vigor 47
"I am very resiliant, mentally, as far as my studies are concerned.", //vigor 48
"I feel strong and vigorous when I'm studying or going to class.", //vigor 49
"To me, my studies are challenging.", //dedication 50
"My studies inspire me.", //dedication 51
"I am enthusiastic about my studies.", //dedication 52
"I am proud of my studies.", //dedication 53
"I find my studies full of meaning and purpose.", //dedication 54
"When I am studying, I forget everything else around me.", //absorption 55
"Time flies when I am studying.", //absorption 56
"I get carried away when I am studying.", //absorption 57
"It is difficult to detach myself from my studies.", //absorption 58
"I am immersed in my studies.", //absorption 59
"I feel happy when I am studying intensely." //absorption 60
);

?>
<h2>socialPsych, the ODU Department of Psychology's Online Social Network</h2>
<p style="text-align: center">You are currently logged in.  Your selections on the previous pages have been saved.  If you wish to stop and return to the setup process later, you must logout by <a href="index.php?logoff=1">clicking on this link</a> BEFORE working on the rest of this page.</p>
<h4>Background Items</h4>
<p>Please note - your responses to this section will be used as background information for your instructor and/or the researchers.  <strong>None of these responses will ever be visible to other students.</strong> Please answer honestly and completely.  If you need to look up a precise number to answer a question, please do so.  If you cannot remember or find the number, give your best guess.</p>
<? if (count($errors) > 0) echo '<p style="color:red">You must respond to every question to continue.  You are currently missing responses to the following item(s): ' . implode(", ", $errors); ?><br />
<form method="post" action="signup2.php">
<table class="demos">
<tr>
  <td colspan = "2">For any of the following if you do not remember and cannot look up the answer, type <strong>I do not remember.</strong>  If you did not take the SAT and/or ACT, type <strong>I did not take this test </strong> for that entry.</td>
</tr>
<tr><td style="width:200px;">Current GPA:</td><td style="width: 300px;"><input maxlength="25" type="text" name="gpa" value="<? echo htmlspecialchars($_POST["gpa"]); ?>" /></td></tr>
<tr><td>SAT Scores:</td><td><input maxlength="25" type="text" name="sat" value="<? echo htmlspecialchars($_POST["sat"]); ?>" /></td></tr>	
<tr><td>ACT Scores:</td><td><input maxlength="25" type="text" name="act" value="<? echo htmlspecialchars($_POST["act"]); ?>" /></td></tr>
<tr><td>Major:</td><td><input maxlength="25" type="text" name="major" value="<? echo htmlspecialchars($_POST["major"]); ?>" /></td></tr>
<tr class="linebelow"><td>Age (in years):</td><td><input maxlength="25" type="text" name="age" value="<? echo htmlspecialchars($_POST["age"]); ?>" /></td></tr>
<tr>
  <td class="items">Work Status:</td>
  <td><input type="radio" name="hours"<? if ($_POST["hours"] == 1) echo " checked"; ?> value="1" />
    Not currently employed
    <br />
  <input type="radio" name="hours"<? if ($_POST["hours"] == 2) echo " checked"; ?> value="2" />
  1-10 hours per week<br />
  <input type="radio" name="hours"<? if ($_POST["hours"] == 3) echo " checked"; ?> value="3" />
  11-20 hours per week<br />
  <input type="radio" name="hours"<? if ($_POST["hours"] == 4) echo " checked"; ?> value="4" />
  21-30 hours per week<br />
  <input type="radio" name="hours"<? if ($_POST["hours"] == 5) echo " checked"; ?> value="5" />
  31-40 hours per week<br />
  <input type="radio" name="hours"<? if ($_POST["hours"] == 6) echo " checked"; ?> value="6" />
  41+ hours per week<br />
  <input type="radio" name="hours"<? if ($_POST["hours"] == 9 or !isset($_POST["hours"])) echo " checked"; ?> value="9" />
  <em> <em>I Choose Not to Answer</em></em><br /></td>
</tr>
<tr>
  <td>Most recent year in school:</td>
  <td><input type="radio" name="year"<? if ($_POST["year"] == 1) echo " checked"; ?> value="1" />
    Freshman (1st year)<br />
    <input type="radio" name="year"<? if ($_POST["year"] == 2) echo " checked"; ?> value="2" />
    Sophomore (2nd year)<br />
    <input type="radio" name="year"<? if ($_POST["year"] == 3) echo " checked"; ?> value="3" />
    Junior (3rd year)<br />
    <input type="radio" name="year"<? if ($_POST["year"] == 4) echo " checked"; ?> value="4" />
    Senior (4th year or more)<br />
    <input type="radio" name="year"<? if ($_POST["year"] == 5) echo " checked"; ?> value="5" />
    Non-degree-seeking<br />
    <input type="radio" name="year"<? if ($_POST["year"] == 6) echo " checked"; ?> value="6" />
    Returning/Non-traditional Student<br />
    <input type="radio" name="year"<? if ($_POST["year"] == 9) echo " checked"; ?> value="9" />
    <em>I Choose Not to Answer</em></td>
</tr>
<tr>
  <td>Sex:</td>
  <td><input type="radio" name="sex"<? if ($_POST["sex"] == 1) echo " checked"; ?> value="1" />
  Female
  <br />
  <input type="radio" name="sex"<? if ($_POST["sex"] == 2) echo " checked"; ?> value="2" />
  Male<br />
  <input type="radio" name="sex"<? if ($_POST["sex"] == 3) echo " checked"; ?> value="3" />
  Other<br />
  <input type="radio" name="sex"<? if ($_POST["sex"] == 9) echo " checked"; ?> value="9" />
  <em>I Choose Not to Answer</em></td>
</tr>
<tr>
  <td>Race:</td>
  <td><input type="radio" name="race"<? if ($_POST["race"] == 1) echo " checked"; ?> value="1" />
  White<br />
  <input type="radio" name="race"<? if ($_POST["race"] == 2) echo " checked"; ?> value="2" />
  Black or African American<br />
  <input type="radio" name="race"<? if ($_POST["race"] == 3) echo " checked"; ?> value="3" />
  American Indian or Alaskan Native<br />
  <input type="radio" name="race"<? if ($_POST["race"] == 4) echo " checked"; ?> value="4" />
  Asian<br />
  <input type="radio" name="race"<? if ($_POST["race"] == 5) echo " checked"; ?> value="5" />
  Native Hawaiian or Other Pacific Islander<br />
  <input type="radio" name="race"<? if ($_POST["race"] == 6) echo " checked"; ?> value="6" />
  Multiracial/Other<br />
  <input type="radio" name="race"<? if ($_POST["race"] == 9) echo " checked"; ?> value="9" />
  <em> <em>I Choose Not to Answer</em></em><br /></td>
</tr>
<tr>
  <td>Ethnicity:</td>
  <td><input type="radio" name="ethnicity"<? if ($_POST["ethnicity"] == 1) echo " checked"; ?> value="1" />
    Hispanic<br />
    <input type="radio" name="ethnicity"<? if ($_POST["ethnicity"] == 2) echo " checked"; ?> value="2" />
    Not Hispanic<br />
    <em>
    <input type="radio" name="ethnicity"<? if ($_POST["ethnicity"] == 9) echo " checked"; ?> value="9" />
    <em>I Choose Not to Answer</em></em><br /></td>
</tr>
</table>
<h4>Opinions About College</h4>
<table class="sevenpoint">
<?
	foreach ($commits as $key=>$commit) {
		if ($key % 10 == 0) echo '<tr><td>&nbsp;</td><td class="top">Very Strongly Disagree</td><td class="top">Strongly Disagree</td><td class="top">Disagree</td><td class="top">Neither</td><td class="top">Agree</td><td class="top">Strongly Agree</td><td class="top">Very Strongly Agree</td><td class="top">Don\'t Know</td></tr>';
		$key++;
		echo '<tr><td class="first">' . "$key. $commit</td>";
		for ($i = 1; $i <= 7; $i++) {
			echo '<td><input type="radio" name="commit' . $key . '"';
			if ($_POST["commit" . $key] == $i) echo " checked";
			echo ' value="' . $i . '"></td>';
		}
		echo '<td class="righty"><input type="radio" name="commit' . $key . '" value="9"';
		if ($_POST["commit" . $key] == 9) echo " checked";
		echo '></td></tr>';
	}
echo '</table>';
/*
<h4>Course History</h4>
<p>For each of the following, mark if you have already completed the course in a previous semester at ODU.</p>
<table class="classEnrollment"> 
	$query = "SELECT `classes`.`classNumber` FROM `classes` JOIN `studentsInClasses` ON (`classes`.`classKey` = `studentsInClasses`.`classKey`) WHERE `studentsInClasses`.`studentKey`=" . $_SESSION["studentKey"];
	$result = myquery($query);
	$enrolledClasses = array();
	while ($row = mysql_fetch_array($result)) array_push($enrolledClasses,$row["classNumber"]);
	
	mysql_data_seek($classListResult, 0);
	while ($row = mysql_fetch_array($classListResult)) {
		echo '<tr><td><strong>' . $row["classNumber"] . '</strong></td><td>' . $row["courseTitle"] . '</td><td>';
		if (in_array($row["classNumber"], $enrolledClasses)) echo '<em>Currently enrolled</em>';
		else {
			echo '<input type="checkbox" name="course' . $row["classKey"] . '"';
			if (isset($_POST["course" . $row["classKey"]])) echo " checked";
			echo ' value="1">';
			echo 'Yes, I previously completed ' . $row["classNumber"];
		}
		echo '</td></tr>';
	}
*/	
	echo '</table>';
	mysql_close();

?>

<input type="hidden" name="check2" value="1" />
<p><input type="submit" value="Continue to the Next Page of Profile Setup" /></p>
</form>
<?

endbody();

?>
