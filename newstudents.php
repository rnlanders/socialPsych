
<?
ini_set("max_execution_time", 600);

require('functionlib.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();

if (!isset($_POST["password"]) or $_POST["password"] != "superduper9") {
?>
	<form enctype="multipart/form-data" method="post" action="newstudents.php">
	<input type="file" name="studentUpload" /><br />
    <input type="password" name="password" /><br />
    <input type="submit" />
    </form>
<?
} else {

	echo '2: Something was blank<br />3: Couldn\'t check student table<br />4: Couldn\'t insert into student table<br />5: Student already in SIC table<br />6: Couldn\'t insert into SIC<br />7: Couldn\'t mail<br />';

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

	if (!array_key_exists('studentUpload', $_FILES)) die();
    $studentDB = $_FILES['studentUpload'];
	if (!is_uploaded_file($studentDB['tmp_name'])) die();
	
	$headers = 	'From: ODU socialPsych <tntlab@odu.edu>' . "\r\n" .
				'Reply-To: ODU socialPsych <tntlab@odu.edu>' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
	
	$fileHandle = @fopen($studentDB['tmp_name'], 'r');
	if ($fileHandle) {
		require('config.php');
		while (!feof($fileHandle)) {
			set_time_limit(60);
			$error = FALSE;
			$mailresult = FALSE;
			
			$buffer = fgets($fileHandle);
			$line = explode(",", $buffer);
			$first = trim($line[0]);
			$last = trim($line[1]);
			$username = strtolower(trim($line[2]));
			$email = strtolower(trim($line[3]));
			$classKey = intval($line[4]);
			if (trim($first) == "First" and trim($last) == "Last") $error = 1;
			if (trim($first) == "" or trim($last) == "" or trim($username) == "" or trim($email) == "" or trim($classKey) == "") $error = 2;
			if (trim($first) == "" and trim($last) == "" and trim($username) == "" and trim($email) == "" and trim($classKey) == "") $error = 1;
			
			$query = "SELECT `studentKey` FROM `student` WHERE `studentEmail` = '" . safe($email) . "'";
			$result = mysql_query($query) or $error = 3;
			
			if (mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result);
				$alreadyStudent = TRUE; 
				$studentKey = $row["studentKey"];
			} else $alreadyStudent = FALSE;

			$pass = trim(generatePassword(9,5));

			if ($error == FALSE and $alreadyStudent == FALSE) {
				
				$query = "INSERT INTO `student` (`first`,`last`,`username`,`password`,`studentEmail`) VALUES ('" . safe($first) . "','" . safe($last) . "','" . $username . "',MD5('" . $pass . "'),'" . $email . "');";
				$result = mysql_query($query) or $error = 4;
				$studentKey = mysql_insert_id();
			} 
			
			if ($error == FALSE) {
				$query = "SELECT `studentKey` FROM `studentsInClasses` WHERE `studentKey` = '" . $studentKey . "' AND `classKey` = '" . $classKey . "';";
				$result = mysql_query($query);
				if (mysql_num_rows($result) > 0) $error = 5;
				else {
					$query = "INSERT INTO `studentsInClasses` (`studentKey`, `classKey`) VALUES (" . $studentKey . "," . $classKey . ");";
					$result = mysql_query($query) or $error = 6;
				}
			}

			$to = $first . " " . $last . " <" . $email . ">";
			$subject = "Student Account Created";
			if ($alreadystudent == TRUE)
				$message = "You have been enrolled in an additional class on socialPsych, the ODU Department of Psychology online social network!\r\n\r\n"
						. "You can access this new class by clicking on the link on the left side of the page after you log in.\r\n\r\n"
						. "You can access socialPsych by following this link: http://social.tntlab.org\r\n\r\n"
						. "If you have not done so already, you should change your password in the My Profile area so that it is easier to remember.  If you have any questions about socialPsych, please e-mail: tntlab@odu.edu";
			else 
				$message = "A new account has been created for you on socialPsych, the ODU Department of Psychology online social network!\r\n\r\n"
						. "This happened because at least one of the courses you are enrolled in during the Summer 2010 semester has granted you access.  socialPsych is an online social network similar to Facebook, except that it automatically connects you with all other Psychology students currently taking courses in the Psychology department.  Please note that you may be REQUIRED to use this by your course instructor; please check with your instructor for more information on his/her specific requirements.\r\n\r\n"
						. "You can access socialPsych by following this link: http://social.tntlab.org\r\n\r\n"
						. "When you log in for the first time, you will be asked to watch several videos and respond to several profile set-up questions.  Please watch and read these carefully.\r\n\r\n"
						. "Username: " . $username . "\r\nPassword: " . $pass . "\r\n\r\n"
						. "You should change your password in the My Profile area so that it is easier to remember.  If you have any questions about socialPsych, please e-mail: tntlab@odu.edu";
			if ($error == FALSE) {
				$mailresult = mail($to, $subject, $message, $headers);
				if ($mailresult == FALSE) $mailresult = mail($to, $subject, $message, $headers);
				if ($mailresult == FALSE) $error = 7;
			}

			if ($error > 1) echo 'Error ' . $error . ': ' . $first . ", " . $last . ", " . $username . ", " . $email . ': ' . $pass . '<br />';
			elseif ($error == FALSE) echo 'Success: ' . $first . ", " . $last . ", " . $username . ", " . $email . '<br />';
		}
		fclose($fileHandle);
		mysql_close();
	}	
}

endbody();
?>
