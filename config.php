<?
	$link = mysql_connect("localhost", "username", "password")
		or die("Error: Could not connect to MySQL.");
	mysql_select_db("database", $link)
		or die("Error: Could not open database.");
?>
