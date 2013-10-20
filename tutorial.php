<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

$logresult = write_log($_SESSION["username"] . " viewed the tutorial video");

printhead("p { margin: 0 0 1em 0; }");
startbody();

printmenu_start();
?>
<p><strong>Tutorial Video</strong></p>
<object width="521" height="345"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=11506353&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=11506353&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="521" height="345"></embed></object>
<p><br />You can return to this video at any time through the <strong>Help!</strong> link on the left.</p>
<form method="post" action="home.php">
<input type="submit" value="Proceed to your home page and news feed" />
</form>
<?
printmenu_end();
endbody();
mysql_close();
?>
