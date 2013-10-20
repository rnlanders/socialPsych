<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 1) { redirect('index.php'); die(); }

$minimarkers = array("Bashful","Bold","Careless","Cold","Complex","Cooperative","Creative","Deep","Disorganized","Efficient","Energetic","Envious","Extraverted","Fretful","Harsh","Imaginative","Inefficient","Intellectual","Jealous","Kind","Moody","Organized","Philosophical","Practical","Quiet","Relaxed","Rude","Shy","Sloppy","Sympathetic","Systematic","Talkative","Temperamental","Touchy","Uncreative","Unenvious","Unintellectual","Unsympathetic","Warm","Withdrawn");

if (isset($_POST["check"])) {
	$error = array();
	foreach ($minimarkers as $marker) if (!isset($_POST[strtolower($marker)])) array_push($error, $marker);
	if (count($error) == 0) {  
		require("config.php");
		$query = "INSERT INTO `miniMarkers` (`studentKey`,`bashful`,`bold`,`careless`,`cold`,`complex`,`cooperative`,`creative`,`deep`,`disorganized`,`efficient`,`energetic`,`envious`,`extraverted`,`fretful`,`harsh`,`imaginative`,`inefficient`,`intellectual`,`jealous`,`kind`,`moody`,`organized`,`philosophical`,`practical`,`quiet`,`relaxed`,`rude`,`shy`,`sloppy`,`sympathetic`,`systematic`,`talkative`,`temperamental`,`touchy`,`uncreative`,`unenvious`,`unintellectual`,`unsympathetic`,`warm`,`withdrawn`) VALUES (";
		$query .= $_SESSION["studentKey"];
		foreach ($minimarkers as $marker) $query .= ",'" . safe($_POST[strtolower($marker)]) . "'";
		$query .= ");";
		$result = myquery($query);
		$reversals = array("shy","quiet","bashful","withdrawn","cold","unsympathetic","rude","harsh","disorganized","sloppy","inefficient","careless","moody","jealous","temperamental","envious","touchy","fretful","uncreative","unintellectual");
		foreach ($reversals as $marker) {
			if ($_POST[$marker] == 1) $_POST[$marker] = 9;
			elseif ($_POST[$marker] == 2) $_POST[$marker] = 8;
			elseif ($_POST[$marker] == 3) $_POST[$marker] = 7;
			elseif ($_POST[$marker] == 4) $_POST[$marker] = 6;
			elseif ($_POST[$marker] == 6) $_POST[$marker] = 4;
			elseif ($_POST[$marker] == 7) $_POST[$marker] = 3;
			elseif ($_POST[$marker] == 8) $_POST[$marker] = 2;
			elseif ($_POST[$marker] == 9) $_POST[$marker] = 1;
		}
		$extraversion = meanof8($_POST["talkative"], $_POST["extroverted"], $_POST["bold"], $_POST["energetic"], $_POST["shy"], $_POST["quiet"], $_POST["bashful"], $_POST["withdrawn"]); 
		$agreeableness = meanof8($_POST["sympathetic"], $_POST["warm"], $_POST["kind"], $_POST["cooperative"], $_POST["cold"], $_POST["unsympathetic"], $_POST["rude"], $_POST["harsh"]); 
		$conscientiousness = meanof8($_POST["organized"], $_POST["efficient"], $_POST["systematic"], $_POST["practical"], $_POST["disorganized"], $_POST["sloppy"], $_POST["inefficient"], $_POST["careless"]); 
		$emotionalStability = meanof8($_POST["unenvious"], $_POST["relaxed"], $_POST["moody"], $_POST["jealous"], $_POST["temperamental"], $_POST["envious"], $_POST["touchy"], $_POST["fretful"]);
		$openness = meanof8($_POST["creative"], $_POST["imaginative"], $_POST["philosophical"], $_POST["intellectual"], $_POST["complex"], $_POST["deep"], $_POST["uncreative"], $_POST["unintellectual"]);
		$query = "UPDATE `student` SET signupStage=2,openness=$openness,conscientiousness=$conscientiousness,extraversion=$extraversion,agreeableness=$agreeableness,emotionalStability=$emotionalStability WHERE `studentKey` = " .  $_SESSION["studentKey"];
		$result = myquery($query);
		mysql_close();
		$_SESSION["signupStage"] = 2;
		redirect("signup2.php");
	}
}

printhead();
startbody();

?>
<h2>socialPsych, the ODU Department of Psychology's Online Social Network</h2>
<p style="text-align: center">You are currently logged in.  Your selections on the consent form have been saved.  If you wish to stop and return to the setup process later, you must logout by <a href="index.php?logoff=1">clicking on this link</a> BEFORE working on the rest of this page.</p>
<p>To build your profile in socialPsych, you'll need to respond to several questions.  First up: personality.
<p>Please use this list of common human traits to describe yourself as accurately as possible.<br>
Describe yourself as you see yourself at the present time, not as you wish to be in the future.<br>
Describe yourself as you are generally or typically, as compared with other persons you know of the same sex and of roughly your same age. For each trait, please select a number indicating how accurately that trait describes you:</p>
<? if (isset($error)) {
		echo '<p style="color:red">You must respond to every question to continue.  You are currently missing responses to the following item(s): ';
		foreach ($error as $error) echo $error . " ";
} ?>
<form method="post" action="signup1.php">
<table class="minimarkers" width="100%">
<?
	function headrow() {
		echo '
<tr><td class="first">&nbsp;</td><td>1<br>
  Extremely<br>
  Inaccurate</td>
  <td>2<br>
    Very Inaccurate</td>
  <td>3<br>
    Moderately<br>
    Inaccurate</td>
  <td>4<br>
    Slightly<br>
    Inaccurate</td>
  <td>5<br>
    Neither Inaccurate<br>
    nor Accurate</td>
  <td>6<br>
    Slightly<br>
    Accurate</td>
  <td>7<br>
    Moderately<br>
    Accurate</td>
  <td>8<br>
    Very<br>
    Accurate</td>
  <td>9<br>
    Extremely<br>
    Accurate</td>
  <td>Don\'t Know</td>
</tr>';
}
	$j = 0;
	foreach ($minimarkers as $marker) {
		if ($j % 10 == 0) headrow();
		$j++;
		echo '<tr><td class="first">' . $marker . '</td>';
		for ($i = 1; $i <= 9; $i++){
			echo '<td><input type="radio"';
			if (isset($_POST[strtolower($marker)])) if ($_POST[strtolower($marker)] == $i) echo " checked";
			echo ' name="' . strtolower($marker) . '" value="' . $i . '"></td>';
		}
		echo '<td class="righty"><input type="radio"';
		if (isset($_POST[strtolower($marker)])) if ($_POST[strtolower($marker)] == 0) echo " checked";
		echo ' name="' . strtolower($marker) . '" value="0"></td></tr>';
	}
?>
</table>
<p style="text-align:center"><input type="submit" value="Continue to the Next Page of Your Profile Setup"></p>
<input type="hidden" name="check" value="1" />
</form>
<?

endbody();

?>
