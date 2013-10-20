<?
require('functionlib.php');

printhead("p { margin: 1em 0 1em 0; }");
startbody();

if (!isset($_GET["id"])) {
	echo '<p>The link you have followed is not valid.  Please check your e-mail and copy/paste the link you were given exactly.</p>';
	endbody();
	die();
}

require('config.php');

$query = "SELECT sic.`studentInClassKey` as sicKey, sic.`studentKey`,sic.`classKey`,c.`courseTitle`,c.`classNumber`,c.`instructor` FROM `studentsInClasses` AS sic LEFT JOIN `classes` AS c ON sic.`classKey`=c.`classKey` WHERE MD5(CONCAT(sic.`studentKey`,'soCial')) = '" . safe($_GET["id"]) . "'";
$result = myquery($query);

if (mysql_num_rows($result) == 0) {
	echo '<p>The link you have followed is not valid.  Please check your e-mail and copy/paste the link you were given exactly.  You may need to follow this link later if your course has not ended yet.</p>';
	endbody();
	die();
}

$row = mysql_fetch_array($result);

echo '<p style="font-weight:bold; font-size: 250%; line-height: 100%">End of Course socialPsych Survey</p>';
echo '<p>Please remember, you must complete this survey to be eligible for any of the $1000 prize.  Your responses also help us understand if online social networks should be deployed at ODU in the future.</p>';

$commits = array(
"I would be very happy to spend the rest of my time in college at Old Dominion University.",
"I enjoy discussing Old Dominion with people outside it.",
"I really feel as if Old Dominion's problems are my own.",
"I think that I could easily become as attached to another school as I am to this one.",
"I do not feel like 'part of the family' at Old Dominion.",
"I do not feel 'emotionally attached' to Old Dominion.",
"Old Dominion has a great deal of personal meaning for me.",
"I do not feel a strong sense of belonging to Old Dominion.",
"I am not afraid of what might happen if I left Old Dominion without being accepted at another school first.",
"It would be very hard for me to leave Old Dominion right now, even if I wanted to.",
"Too much in my life would be disrupted if I decided I wanted to leave Old Dominion now.",
"It wouldn't be too costly for me to leave Old Dominion now.",
"Right now, staying with Old Dominion is a matter of necessity as much as desire.",
"I feel that I have too few options to consider leaving Old Dominion.",
"One of the few serious consequences of leaving Old Dominion would be the scarcity of available alternatives.",
"One of the major reasons I continue to go to school at Old Dominion is that leaving would require considerable personal sacrifice - another school may not match the overall benefits I have here.",
"I think that people these days move from school to school too often.",
"I do not believe that a person must always be loyal to his or her school.",
"Jumping from school to school does not seem at all unethical to me.",
"One of the major reasons I continue to go to Old Dominion is that I believe that loyalty is important and therefore feel a sense of moral obligation to remain.",
"If a better school asked me to transfer, I would not feel it was right to leave Old Dominion.",
"I was taught to believe in the value of remaining loyal to one organization.",
"Things were better in the days when people stayed with one school for their entire time in college.",
"I do not think that wanting to be the 'big man/woman on campus' (popular and highly involved in campus activities) is sensible anymore.",
"I enjoy course offerings from the Psychology department.",
"The instructors in the Psychology department are effective teachers.",
"I like the Psychology department.",
"The Psychology department is supportive of me.",
"I enjoy taking courses in Psychology.",
"I am just not good in Psychology.",
"I get good grades in Psychology.",
"I learn Psychology concepts quickly.",
"I have always believed Psychology was one of my best subjects.",
"In my Psychology courses, I understand even the most difficult concepts.",
"I enjoy reading about Psychology.",
"I look forward to Psychology courses.",
"I take courses in Psychology because I enjoy it.",
"I am interested in the things I learn in Psychology.",
"I often worry that it will be difficult for me in Psychology classes.",
"I get very tense when I have to do Psychology homework.",
"I get very nervous working on Psychology homework.",
"I feel helpless when working on Psychology homework.",
"I worry that I will get poor grades in Psychology courses.",
"When I get up in the morning, I feel like going to class.",
"When I'm doing my work as a student, I feel bursting with energy.",
"As far as my studies are concerned I always persevere, even when things are not going well.",
"I can continue studying for long periods of time.",
"I am very resiliant, mentally, as far as my studies are concerned.",
"I feel strong and vigorous when I'm studying or going to class.",
"To me, my studies are challenging.",
"My studies inspire me.",
"I am enthusiastic about my studies.",
"I am proud of my studies.",
"I find my studies full of meaning and purpose.",
"When I am studying, I forget everything else around me.",
"Time flies when I am studying.",
"I get carried away when I am studying.",
"It is difficult to detach myself from my studies.",
"I am immersed in my studies.",
"I feel happy when I am studying intensely."
);

$reactions = array(
	"I often found that I had been reading/watching/listening during class and then didn't know what I had just read/saw/heard.",
	"I often missed important points during class because I was thinking of other things.");
	
$satis = array(
	"How satisfied are you with your instructor's overall effectiveness?",
	"How satisfied are you with your instructor's responsiveness to student questions and problems?",
	"How satisfied are you with the quality of courses at ODU?",
	"How satisfied are you with the quality of courses in the Psychology department?",
	"How satisfied are you with the fairness of this class's exams?",
	"How satisfied are you with exam coverage and the importance of the material tested?",
	"How satisfied are you with the extent to which the course prepares you for future courses or future job responsibilities?",
	"How satisfied are you with the relevance of the course to your life?",
	"How satisfied are you with the classrooms/learning environment?",
	"How satisfied are you with the quality of course materials?",
	"How satisfied are you with the length of the course?",
	"How satisfied are you with the pace of the course material presented?"
);

$socials = array(
	"socialPsych is easy to use.", //1
	"It was hard for me to use socialPsych.",

	"I liked using socialPsych.", //3
	"I would use socialPsych even if I wasn't required to.",

	"socialPsych helps me in my classes.", //5
	"I learned more in my classes because of socialPsych.",
	"socialPsych helped me get better grades in my class(es).",
	"My courses were better because I had access to socialPsych.",

	"I interacted more with my classmates because of socialPsych.", //9
	"I got to know my classmates better because of socialPsych.",
	"I got to know my professor better because of socialPsych.",
	"I like using socialPsych to socialize with my classmates.",
	"I like using socialPsych to get extra help/information.",

	"Using e-mail notifications helped me stay involved on socialPsych.", //14
	"I received too many e-mails from socialPsych.",

	"Discussion on socialPsych should be required in all classes.", //16
	"socialPsych should be available to all students even if the instructor doesn't require it in their course.",
	"socialPsych should be available in the future.",

	"The mentoring functions on socialPsych helped locate/give help from/to other students.", //19
	"socialPsych is an excellent resource to find mentors/mentees.",

	"The certification exams were fun.", //21
	"I enjoyed taking certification exams.",
	"I felt good gaining ranks through certification exams.",
	"The certification exams were too difficult.",
	"The certification exams were boring." //25
);
$logresult = write_log($_row["studentKey"] . " opened the post survey");
echo '<form method="post" action="postsubmit.php">';
echo '<input type="hidden" name="sK" value="' . $row["studentKey"] . '">';
echo '<input type="hidden" name="id" value="' . $_GET["id"] . '">';
echo '<select name="classes">';
echo '<option value="99">-- Click to select --</option>';
for ($i = 0; $i < 9; $i++) echo '<option value="' . $i . '">' . $i . '</option>';
echo '<option value="9">9 or more</option></select>';
echo ' Not including Summer 2010, how many Psychology courses have you taken at ODU?  Your best guess is fine.</p>';

$j = 1;
do {
	$coursetitle = $row["classNumber"] . " - " . $row["courseTitle"];
	$shorttitle = $row["classNumber"];
	$prefix = "course_" . $j . "_";
	echo '<p style="line-height: 100%; font-size: 250%; font-weight: bold; margin-bottom: 0;">PSYC ' . $coursetitle . '</p>';
	echo '<p>Course Instructor: ' . $row["instructor"] . '</p>';
	echo '<p>If you completed this evalulation of this course earlier this summer (in June/July), please skip it this time.</p>';
	echo '<input type="hidden" name="' . $prefix . 'cK" value="' . $row["classKey"] . '">';
	echo '<input type="hidden" name="' . $prefix . 'sicK" value="' . $row["sicKey"] . '">';
	echo '<input type="hidden" name="' . $prefix . 'sK" value="' . $row["studentKey"] . '">';
	echo '<p><select name="' . $prefix . 'status">' .
	'<option value="99">-- Click to select --</option>' .
	'<option value="1">Withdrew early from course</option>' .
	'<option value="2">Took an Incomplete</option>' .
	'<option value="3">Completed course, earned/expecting an A</option>' .
	'<option value="4">Completed course, earned/expecting an A-</option>' .
	'<option value="5">Completed course, earned/expecting a B+</option>' .
	'<option value="6">Completed course, earned/expecting a B</option>' .
	'<option value="7">Completed course, earned/expecting a B-</option>' .
	'<option value="8">Completed course, earned/expecting a C+</option>' .
	'<option value="9">Completed course, earned/expecting a C</option>' .
	'<option value="10">Completed course, earned/expecting a C-</option>' .
	'<option value="11">Completed course, earned/expecting a D+</option>' .
	'<option value="12">Completed course, earned/expecting a D</option>' .
	'<option value="13">Completed course, earned/expecting an F/Fail</option>' .
	'<option value="14">Completed course, earned/expecting a Pass (P)</option>' .
	'<option value="15">Completed course, but was auditing</option>' .
	'<option value="16">Course is not over yet (skip this course eval for now)</option>' .
	'<option value="17">Already completed this form for this course</option>' .
	'<option value="18">Other</option></select>';	
	echo ' As of today, what is your status for this course?</p>';

	echo '<p><select name="' . $prefix . 'retake">' .
	'<option value="99">-- Click to select --</option>' .
	'<option value="1">No, I have not taken this course before.</option>' .
	'<option value="2">Yes, I have taken it once before.</option>' .
	'<option value="3">Yes, I have taken it twice before.</option>' .
	'<option value="4">Yes, I have taken it three times before.</option>' .
	'<option value="5">Yes, I have taken it four or more times before.</option></select>';
	echo ' Is this a retake of this course?</p>';

	echo '<table class="sevenpoint">';
foreach ($reactions as $key=>$reaction) {
	if ($key == 0) echo '<tr><td>&nbsp;</td><td class="top">Very Strongly Disagree</td><td class="top">Strongly Disagree</td><td class="top">Disagree</td><td class="top">Neither</td><td class="top">Agree</td><td class="top">Strongly Agree</td><td class="top">Very Strongly Agree</td><td class="top">Don\'t Know</td></tr>';
	$key++;
	echo '<tr><td class="first">' . "$key. $reaction</td>";
	for ($i = 1; $i <= 7; $i++) {
		echo '<td><input type="radio" name="' . $prefix . 'reaction' . $key . '" value="' . $i . '"></td>';
	}
	echo '<td class="righty"><input type="radio" name="' . $prefix . 'reaction' . $key . '" value="9"></td></tr>';
}
echo '</table>';
	echo '<table class="sevenpoint">';
foreach ($satis as $key=>$satisf) {
	if ($key % 6 == 0) echo '<tr><td>&nbsp;</td><td class="top">Very Unsatisfied</td><td class="top">Unsatisfied</td><td class="top">Neither</td><td class="top">Satisfied</td><td class="top">Very Satisfied</td><td class="top">Don\'t Know</td></tr>';
	$key++;
	echo '<tr><td class="first">' . "$key. $satisf</td>";
	for ($i = 1; $i <= 5; $i++) {
		echo '<td><input type="radio" name="' . $prefix . 'satisf' . $key . '" value="' . $i . '"></td>';
	}
	echo '<td class="righty"><input type="radio" name="' . $prefix . 'satisf' . $key . '" value="9"></td></tr>';
}
echo '</table>';

	
	$j++;
} while ($row = mysql_fetch_array($result));

echo '<p style="line-height: 100%; font-size: 250%; font-weight: bold; margin-bottom: 0;">Opinions about School</p>';
echo '<table class="sevenpoint">';
foreach ($commits as $key=>$commit) {
	if ($key % 10 == 0) echo '<tr><td>&nbsp;</td><td class="top">Very Strongly Disagree</td><td class="top">Strongly Disagree</td><td class="top">Disagree</td><td class="top">Neither</td><td class="top">Agree</td><td class="top">Strongly Agree</td><td class="top">Very Strongly Agree</td><td class="top">Don\'t Know</td></tr>';
	$key++;
	echo '<tr><td class="first">' . "$key. $commit</td>";
	for ($i = 1; $i <= 7; $i++) {
		echo '<td><input type="radio" name="commit' . $key . '"';
		if ($_POST["commit" . $key] == $i) echo " checked";
		echo ' value="' . $i . '"></td>';
	}
	echo '<td class="righty"><input type="radio" name="commit' . $key . '" value="9"';
	if ($_POST["commit" . $key] == 9) echo " checked";
	echo '></td></tr>';
}
echo '</table>';
echo '<p style="line-height: 100%; font-size: 250%; font-weight: bold; margin-bottom: 0;">Opinions about socialPsych</p>';
echo '<p><input type="checkbox" name="doagainyes" value="1"> Check this box if you think socialPsych should be available during the Fall 2010/Spring 2011 schoolyear</p>';
echo '<p><input type="checkbox" name="doagainno" value="1"> Check this box if you think socialPsych should <strong>NOT</strong> be available during the Fall 2010/Spring 2011 schoolyear</p>';

	echo '<table class="sevenpoint">';
foreach ($socials as $key=>$social) {
	if ($key % 10 == 0) echo '<tr><td>&nbsp;</td><td class="top">Very Unsatisfied</td><td class="top">Unsatisfied</td><td class="top">Neither</td><td class="top">Satisfied</td><td class="top">Very Satisfied</td><td class="top">Don\'t Know</td></tr>';
	$key++;
	echo '<tr><td class="first">' . "$key. $social</td>";
	for ($i = 1; $i <= 5; $i++) {
		echo '<td><input type="radio" name="social' . $key . '" value="' . $i . '"></td>';
	}
	echo '<td class="righty"><input type="radio" name="social' . $key . '" value="9"></td></tr>';
}
echo '</table>';

echo '<p>The only way socialPsych will be available in future semesters is with detailed information from you, the students, on how valuable (or not) it was to you.  Please write honestly, openly, and in detail.</p>';

echo '<p>What\'s the <strong>best</strong> thing about socialPsych?<br />';
echo '<textarea style="width: 75%; height: 10em;" name="best"></textarea></p>';

echo '<p>What\'s the <strong>worst</strong> thing about socialPsych?<br />';
echo '<textarea style="width: 75%; height: 10em;" name="worst"></textarea></p>';

echo '<p>What\'s would you change about socialPsych if you could?<br />';
echo '<textarea style="width: 75%; height: 10em;" name="changeit"></textarea></p>';

echo '<p>Do you have any other comments on socialPsych?<br />';
echo '<textarea style="width: 75%; height: 10em;" name="comments"></textarea></p>';

echo '<p><input type="checkbox" name="focus" value="1"> Check this box if you\'d be willing to participate in a focus group on socialPsych (as more people that participate in socialPsych, the better the chance that socialPsych will be available in future semesters)</p>';

echo '<center><input style="margin: 2em 0 1em 0;" type="submit" value="Submit my End of Course Survey"></center>';

echo '</form>';

endbody();
?>
