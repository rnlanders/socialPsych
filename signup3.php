<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 3) { redirect('index.php'); die(); }

require('config.php');

if (isset($_POST["check3"])) {
	if ($_POST["anon"] != "1" and $_POST["anon"] != "0") $error = "You must make a choice for your anonymity setting.";
	elseif ($_POST["anon"] == "1" and safe($_POST["nickname"]) == "") $error = "If you wish to be anonymous, you must set a nickname.";
	elseif ($_POST["anon"] == "1" and strlen(safe($_POST["nickname"])) < 4) $error = "If you wish to be anonymous, your nickname must be 4 characters or longer.";
	elseif ($_POST["anon"] == "1") {
		$query = 'SELECT `anonNick` FROM `student` WHERE LOWER(`anonNick`) = "' . strtolower(safe($_POST["nickname"])) . '"';
		$result = myquery($query); 
		if (mysql_num_rows($result) > 0) $error = "That nickname is already taken.  You must choose a unique nickname.";
	}
	
	if (!isset($error)) {
		if ($_POST["anon"] == "0") $anon = 0;
		elseif ($_POST["anon"] == "1") $anon = 1;
		if ($anon == 0) $anonNick = "null";
		else $anonNick = '"' . safe($_POST["nickname"]) . '"';
		
		$sqldata = array(
			"signupStage"=>4,
			"anon"=>$anon,
			"anonNick"=>$anonNick
		);
		
		mysql_update_array("student", $sqldata, "`studentKey` = " . $_SESSION["studentKey"], FALSE, FALSE);

		$_SESSION["anon"] = $anon;
		$_SESSION["anonNick"] = htmlspecialchars(safe($_POST["nickname"]));
		$_SESSION["signupStage"] = 4;
		mysql_close();
		redirect("signup4.php");
	
	} 	
}

mysql_close();

printhead();
startbody();

?>
<h2>socialPsych, the ODU Department of Psychology's Online Social Network</h2>
<p style="text-align: center">You are currently logged in.  Your selections on the previous pages have been saved.  If you wish to stop and return to the setup process later, you must logout by <a href="index.php?logoff=1">clicking on this link</a> BEFORE working on the rest of this page.</p>
<form method="post" action="signup3.php">
<? 

if (isset($error)) echo '<p style="color:red">' . $error . '</p>';

?>
<h4>Anonymity Setting</h4>
<p>In socialPsych, you have the option to appear anonymous to your classmates. Please note that your instructors will still know who you are. You can change this option later.</p>
<table class="anon">
<tr><td><input type="radio" name="anon" value="1" <?

	if ($_POST["anon"] == 1) echo "checked";

?> /></td><td><strong>ANONYMOUS</strong></td>
<td>I want other students to see the nickname below in my profile and when I participate in discussions:<br />
  <em>Nickname:</em> <input type="text" name="nickname" value="<? 
  if (isset($_POST["nickname"])) echo htmlspecialchars($_POST["nickname"]);
  else echo $_SESSION["first"]; ?>" /></td>
</tr>
<tr><td><input type="radio" name="anon" value="0"  <?

	if (!isset($_POST["anon"])) echo "checked";
	elseif ($_POST["anon"] == 0) echo "checked";

?> /></td><td><strong>REAL NAME</strong></td>
<td>I want other students to see my real name in my profile and when I participate in discussions.</td>
</tr>
</table>
<input type="hidden" name="check3" value="1" />
<p><input type="submit" value="Continue to the Next Page of Profile Setup" /></p>
</form>
<?

endbody();

?>
