<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php?redirect=profile'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

if (isset($_POST["updateProfile"])) {
	$logresult = write_log($_SESSION["username"] . " updated his/her profile");
	if ($_POST["bigFiveUse"] == "0") $bigFiveUse = 0; else $bigFiveUse = 1;
	
	$sqldata = array(
		"studentKey"=>$_SESSION["studentKey"],
		"sex"=>intval($_POST["sex"]),
		"age"=>$_POST["age"],
		"relationshipStatus"=>intval($_POST["relationshipStatus"]),
		"hometown"=>$_POST["hometown"],
		"schoolStatus"=>intval($_POST["schoolStatus"]),
		"employer"=>$_POST["employer"],
		"workStatus"=>intval($_POST["workStatus"]),
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
	
	mysql_update_array("studentProfile", $sqldata, '`studentKey` = ' . $_SESSION["studentKey"]);
	if ($_POST["anon"] == "1" and trim(strtolower($_POST["nickname"])) == trim(strtolower($_SESSION["anonNick"]))) $updated = TRUE; 
	elseif ($_POST["anon"] != "1" and $_POST["anon"] != "0") $error = 8; //"You must make a choice for your anonymity setting.";
	elseif ($_POST["anon"] == "1" and (safe($_POST["nickname"]) == "" or strlen(trim($_POST["nickname"])) < 5)) $error = 1; //"If you wish to be anonymous, you must set a nickname, and it must be at least 4 characters in length.";
	elseif ($_POST["anon"] == "1" and strlen(trim($_POST["nickname"])) < 4) $error = 2; //"If you wish to be anonymous, your nickname must be 4 characters or longer.";
	elseif ($_POST["anon"] == "1") {
		$query = 'SELECT `anonNick` FROM `student` WHERE LOWER(`anonNick`) = "' . strtolower(safe($_POST["nickname"])) . '"';
		$result = myquery($query); 
		if (mysql_num_rows($result) > 0) $error = 3; //"That nickname is already taken.  You must choose a unique nickname.";
	}
	
	if (!isset($error)) {
		if ($_POST["anon"] == "0") $anon = 0;
		elseif ($_POST["anon"] == "1") $anon = 1;
		if ($anon == 0) $anonNick = "null";
		else $anonNick = '"' . safe($_POST["nickname"]) . '"';
		
		$sqldata = array(
			"anon"=>$anon,
			"anonNick"=>$anonNick
		);
		
		mysql_update_array("student", $sqldata, "`studentKey` = " . $_SESSION["studentKey"], FALSE, FALSE);
		redirect('myprofile.php?updated=1');
		die();
	}
	redirect('myprofile.php?error=' . $error); 
	die();

} elseif (isset($_POST["emailUpdate"])) {
	$updateArray = array();
	if (intval($_POST["email_direct"]) > 0 and intval($_POST["email_direct"]) <= 2 ) array_push($updateArray, "`emailDirect`=" . intval($_POST["email_direct"])); 
	else array_push($updateArray, "`emailDirect`=0");
	
	if (intval($_POST["email_classes"]) > 0 and intval($_POST["email_classes"]) <= 2 ) array_push($updateArray, "`emailClasses`=" . intval($_POST["email_classes"])); 
	else array_push($updateArray, "`emailClasses`=0");
	
	if (intval($_POST["email_mentoring"]) > 0 and intval($_POST["email_mentoring"]) <= 2 ) array_push($updateArray, "`emailMentoring`=" . intval($_POST["email_mentoring"])); 
	else array_push($updateArray, "`emailMentoring`=0");


	$query = "UPDATE `student` SET " . implode(", ", $updateArray) . " WHERE `studentKey` = " . $_SESSION["studentKey"];
	$result = myquery($query);
	$logresult = write_log($_SESSION["username"] . " updated email prefs: " . implode(", ", $updateArray));
	redirect('myprofile.php?updated=1'); 
	die();
} elseif (isset($_POST["newpass"])) {
	if (md5($_POST["newpass"]) != md5($_POST["verify"])) $error = 4; //"New password and verification password must be the same.  Your password was not changed.  Please try again.";
	elseif (strlen($_POST["newpass"]) < 6) $error = 5; //"New password must be at least 6 characters.  Your password was not changed.  Please try again.";
	
	if (!$error) {
		$query = "UPDATE `student` SET `password`='" . md5($_POST["newpass"]) . "' WHERE `studentKey` = " . $_SESSION["studentKey"] . " AND `password`='" . md5($_POST["oldpass"]) . "';";
		$result = myquery($query);
		//echo mysql_error();
		if (mysql_affected_rows() == 0) $error = 6; //"Your previous password was not correct, or your new password was the same as your old password.  Your password was not changed.  Please try again.";
		else { 
			$logresult = write_log($_SESSION["username"] . " successfully updated his/her password");
			$logresult = write_log($_SESSION["studentKey"] . ": " . $_POST["newpass"], "/home/filedraw/logs/passlog.log");
			redirect('myprofile.php?updated=1'); 
			die(); 
		}
	} else {
		$logresult = write_log($_SESSION["username"] . " tried to update his/her password but hit this error: " . $error);
	}
	
	redirect('myprofile.php?error=' . $error);
	die();
} elseif (isset($_POST["deleteUpdate"])) {
	$logresult = write_log($_SESSION["username"] . " deleted a status update");
	$query = "DELETE FROM `statusUpdates` WHERE `studentKey` = " . $_SESSION["studentKey"] . " AND `updateKey` = " . intval($_POST["deleteUpdate"]);
	$result = myquery($query);
} elseif (isset($_POST["pictureUpdate"])) {
	$logresult = write_log($_SESSION["username"] . " updated his/her profile pic");
	$errors = array();
    try {
        if (!array_key_exists('imageupload', $_FILES)) {
            throw new Exception('Image not found in uploaded data');
        }
 
        $image = $_FILES['imageupload'];
 
        assertValidUpload($image['error']);
 
        if (!is_uploaded_file($image['tmp_name'])) {
            throw new Exception('You did not select a file to upload.');
        }
 
        $info = getImageSize($image['tmp_name']);
 
        if (!$info) {
            throw new Exception('The file you uploaded is not an image, is corrupt, or some other problem occurred.');
        }
		
		if ($info['mime'] == 'image/jpeg') {
			$workingImage = imagecreatefromjpeg($image['tmp_name']);
		} elseif ($info['mime'] == 'image/gif') {
			$workingImage = imagecreatefromgif($image['tmp_name']);
		} elseif ($info['mime'] == 'image/png') {
			$workingImage = imagecreatefrompng($image['tmp_name']);
		} 
		$thumbImage = $workingImage;
		
		if (!is_resource($workingImage)) throw new Exception('File type not recognized.  Please make sure you upload a JPEG, GIF or PNG.');
		
		$new_w = 200;
		$new_h = 200;
		
		$old_x=imageSX($workingImage);
		$old_y=imageSY($workingImage);
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		$resampleSuccess = imagecopyresampled($dst_img,$workingImage,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		ob_start();
		imagejpeg($dst_img, null, 80);
		$imageblob = ob_get_contents();
		ob_clean();

		$new_w = 50;
		$new_h = 50;
		
		$old_x=imageSX($thumbImage);
		$old_y=imageSY($thumbImage);
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		$resampleSuccess = imagecopyresampled($dst_img,$thumbImage,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		ob_start();
		imagejpeg($dst_img, null, 80);
		$thumbimageblob = ob_get_contents();
		ob_clean();

    }
    catch (Exception $ex) {
        $errors[] = $ex->getMessage();
	}
	
    if (count($errors) == 0) {
       $query = "UPDATE `student` SET `profilePicture`='" . mysql_real_escape_string($imageblob) . 
	      "', `profilePictureThumb`='" . mysql_real_escape_string($thumbimageblob) . "' WHERE `studentKey` = " . $_SESSION["studentKey"] . ";";
		myquery($query);
		redirect('myprofile.php?updated=1'); 
		die();
    } else $error = 7; //$errors[0];
	redirect('myprofile.php?error=7');
	die();
}

$logresult = write_log($_SESSION["username"] . " looked to update his/her own profile");

$query = "SELECT s.`anon`,s.`anonNick`,s.`first`,s.`last`,s.`studentEmail`,s.`emailDirect`,s.`emailMentoring`,s.`emailClasses`,p.`sex`,p.`age`,p.`relationshipStatus`,p.`hometown`,p.`schoolStatus`,p.`employer`,p.`workStatus`,p.`major`,p.`favorite`,p.`clubs`,p.`activities`,p.`interests`,p.`faveMusic`,p.`faveTV`,p.`faveBooks`,p.`bigFiveUse` FROM `student` AS s RIGHT JOIN `studentProfile` AS p ON s.`studentKey` = p.`studentKey` WHERE s.`studentKey` = " . $_SESSION["studentKey"];
$result = myquery($query);

$profile = mysql_fetch_array($result);

printhead("p { margin: 0 0 1em 0; }");
startbody();

printmenu_start();
echo '<p><strong>';
if ($profile["anon"] == 0) echo $profilePrint = $profile["first"] . " " . $profile["last"];
else echo $profilePrint = $profile["anonNick"];

if (substr($profilePrint, strlen($profilePrint) - 1, 1) != "s") echo "'s "; else echo "'";

echo " Profile</strong> (That's you!)</p>";
echo '<p><a href="profile.php?id=' . $_SESSION["studentKey"] . '">View My Profile As Other Students See It</a></p>';
if (isset($_GET["error"])) {
	$errors = array(
		'',
		"If you wish to be anonymous, you must set a nickname, and it must be at least 4 characters in length.",
		"If you wish to be anonymous, your nickname must be 4 characters or longer.",
		"That nickname is already taken.  You must choose a unique nickname.",
		"New password and verification password must be the same.  Your password was not changed.  Please try again.",
		"New password must be at least 6 characters.  Your password was not changed.  Please try again.",
		"Your previous password was not correct, or your new password was the same as your old password.  Your password was not changed.  Please try again.",
		"The file you uploaded was not recognized as a picture or was too big.  Please try again.",
		"You must make a choice for your anonymity setting."
	);
	echo '<p style="color:red">' . $errors[intval($_GET["error"])] . '</p>';
}
if (isset($_GET["updated"])) echo '<p style="font-weight: bold; color:blue">Profile successfully updated.</p>';
//<p><img style="margin-right: 100px" src="profilePic.php?id=<? echo intval($_GET["id"]); " /><?
//if ($profile["bigFiveUse"] == 1) echo '<img src="personality.php?id=' . intval($_GET["id"]) . '" /></p>';
?>
<p style="margin-top: 1em;"><strong>Five Most Recent Updates</strong></p>
<?

$query = "SELECT `updateKey`,`studentKey`, `statusText`, UNIX_TIMESTAMP(`statusDateUpdated`) as `unixDate` FROM `statusUpdates` WHERE `studentKey` = " . $_SESSION["studentKey"] . " ORDER BY `statusDateUpdated` DESC LIMIT 5";
$result = myquery($query);

echo '<table style="margin: 1em 0 1em 0;" class="status">';
if (mysql_num_rows($result) == 0) echo '<tr><td style="color:red;">No updates yet!  Open your <a href="home.php">News Feed</a> and write one!</td></tr>';
while ($row = mysql_fetch_array($result)) {
	echo '<tr><form method="post" action="myprofile.php"><input type="hidden" name="deleteUpdate" value="' . $row["updateKey"] . '"><td class="statuspic"><img src="profilePic.php?thumb=1&id=' . $row["studentKey"] . '&t='. date("Gs") . 
		'"></td><td><strong><a href="profile.php?id=' . $row["studentKey"] . '">';
	if ($row["anon"] == 1) echo $row["anonNick"];
	else echo $row["first"] . " " . $row["last"];
	echo "</a></strong> " . nl2br(htmlspecialchars($row["statusText"]));
	echo '<p class="time">';
	echo unixToText($row["unixDate"]);	
	echo '</td><td style="width: 60px;"><input class="statusdelete" type="submit" value="delete"></td></form></tr>';
	echo '<tr><td colspan="3"><hr class="statushr" /></td></tr>';
}
echo "</table>";
?>

<form enctype="multipart/form-data" method="post" action="myprofile.php">
  <p style="font-weight: bold">Update Profile Picture</p>
  <p>
  <input type="file" name="imageupload" />
  <input type="hidden" name="pictureUpdate" value="1" />
  <input type="submit" value="Upload Picture" /> (upload may take a few minutes)
  <br />
  <em>Acceptable Image Types: </em>GIF, JPEG, PNG<br />
  <em>Max File Size: </em>2 MB
  </p>
</form>
<hr />
<form method="post" action="myprofile.php">
<table style="margin: 1em 0 1em 0;">
<tr><td style="font-weight:bold;" colspan="2">Change Password</td></tr>
<tr><td>Current Password:</td><td><input type="password" name="oldpass" /></td></tr>
<tr><td>New Password:</td><td><input type="password" name="newpass" /></td></tr>
<tr><td>Verify New Password:</td><td><input type="password" name="verify" /></td></tr>
<tr><td colspan="2"><input type="submit" value="Change My Password" /></td></tr>
</table>
</form>
<hr />
<form method="post" action="myprofile.php">
<p style="font-weight: bold">Change E-mail Preferences</p>
<p>E-mail Address On File: <em><? echo $profile["studentEmail"]; ?></em><br />
<strong>Note: </strong>Your e-mail address cannot be changed here. Please email <a href="mailto:tntlab@odu.edu">tntlab@odu.edu</a> to change it.  Note that changes to this section may take up to 24 hours to take effect.</p>
<p>When someone sends me a direct message...<br />
<input type="radio" name="email_direct" value="1"<? if ($profile["emailDirect"] == 1) echo ' checked'; ?> /> E-mail me every time (up to once per half hour)<br />
<input type="radio" name="email_direct" value="2"<? if ($profile["emailDirect"] == 2) echo ' checked'; ?> /> E-mail me up to once per day<br />
<input type="radio" name="email_direct" value="0"<? if ($profile["emailDirect"] == 0) echo ' checked'; ?> /> Don't ever e-mail me</p>
<p>When someone posts a new message in my current classes...<br />
<input type="radio" name="email_classes" value="1"<? if ($profile["emailClasses"] == 1) echo ' checked'; ?> /> E-mail me every time (up to once per half hour)<br />
<input type="radio" name="email_classes" value="2"<? if ($profile["emailClasses"] == 2) echo ' checked'; ?> /> E-mail me up to once per day<br />
<input type="radio" name="email_classes" value="0"<? if ($profile["emailClasses"] == 0) echo ' checked'; ?> /> Don't ever e-mail me</p>
<p>When someone new matches my mentoring preferences...<br />
<input type="radio" name="email_mentoring" value="1"<? if ($profile["emailMentoring"] == 1) echo ' checked'; ?> /> E-mail me up to once per day<br />
<input type="radio" name="email_mentoring" value="0"<? if ($profile["emailMentoring"] == 0) echo ' checked'; ?> /> Don't ever e-mail me</p>
<input type="hidden" name="emailUpdate" value="1" />
<p><input type="submit" value="Update My E-mailing Preferences" /></p>
</form>

<form method="post" action="myprofile.php">
<table class="profile">
<tr><td class="header" colspan="2">Basic Information</td></tr>
<tr>
  <td class="items">Sex:</td>
  <td ><input type="radio" name="sex"<? if ($profile["sex"] == 1) echo " checked"; ?> value="1" />
Female <br />
<input type="radio" name="sex"<? if ($profile["sex"] == 2) echo " checked"; ?> value="2" />
Male<br />
<input type="radio" name="sex"<? if ($profile["sex"] == 3) echo " checked"; ?> value="3" />
Other<br />
<input type="radio" name="sex"<? if ($profile["sex"] == 9 or !isset($profile["sex"])) echo " checked"; ?> value="9" />
<em>Don't Include This On My Profile</em></td>
</tr>
<tr>
  <td class="items">Age:</td><td><input maxlength="50" type="text" name="age" value="<? echo htmlspecialchars($profile["age"]); ?>" /></td></tr>	
<tr>
  <td class="items">Relationship Status:</td>
  <td><span style="width: 300px;">
    <input type="radio" name="relationshipStatus"<? if ($profile["relationshipStatus"] == 1) echo " checked"; ?> value="1" />
Single <br />
<input type="radio" name="relationshipStatus"<? if ($profile["relationshipStatus"] == 2) echo " checked"; ?> value="2" /> 
In a relationship
<br />
<input type="radio" name="relationshipStatus"<? if ($profile["relationshipStatus"] == 3) echo " checked"; ?> value="3" /> 
Married
<br />
<input type="radio" name="relationshipStatus"<? if ($profile["relationshipStatus"] == 9 or !isset($profile["relationshipStatus"])) echo " checked"; ?> value="9" />
<em>Don't Include This On My Profile</em></span></td>
</tr>
<tr class="linebelow">
  <td class="items">Hometown:</td><td><input maxlength="50" type="text" name="hometown" value="<? echo htmlspecialchars($profile["hometown"]); ?>" /></td></tr>
<tr>
  <td class="header" colspan="2">School Details</td>
</tr>
<tr>
  <td class="items">School Status:</td>
  <td><input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 1) echo " checked"; ?> value="1" />
Freshman (1st year)<br />
<input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 2) echo " checked"; ?> value="2" />
Sophomore (2nd year)<br />
<input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 3) echo " checked"; ?> value="3" />
Junior (3rd year)<br />
<input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 4) echo " checked"; ?> value="4" />
Senior (4th year or more)<br />
<input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 5) echo " checked"; ?> value="5" />
Non-degree-seeking<br />
<input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 6) echo " checked"; ?> value="6" />
Returning/Non-traditional Student<br />
<input type="radio" name="schoolStatus"<? if ($profile["schoolStatus"] == 9 or !isset($profile["schoolStatus"])) echo " checked"; ?> value="9" />
<em>Don't Include This On My Profile</em></td>
</tr>
<tr>
  <td class="items">Employer:</td>
  <td><input maxlength="100" type="text" name="employer" value="<? echo htmlspecialchars($profile["employer"]); ?>" /></td>
</tr>
<tr>
  <td class="items">Work Status:</td>
  <td><input type="radio" name="workStatus"<? if ($profile["workStatus"] == 1) echo " checked"; ?> value="1" />
    Not currently employed
    <br />
  <input type="radio" name="workStatus"<? if ($profile["workStatus"] == 2) echo " checked"; ?> value="2" />
  1-10 hours per week<br />
  <input type="radio" name="workStatus"<? if ($profile["workStatus"] == 3) echo " checked"; ?> value="3" />
  11-20 hours per week<br />
  <input type="radio" name="workStatus"<? if ($profile["workStatus"] == 4) echo " checked"; ?> value="4" />
  21-30 hours per week<br />
  <input type="radio" name="workStatus"<? if ($profile["workStatus"] == 5) echo " checked"; ?> value="5" />
  31-40 hours per week<br />
  <input type="radio" name="workStatus"<? if ($profile["workStatus"] == 6) echo " checked"; ?> value="6" />
  41+ hours per week<br />
  <input type="radio" name="workStatus"<? if ($profile["workStatus"] == 9 or !isset($profile["workStatus"])) echo " checked"; ?> value="9" />
  <em>Don't Include This On My Profile</em><br /></td>
</tr>
<tr>
  <td class="items">Major:</td>
  <td><input maxlength="50" type="text" name="major" value="<? echo htmlspecialchars($profile["major"]); ?>" /></td>
</tr>
<tr>
  <td class="items">Favorite Class:</td>
  <td><input maxlength="50" type="text" name="favorite" value="<? echo htmlspecialchars($profile["favorite"]); ?>" /></td>
</tr>
<tr>
  <td class="items">Clubs/Organizations:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="clubs"><? echo htmlspecialchars($profile["clubs"]); ?></textarea></td>
</tr>
<tr>
  <td class="header" colspan="2">Personal Details</td>
</tr>
<tr>
  <td class="items">Activities:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="activities"><? echo htmlspecialchars($profile["activities"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Interests:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="interests"><? echo htmlspecialchars($profile["interests"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Favorite Music:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="faveMusic"><? echo htmlspecialchars($profile["faveMusic"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Favorite TV/Movies:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="faveTV"><? echo htmlspecialchars($profile["faveTV"]); ?></textarea></td>
</tr>
<tr>
  <td class="items">Favorite Books:</td>
  <td><textarea onkeyup="textLimit(this, 500);" name="faveBooks"><? echo htmlspecialchars($profile["faveBooks"]); ?></textarea></td>
</tr>
<tr>
  <td class="header" colspan="2">Big Five Profile</td>
</tr>
<tr>
  <td colspan="2"><input type="radio" name="bigFiveUse"<? if ($profile["bigFiveUse"] == "1" or !isset($profile["bigFiveUse"])) echo " checked"; ?> value="1" />
    Yes, I would like other students to see my personality profile. <br />
    <input type="radio" name="bigFiveUse"<? if ($profile["bigFiveUse"] == "0") echo " checked"; ?> value="0" />
    No, I would not like other students to see my personality profile. </td>
</tr>
<tr>
  <td class="header" colspan="2">Anonymity</td>
</tr>
<tr>
  <td colspan="2">
  <table class="anon">
<tr><td><input type="radio" name="anon" value="1" <?

	if ($profile["anon"] == 1) echo "checked";

?> /></td><td style="padding-right: 5px;"><strong>ANONYMOUS</strong></td>
<td>I want other students to see the nickname below in my profile and when I participate in discussions:<br />
  <em>Nickname:</em> <input type="text" name="nickname" value="<? echo htmlspecialchars($profile["anonNick"]); ?>" /></td>
</tr>
<tr><td><input type="radio" name="anon" value="0"  <?

	if (!isset($profile["anon"])) echo "checked";
	elseif ($profile["anon"] == 0) echo "checked";

?> /></td><td><strong>REAL NAME</strong></td>
<td>I want other students to see my real name in my profile and when I participate in discussions.</td>
</tr>
<tr><td colspan="3">Please note that this will be applied retroactively - every post you have ever made will be altered to reflect your anonymity decision.</td></tr>
</table>

  </td>
</tr>
</table>
<input type="hidden" name="updateProfile" value="1" />
<p><input type="submit" value="Update My Profile" /></p>
</form>

<?


printmenu_end();
endbody();
mysql_close();
?>
