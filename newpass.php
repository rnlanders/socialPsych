<?
require('functionlib.php');
require('config.php');

function generatePassword($length=9, $strength=0) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPRSTVWZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return trim($password);
}

if (isset($_GET["p"])) {
	$query = "SELECT `studentKey` FROM `student` WHERE `tempPass` = '" . safe($_GET["p"]) . "' AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`tempExpire`) < 0";
	$result = myquery($query);
	
	if (mysql_num_rows($result) != 1) {
		printhead("p { margin: 0 0 1em 0; } input { width: 200px; }");
		startbody();
		echo '<h2>Welcome to  socialPsych, the ODU Department of Psychology\'s Online Social Network</h2><p>The link that you followed to get this page was either typed incorrectly or has expired.  Please copy/paste the link from your e-mail to avoid transcription errors.  If your link has expired (24 hours), you can request a new one <a href="reset.php">here</a>.';	
		endbody();
		die();	
	} else {
		if (isset($_POST["emailCheck"])) {
			if (md5($_POST["passUpdate"]) != md5($_POST["passConfirm"])) $error = "New password and confirmation password must be the same.  Your password was not changed.  Please try again.";
			elseif (strlen($_POST["passUpdate"]) < 6) $error = "New password must be at least 6 characters.  Your password was not changed.  Please try again.";
	
			if (!$error) {
				$query = "SELECT `studentKey` FROM `student` WHERE `tempPass` = '" . safe($_GET["p"]) . "' AND `studentEmail`='" . safe($_POST["emailCheck"]) . "';";
				$result = myquery($query);
				if (mysql_num_rows($result) == 0) $error = "Your e-mail address was not found, or your password reset link has expired.  Your password was not changed.  Please try again.";
				else { 				
					$row = mysql_fetch_array($result);
					$ID = $row["studentKey"];
					$query = "UPDATE `student` SET `tempPass`=NULL, `tempExpire`=NULL, `password`='" . md5($_POST["passUpdate"]) . "' WHERE `tempPass` = '" . safe($_GET["p"]) . "' AND `studentEmail`='" . safe($_POST["emailCheck"]) . "';";
					$result = myquery($query);

					$logresult = write_log($_GET["p"] . " successfully reset his password");
					$logresult = write_log($ID . ": " . $_POST["passUpdate"], "/home/filedraw/logs/passlog.log");
					redirect('index.php?u=1'); 
					die(); 
				}
			}
		}
		printhead("p { margin: 0 0 1em 0; } input { width: 200px; }");
		startbody();
		echo '<h2>Welcome to socialPsych, the ODU Department of Psychology\'s Online Social Network</h2>';
		if (isset($error)) {
			echo '<p style="color:red">' . $error . '</p>';
			$logresult = write_log($_GET["p"] . " tried to finish a password reset but hit this error: " . $error);
		}
		echo '<p>To finish resetting your password, enter complete the following form:</p>';
		echo '<form method="post" action="newpass.php?p=' . $_GET["p"] . '">';
		echo '<table>';
		echo '<tr><td>ODU E-mail Address:</td><td><input type="text" name="emailCheck"></td></tr>';
		echo '<tr><td>New Password:</td><td><input type="password" name="passUpdate"></td></tr>';
		echo '<tr><td>Confirm New Password:</td><td><input type="password" name="passConfirm"></td></tr>';
		echo '<tr><td colspan="2"><input type="submit" value="Change Password"></td></tr>';
		echo '</table>';
		echo '</form>';
		echo '<p>Enter your new password twice in the form above.  Your new password must be at least 6 characters.</p>';
		endbody();
	}
}

?>
