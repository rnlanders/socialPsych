<?
require('functionlib.php');

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


if (trim($_POST["resetEmail"]) != "") {
	require_once('config.php');
	$pass = generatePassword(9,5);

	$query = "UPDATE `student` SET `tempPass`=MD5('" . $pass . "'),`tempExpire`=ADDDATE(NOW(), INTERVAL 1 DAY) WHERE `studentEmail`='" . safe(strtolower($_POST["resetEmail"])) . "' AND (`tempExpire` IS NULL OR UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`tempExpire`) > 0)";
	$result = myquery($query);
	
	if (mysql_affected_rows() == 0) {
		$error = "The e-mail address you entered was not found.  You may have entered your e-mail address incorrectly, you may have requested a password reset within the last 24 hours, or your account may not exist.  Please try again or contact tntlab@odu.edu for further assistance.";
		$logresult = write_log($_POST["resetEmail"] . " asked for a password reset but was not found");
	} else {
		$logresult = write_log($_POST["resetEmail"] . " asked for a password reset as " . md5($pass));
		$headers = 	'From: ODU socialPsych <tntlab@odu.edu>' . "\r\n" .
			'Reply-To: ODU socialPsych <tntlab@odu.edu>' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		$subject = 'Password Change Requested';
		$to = safe(strtolower($_POST["resetEmail"]));
		$message = "You recently requested a new password to access socialPsych, the ODU Department of Psychology online social network.\r\n\r\n" .
			"To create a new password, please follow this link (if you cannot click on the link, you should copy/paste it into your web browser):\r\nhttp://social.tntlab.org/newpass.php?p=" . md5($pass) . "\r\n\r\n" .
			"If you didn't request a new password or no longer wish to change your password, you can safely ignore this e-mail.  The link above will expire in 24 hours.";
		$mailresult = mail($to, $subject, $message, $headers);
		
		redirect('index.php?pu=1'); die();
	}
}

printhead("p { margin: 0 0 1em 0; }");
startbody();

echo '<h2>Welcome to  socialPsych, the ODU Department of Psychology\'s Online Social Network</h2>';
if (isset($error)) echo '<p style="color:red;">' . $error . '</p>';
?>
<p>You can request a password reset up to once per 24 hours.  To reset your password, enter your ODU e-mail address here<br />(for example: jdoe0001@odu.edu):</p>
<form method="post" action="reset.php">
<input type="text" name="resetEmail" maxlength="50" /> <input style="margin: 0 0 1em 0;" type="submit" value="Reset My Password" />
</form>
<p><a href="index.php">Click here to return to the socialPsych Login</a></p>
<?
endbody();
?>
