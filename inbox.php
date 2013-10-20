<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; }");
startbody();

$logresult = write_log($_SESSION["username"] . " opened his/her inbox");

printmenu_start();
?>
<p><strong>Inbox</strong></p>
<p>To create a new conversation with another student, open that student's profile and click <strong>Send Message</strong>. You might consider using the <a href="search.php">Search</a> function or <a href="mentoring.php">Mentoring Center</a> to find them.</p>
<p><strong>Current Conversations:</strong></p>
<?

//$query = "SELECT DISTINCT pm.`fromStudentKey`, s.`first`, s.`last`, s.`anonNick`, s.`anon` FROM `privateMessages` AS pm LEFT JOIN `student` AS s ON (pm.`fromStudentKey` = s.`studentKey`) WHERE pm.`toStudentKey` = " . $_SESSION["studentKey"] . " ORDER BY pm.`lastMessage` DESC";
$query = "SELECT c.`conversationKey`,c.`fromStudentKey`,c.`toStudentKey`,UNIX_TIMESTAMP(c.`lastMessage`) AS `lastMessage`,UNIX_TIMESTAMP(c.`lastToRead`) AS `lastToRead`,UNIX_TIMESTAMP(c.`lastFromRead`) AS `lastFromRead`,s1.`first` AS fromFirst,s1.`last` AS fromLast,s1.`anon` AS fromAnon,s1.`anonNick` AS fromNick,s2.`first` AS toFirst,s2.`last` AS toLast,s2.`anon` AS toAnon,s2.`anonNick` AS toNick FROM `conversations` AS c LEFT JOIN `student` AS s1 ON c.`fromStudentKey` = s1.`studentKey` LEFT JOIN `student` AS s2 ON c.`toStudentKey` = s2.`studentKey` WHERE c.`fromStudentKey` = " . $_SESSION["studentKey"] . " OR c.`toStudentKey` = " . $_SESSION["studentKey"] . " ORDER BY c.`lastMessage` DESC";
$result = myquery($query);

if (mysql_num_rows($result) == 0) echo '<p>No current conversations!  Start one!</p>';

while ($row = mysql_fetch_array($result)) {
	echo '<p>';
	if ($row["toStudentKey"] == $_SESSION["studentKey"]) {
		if ($row["fromAnon"] == 0) $convName = $row["fromFirst"] . " " . $row["fromLast"];
		else $convName = $row["fromNick"];
		$lastRead = $row["lastToRead"];
	} else {
		if ($row["toAnon"] == 0) $convName = $row["toFirst"] . " " . $row["toLast"];
		else $convName = $row["toNick"];
		$lastRead = $row["lastFromRead"];
	}
	if (is_null($lastRead) OR $lastRead < $row["lastMessage"]) echo '<strong>';
	echo '<a href="message.php?id=' . $row["conversationKey"] . '">Conversation with ' . $convName . '</a>';
	if (is_null($lastRead) OR $lastRead < $row["lastMessage"]) echo '</strong> [New Message!]';
	echo '<br /><span class="date">Last message at ' . date("g:ia \o\\n l, F jS, Y", $row["lastMessage"]+7200) . '</span>';
	echo '</p>';
}

printmenu_end();
endbody();
mysql_close();
?>
