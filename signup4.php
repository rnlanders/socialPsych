<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 4) { redirect('index.php'); die(); }

require('config.php');

if (isset($_POST["check4"])) {
	if ($_POST["bigFiveUse"] == "0") $bigFiveUse = 0; else $bigFiveUse = 1;
	
	$sqldata = array(
		"studentKey"=>$_SESSION["studentKey"],
		"sex"=>intval($_POST["sex"]),
		"age"=>$_POST["age"],
		"relationshipStatus"=>intval($_POST["relationship"]),
		"hometown"=>$_POST["hometown"],
		"schoolStatus"=>intval($_POST["year"]),
		"employer"=>$_POST["employer"],
		"workStatus"=>intval($_POST["hours"]),
		"major"=>$_POST["major"],
		"favorite"=>$_POST["favorite"],
		"clubs"=>$_POST["clubs"],
		"activities"=>$_POST["activities"],
		"interests"=>$_POST["interests"],
		"faveMusic"=>$_POST["faveMusic"],
		"faveTV"=>$_POST["faveTV"],		
		"faveBooks"=>$_POST["faveBooks"],		
		"bigFiveUse"=>$bigFiveUse
	);
	
	$result = mysql_insert_array("studentProfile", $sqldata);
	
	$sqldata = array(
		"signupStage"=>5,
	);
		
	mysql_update_array("student", $sqldata, "`studentKey` = " . $_SESSION["studentKey"], FALSE, FALSE);
	
	$_SESSION["signupStage"] = 5;
	mysql_close();
	redirect('signup5.php');
}

printhead();
startbody();

?>
<h2>socialPsych, the ODU Department of Psychology's Online Social Network</h2>
<p style="text-align: center">You are currently logged in.  Your selections on the previous pages have been saved.  If you wish to stop and return to the setup process later, you must logout by <a href="index.php?logoff=1">clicking on this link</a> BEFORE working on the rest of this page.</p>
<h4>Profile Items</h4>
<p>Please note - your responses to this section <strong>will be visible </strong>to other students in your profile. If you set your profile to be anonymous earlier, your real name will <strong>not </strong>be visible on your profile. If you do not want other students to see your answer to an item below, leave it blank. You can change your profile later.</p>
<? if (count($errors) > 0) echo '<p style="color:red">You must respond to every question to continue.  You are currently missing responses to the following item(s): ' . implode(", ", $errors); ?>
<form method="post" action="signup4.php">
<table class="profile">
<tr><td class="header" colspan="2">Basic Information</td></tr>
<tr>
  <td class="items">Sex:</td>
  <td ><input type="radio" name="sex"<? if ($_POST["sex"] == 1) echo " checked"; ?> value="1" />
Female <br />
<input type="radio" name="sex"<? if ($_POST["sex"] == 2) echo " checked"; ?> value="2" />
Male<br />
<input type="radio" name="sex"<? if ($_POST["sex"] == 3) echo " checked"; ?> value="3" />
Other<br />
<input type="radio" name="sex"<? if ($_POST["sex"] == 9 or !isset($_POST["sex"])) echo " checked"; ?> value="9" />
<em>Don't Include This On My Profile</em></td>
</tr>
<tr>
  <td class="items">Age:</td><td><input maxlength="50" type="text" name="age" value="<? echo htmlspecialchars($_POST["age"]); ?>" /></td></tr>	
<tr>
  <td class="items">Relationship Status:</td>
  <td><span style="width: 300px;">
    <input type="radio" name="relationship"<? if ($_POST["relationship"] == 1) echo " checked"; ?> value="1" />
Single <br />
<input type="radio" name="relationship"<? if ($_POST["relationship"] == 2) echo " checked"; ?> value="2" /> 
In a relationship
<br />
<input type="radio" name="relationship"<? if ($_POST["relationship"] == 3) echo " checked"; ?> value="3" /> 
Married
<br />
<input type="radio" name="relationship"<? if ($_POST["relationship"] == 9 or !isset($_POST["sex"])) echo " checked"; ?> value="9" />
<em>Don't Include This On My Profile</em></span></td>
</tr>
<tr class="linebelow">
  <td class="items">Hometown:</td><td><input maxlength="50" type="text" name="hometown" value="<? echo htmlspecialchars($_POST["hometown"]); ?>" /></td></tr>
<tr>
  <td class="header" colspan="2">School Details</td>
</tr>
<tr>
  <td class="items">School Status:</td>
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
<input type="radio" name="year"<? if ($_POST["year"] == 9 or !isset($_POST["year"])) echo " checked"; ?> value="9" />
<em>Don't Include This On My Profile</em></td>
</tr>
<tr>
  <td class="items">Employer:</td>
  <td><input maxlength="100" type="text" name="employer" value="<? echo htmlspecialchars($_POST["employer"]); ?>" /></td>
</tr>
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
  <em>Don't Include This On My Profile</em><br /></td>
</tr>
<tr>
  <td class="items">Major:</td>
  <td><input maxlength="50" type="text" name="major" value="<? echo htmlspecialchars($_POST["major"]); ?>" /></td>
</tr>
<tr>
  <td class="items">Favorite Class:</td>
  <td><input maxlength="50" type="text" name="favorite" value="<? echo htmlspecialchars($_POST["favorite"]); ?>" /></td>
</tr>
<tr>
  <td class="items">Clubs/Organizations:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="clubs"><? echo htmlspecialchars($_POST["clubs"]); ?></textarea></td>
</tr>
<tr>
  <td class="header" colspan="2">Personal Details</td>
</tr>
<tr>
  <td class="items">Activities:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="activities"><? echo htmlspecialchars($_POST["activities"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Interests:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="interests"><? echo htmlspecialchars($_POST["interests"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Favorite Music:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="faveMusic"><? echo htmlspecialchars($_POST["faveMusic"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Favorite TV/Movies:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="faveTV"><? echo htmlspecialchars($_POST["faveTV"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Favorite Books:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="faveBooks"><? echo htmlspecialchars($_POST["faveBooks"]); ?></textarea></td>
</tr>
<?
if ($_SESSION["instructorFlag"] == 1) echo '<input type="hidden" name="bigFiveUse" value="0">'; else {
?>
<tr>
  <td class="header" colspan="2">Big Five Profile</td>
</tr>
<tr>
  <td colspan="2"><p>Earlier, you completed a personality profile, which appears in the graph below. Would you like this graph visible to other students in your profile?<br /><strong>NOTE: </strong>Due to a technical problem out of our control, the colors in your Big Five profile chart may appear distorted or text labels may be missing.  This problem should be automatically repaired soon.<br /><img src="personality.php?id=<? echo $_SESSION["studentKey"]; ?>" />
</p>
    </td>
</tr>
<tr>
  <td colspan="2"><input type="radio" name="bigFiveUse"<? if ($_POST["bigFiveUse"] == "1" or !isset($_POST["bigFiveUse"])) echo " checked"; ?> value="1" /> 
    Yes, I would like other students to see my personality profile.
<br />
<input type="radio" name="bigFiveUse"<? if ($_POST["bigFiveUse"] == "0") echo " checked"; ?> value="0" /> 
No, I would not like other students to see my personality profile.
</td>
</tr>
<? } ?>
</table>
<input type="hidden" name="check4" value="1" />
<p><input type="submit" value="Continue to the Next Page of Profile Setup" /></p>
</form>
<?

endbody();

?>
