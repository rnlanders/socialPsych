<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();
printmenu_start();

if (trim($_POST["searchterm"]) != "") {
	echo '<p><strong>Search Results</strong></p>';
	if ($_POST["type"] >= 1 and $_POST["type"] <= 4) {
		$query = "SELECT s.`studentKey`,s.`anon`,s.`first`,s.`last`,s.`anonNick` FROM ";
		if ($_POST["type"] == 1) {
			$query .= "(SELECT s1.`signupStage`, s1.`studentKey`, s1.`anon`, s1.`first`, s1.`last`, s1.`anonNick` FROM `student` AS s1 WHERE s1.`anonNick` LIKE '%" . safe($_POST["searchterm"]) . "%' AND s1.`anon` = 1";
			$query .= " UNION SELECT s2.`signupStage`, s2.`studentKey`, s2.`anon`, s2.`first`, s2.`last`, s2.`anonNick` FROM `student` AS s2 WHERE CONCAT(s2.`first`,s2.`last`) LIKE '%" . safe($_POST["searchterm"]) . "%' AND s2.`anon` = 0) AS s WHERE";
		} elseif ($_POST["type"] == 2) $query .= "`student` AS s LEFT JOIN `studentProfile` AS sp ON s.`studentKey`=sp.`studentKey` WHERE sp.`clubs` LIKE '%" . safe($_POST["searchterm"]) . "%' AND";
		elseif ($_POST["type"] == 3) $query .= "`student` AS s LEFT JOIN `studentProfile` AS sp ON s.`studentKey`=sp.`studentKey` WHERE sp.`activities` LIKE '%" . safe($_POST["searchterm"]) . "%' OR sp.`interests` LIKE '%" . safe($_POST["searchterm"]) . "%' AND";
		elseif ($_POST["type"] == 4) $query .= "`student` AS s LEFT JOIN `studentProfile` AS sp ON s.`studentKey`=sp.`studentKey` WHERE sp.`faveMusic` LIKE '%" . safe($_POST["searchterm"]) . "%' OR sp.`faveTV` LIKE '%" . safe($_POST["searchterm"]) . "%' OR sp.`faveBooks` LIKE '%" . safe($_POST["searchterm"]) . "%' AND";		
		$query .= " s.`studentKey` != " . $_SESSION["studentKey"] . " AND s.`signupStage`=9 LIMIT 25";
		$result = myquery($query);
		if (mysql_num_rows($result) == 0) echo '<p>No search results found.</p>';
		else {
			if (mysql_num_rows($result) == 25) echo '<p>Only the first 25 results are shown.  Try a more specific search.</p>';
			echo '<table class="status">';
			while ($row = mysql_fetch_array($result)) {
				echo '<tr><td class="statuspic"><img src="profilePic.php?thumb=1&id=' . $row["studentKey"] . 
					'"></td><td><strong><a href="';
				if ($row["studentKey"] == $_SESSION["studentKey"]) echo 'myprofile.php">';
				else echo 'profile.php?id=' . $row["studentKey"] . '">';
				if ($row["anon"] == 1) echo $row["anonNick"] . "</a>*";
				else echo $row["first"] . " " . $row["last"] . "</a>";
				echo "</strong></td></tr>";
			}
			echo '</table>';
		}
	} elseif ($_POST["type"] == 5) {
		$query = "SELECT csu.`classStatusKey`,csu.`classKey`,csu.`keyRepliedTo`,csu.`statusText`,c.`classNumber`,c.`sectionNumber`,c.`courseTitle` FROM `classStatusUpdates` AS csu LEFT JOIN `classes` AS c ON csu.`classKey` = c.`classKey` WHERE csu.`private` = 0 AND `statusText` LIKE '%" . safe($_POST["searchterm"]) .  "%' ORDER BY csu.`dateUpdated` DESC LIMIT 25;";
		$result = myquery($query);
		if (mysql_num_rows($result) == 0) echo '<p>No search results found.</p>';
		else {
			if (mysql_num_rows($result) == 25) echo '<p>Only the first 25 results are shown.  Try a more specific search.</p>';
			while ($row = mysql_fetch_array($result)) {
				echo '<p><strong><a href="course.php?id=' . $row["classKey"] . '#';
				if (is_null($row["keyRepliedTo"])) echo $row["classStatusKey"]; else echo $row["keyRepliedTo"];
				echo '">Discussion in ' . $row["classNumber"] . ' - ' . $row["courseTitle"] . ', Section ' . $row["sectionNumber"] . '</a></strong><br />';
				echo '<strong>Comment:</strong> ' . $row["statusText"] . '</p>';
			}
		}
	} else die();
}

echo '<form method="post" action="search.php">';

echo '<p><strong>Search</strong></p>';
echo '<p>Search Term: <input style="width: 300px" maxlength="100" type="text" name="searchterm" value="';
if (isset($_POST["searchterm"])) echo htmlspecialchars($_POST["searchterm"]);
echo '"></p>';
echo '<p>Students...<br />';
echo '<input type="radio" name="type"'; 
if (intval($_POST["type"]) > 0) { $case = intval($_POST["type"]); $logresult = write_log($_SESSION["username"] . " searched for type " . intval($_POST["type"]) . " with search: " . $_POST["searchterm"]); }
elseif ($_GET["s"] == "student") { $case = 1; $logresult = write_log($_SESSION["username"] . " opened the search page for students"); }
elseif ($_GET["s"] == "classes") { $case = 5; $logresult = write_log($_SESSION["username"] . " opened the search page for classes"); }
else { $case = 1; $logresult = write_log($_SESSION["username"] . " opened the search page without specifying a type"); }
if ($case == 1) echo " checked";
echo ' value="1">by Name<br />';
echo '<input type="radio" name="type"';
if ($case == 2) echo " checked";
echo ' value="2">by Clubs/Organizations<br />';
echo '<input type="radio" name="type"';
if ($case == 3) echo " checked";
echo ' value="3">by Activities/Interests<br />';
echo '<input type="radio" name="type"';
if ($case == 4) echo " checked";
echo ' value="4">by Favorite Music/TV/Movies/Books</p>';
echo '<p>Classes...<br />';
echo '<input type="radio" name="type"';
if ($case == 5) echo " checked";
echo ' value="5">by Discussion Content</p>';
echo '<p>For a list of classes and their instructors, <a href="courses.php">Browse Classes</a>.</p>';
echo '<p><input type="submit" value="Search"></p>';

echo '</form>';

printmenu_end();
endbody();
mysql_close();
?>
