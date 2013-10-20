<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 9) { redirect('index.php'); die(); }

require('config.php');

printhead("p { margin: 0 0 1em 0; } li { margin: 0 0 1em 0; }");
startbody();
printmenu_start();

echo '<p><strong>Help!</strong></p>';
echo '<p>If you\'re just getting started with socialPsych, you should <a href="tutorial.php">watch the tutorial video first</a>.</p>';
echo '<p>Please see the list of frequently asked questions below if you have additional questions.  If your question is not answered in the list below, please e-mail <a href="mailto:tntlab@odu.edu">tntlab@odu.edu</a> and we\'ll get back to you within 48 hours (usually faster!).</p>';
echo '<ul>';
echo '<li><strong>How can I earn money from participating in socialPsych?</strong><br />There is a total of $1000.00 available in Summer 2010 for participation in socialPsych!  You can be awarded portions of this in a variety of ways.  The top two posts across all classes (judged by relevance to conversation and overall enthusiasm, up to one award per person) will be awarded $100 each.  The two most active uses of the mentor system will earn $100 each.  The three students with the most and highest course ceritifications will earn $100 each.  The person with the most posts across the semester will earn $100 (obvious attempts to game the system through multiple posts of low quality will disqualify you!).  Two $50 awards will be awarded at random for all people that log into socialPsych on at least 10 different days.  Finally, one $100 award will be awarded through a vote by you, the students - this will happen toward the end of the summer.';
echo '<li><strong>How do discussions work?</strong><br />There are three ways to talk directly with other students and your instructors:<br /><ol><li><strong>Status Updates:</strong> Any time you post a status update, any other student enrolled in any class you are enrolled in will see it on their home page.  So if you\'re enrolled in two classes, you\'ll see updates from students in both classes, but the students you see may not see each other.</li><li><strong>Course Discussions:</strong> Course discussions are specific to your classes.  If you want to complete course assignments or chat with people in your classes, this is where you should do it.</li><li><strong>Conversations:</strong> Conversations are private messages between two people only.  If you want to talk directly to another student, this is how you should do it.</li></ol></li>';
echo '<li><strong>What can I do to protect my privacy?</strong><br />You have complete control over what is shared with other students in socialPsych.  To maximize your privacy, click on <em>My Profile</em> in the list to the left and choose <em>Anonymous</em> at the bottom of the page.  You can then type in the nickname that you want to appear as in the textbox in that area.  This will change all references to you in socialPsych to your nickname.  PLEASE NOTE that if you change this setting, every reference to you in all of socialPsych will change simultaneously.  Also please remember that the research team and your instructor(s) will always know your true identity.</li>';
echo '<li><strong>How do I chat in my classrooms?</strong><br />Simply click on a link under <em>My Class Discussions</em> to open the discussion areas associated with your classes.  Type your comments on the right.</li>';
echo '<li><strong>I\'m getting too much e-mail from socialPsych!  How do I fix it?</strong><br />Open <em>My Profile</em> on the left and select &quot;Don\'t Ever E-mail Me&quot; for each of the options under <em>Change Email Preferences</em>.  Click <em>Update My E-mailing Preferences</em> to confirm your selections.</li>';
echo '<li><strong>What does certification mean?</strong><br />You can be certified in course material by taking tests up to once per four days in socialPsych.  You can access these tests through the <em>Certification Center</em> link to the left.  When you are certified in a course, your certification ribbon or star will appear next to your picture when chatting in that classroom.  For example, if you are certified in 201S, your rank badge will appear when you chat in the 201S classroom.  It will also appear when people are looking at you in the <em>Mentoring Center</em> (once you have set your mentoring preferences).  For more detail, see the <em>Certification Center</em>.</li>';
echo '<li><strong>How do I find other students so that I can help them or they can help me?</strong><br />If you\'re interested in being mentored or being a mentor, simply open the <em>Mentoring Center</em> link to the left and check which classes you are interseted in.  After your preferences are saved, you\'ll be e-mailed whenever a new mentoring match is found.  When you want to pursue a mentoring relationship, simply <em>Open a Conversation</em> with that user by opening their profile and clicking on the <em>Enter a Conversation with this Student</em> link.</li>';
echo '<li><strong>How do private/hidden posts in class discussions work?</strong><br />A comment thread is a primary comment and all of its replies.  Only comment threads as a whole can be made private to a particular class, and this must be set when the thread is created.  Threads cannot be made private later.  You cannot make individual comments on a thread private if the initial comment is public.  You cannot make individual comments on a thread public if the initial comment is private.  Private threads and comments will also not appear in search results.</li>';  
echo '</ul>';
printmenu_end();
endbody();
mysql_close();
?>
