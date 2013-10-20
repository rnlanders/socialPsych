<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) redirect('index.php');
if ($_SESSION["signupStage"] <> 0) redirect('index.php');
if ($_SESSION["instructorFlag"] == 1) {

	require('config.php');
	$sqldata = array("signupStage"=>4);
		
	mysql_update_array("student", $sqldata, "`studentKey` = " . $_SESSION["studentKey"], FALSE, FALSE);

	$_SESSION["signupStage"] = 4;

	mysql_close();
	redirect('signup4.php');
	die();
}

if (isset($_POST["research"])) {
	if ($_POST["research"] == 0) $participationConsent = 0;
	elseif ($_POST["research"] == 1) $participationConsent = 1;
	else $error = TRUE;

	if ($_POST["grade"] == 0) $gradeConsent = 0;
	elseif ($_POST["grade"] == 1) $gradeConsent = 1;
	else $error = TRUE;
	
	if ($participationConsent == 0 and $gradeConsent == 1) $error = TRUE;
	
	if (empty($_POST["signature"])) $error = TRUE;
	
	if (!$error) {
		require("config.php");
		$query = "UPDATE `student` SET signupStage=1,signature='" . safe($_POST["signature"]) . "',participationConsent=$participationConsent,gradeConsent=$gradeConsent WHERE `studentKey` = " .  $_SESSION["studentKey"];
		$result = mysql_query($query) or die(mysql_error());
		mysql_close();
		$_SESSION["signupStage"] = 1;
		redirect('signup1.php');
	}
}

printhead("p { margin-top: 1em; }");
startbody();

?>
<form method="post" action="consent.php">
<h2>Thank you for joining socialPsych, the ODU Department of Psychology's Online Social Network!</h2>
<p style="text-align: center">You are currently logged in.  If you wish to stop and return to this later, you must logout by <a href="index.php?logoff=1">clicking on this link</a> BEFORE working on the rest of this page.</p>
<? if ($error) echo '<p style="color:red">There was a problem with your consent selection, or you did not provide your electronic signature.  Please try again.  Please note: you must consent that your data be used for research purposes in order to consent that your grades be released.</p>'; ?>
<p><em><strong>INFORMED CONSENT</strong></em></p>
<object width="720" height="480"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=11501565&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=11501565&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="720" height="480"></embed></object>
<p><strong>INTRODUCTION</strong><br />
  The purposes of this form are to give  you information that may affect your decision whether to say YES or NO to  participation in research conducted on this social network, and to record the consent of those who say  YES.&nbsp; This project is titled &quot;socialPsych &ndash;  the Department of Psychology Student Social Network&quot; and takes place online.</p>
<p><strong>RESEARCHERS</strong><br />
  Assistant  Professor Richard Landers, Ph.D. of the Department of Psychology in the College  of Sciences, head of the Technology iN Training Laboratory (TNTLab) is the  Responsible Project Investigator. &nbsp;Rachel  Johnson is also a project investigator.</p>
<p><strong>DESCRIPTION OF THE RESEARCH STUDY</strong><br />
  socialPsych is part of a large research  project examining how online social networks can be most successfully  integrated into college classrooms and workplace training. To that end, your  actions on this social network will be recorded and used in analyses. You will  not be asked to behave in any manner inconsistent with what your instructors  require for your course participation.&nbsp;  If you do not consent for your data to be used in research, they will  still be available to your course instructors for grading purposes. Your decisions on this page will have no impact on any requirements specified by your instructors. Your instructors will not be informed of your decision to participate in this study. Your decisions on this page will have no impact on your course grades.</p>
<p>Depending upon instructor decisions, your specific activities on socialPsych  may include but are not limited to general discussion with other students, general  discussion with your instructor, posting of websites found for  extra credit assignments and related discussion, sharing of personal  experiences related to course content, completion of short quizzes not for  course credit, and establishing connection to mentors or mentees. You will also be asked to complete several psychological measures during the setup process, including Saucier&rsquo;s Big Five Mini Markers  personality inventory, two measures of organizational commitment, one measure  of subject-specific course anxiety, one measure of student engagement, and a  demographics questionnaire.</p>
<p>Thus, if you say YES or NO to participation in this study, you will not be asked to do anything additional to what your instructors already require. Saying YES only gives the researchers access to the content you supply while using the system.</p>
<p>Approximately 600-1200 college students  in up to 41 course sections will be participating in this study, depending upon  course enrollments. It will take approximately 30 minutes to complete the initial psychological surveys before you will gain access to socialPsych.</p>
<p><strong>EXCLUSIONARY CRITERIA</strong> <br />
You must be over the age of 18 to  participate in this study. If you are age 17 or younger, please DO NOT consent to participate in this study.</p>
<p><strong>RISKS AND BENEFITS</strong> <br />
RISKS:&nbsp;  If you decide to participate in this study, the data you supply will be used for research purposes. Your interactions in this system may be required by your instructor, in which case, no additional known risks are present by consenting for your data to be used for research purposes. If it is not required, participating may expose you to other students taking courses in the Psychology department, which the researchers cannot control. And, as with any research, there is  some possibility that you may be subject to risks that have not yet been  identified. &nbsp;</p>
<p>BENEFITS:&nbsp; There are no direct benefits from  participation in this study.</p>
<p><strong>COSTS AND PAYMENTS</strong> <br />
  If you decide to participate in this  study, you may be eligible for one of several small cash awards ($50-$100) for  best quality post, most interesting profile, and several others (detailed later). General eligibility for these awards in your classroom is determined by your instructor, while specific award decisions are made by the researchers. There is no guaranteed payment  for participation in this study. In order to be eligible for these awards, you must complete one additional survey online at the end of your course(s).</p>
<p>Your instructor may require  participation for course grades, but your decision to participate in the  research aspect of this study will have no bearing on your course grades; your  instructor will not be informed of your decision to allow your activities to be used in academic research until after final grades have been posted.</p>
<p><strong>NEW INFORMATION</strong> <br />
If the researchers find new information  during this study that would reasonably change your decision about participating,  they will give it to you at that time.</p>
<p><strong>CONFIDENTIALITY</strong> <br />
  The researchers will take reasonable  steps to keep private information, such as the questionnaires you complete,  status updates, discussion you supply, identifying information collected, and so on,  confidential.&nbsp; The  results of this study may be used in reports, presentations, and publications;  but the researcher will not identify you individually.&nbsp; Records may be subpoenaed by court order or  inspected by government bodies with oversight authority but will not be shared  outside the research team under any other circumstances.</p>
<p>Also consider that while collected data from your participation in this network will remain confidential, your actual participation in course discussions will be viewed by other students currently taking courses in the Psychology department, and the researchers cannot assure you remain anonymous to these students if you choose to disclose information that could be used to identify you. </p>
<p><strong>WITHDRAWAL PRIVILEGE</strong> <br />
It is OK for you to say NO.&nbsp; Even if you say YES now, you are free to say  NO later, and walk away or withdraw from the study &#8209;&#8209; at any time.&nbsp; Your decision will not affect your  relationship with Old Dominion University, or otherwise cause a loss of  benefits to which you might otherwise be entitled.</p>
<p><strong>COMPENSATION FOR ILLNESS AND INJURY</strong> <br />
If you say YES, then your consent in  this document does not waive any of your legal rights.&nbsp; However, in the unlikely event of any harm arising from this study, neither Old Dominion  University nor the researchers are able to give you any money, insurance  coverage, free medical care, or any other compensation for such injury.&nbsp; In the event that you suffer injury as a  result of participation in any research project, you may contact Dr. Richard  Landers at 757-683-4212, Dr. George Maihafer (the current IRB chair) at 757-683-4520,  or the Office of Research of Old Dominion University at 
757-683-3460, who will be glad to review the matter with you.&nbsp; <strong><u></u></strong></p>
<p><strong>VOLUNTARY CONSENT</strong> <br />
By saying YES on this form, you are saying  several things.&nbsp; You are saying that you  have read this form or have had it read to you, that you are satisfied that you  understand this form, the research study, and its risks and benefits.&nbsp; The researchers can answer any  questions you may have about the research by contacting them at <a href="mailto:tntlab@odu.edu">tntlab@odu.edu</a>.  If you have any questions later on, then the researchers should be able  to answer them. If at any time you feel pressured to  participate, or if you have any questions about your rights or this form, then  you should call Dr. George Maihafer, the current IRB chair, at 757-683-4520, or  the Old Dominion University Office of Research, at 757-683-3460.</p>
<hr /><p>socialPsych is part of a large research project examining how online social networks can be most successfully integrated into college classrooms and workplace training.  To that end, your actions on this social network will be recorded and used in analyses.  <strong>Please note that your identity will be held in strict confidence and never revealed to anyone outside the research team; all analyses presented publicly will include only aggregate data.</strong> If, despite this assurance, you still do not wish this data to be retained for the purposes of research, you can specify your decision here:
  <table>
  <tr><td style="vertical-align: top;">  <input type="radio" name="research" checked value="1" /></td>
  <td>YES - I understand that my data will be held confidentially, and I consent to its use for academic research.</td>
  </tr>
	<tr><td style="vertical-align: top;"><input type="radio" name="research" value="0" /></td>
	<td>NO - I understand that my data will be held confidentially, but I still <strong>do not</strong> wish it to be used for research purposes.	  </p></td>
	</tr></table>
<p>Please note that regardless of your choice above, your instructor may retain this data for grading, extra credit, or other purposes at their discretion; if you have questions about your instructor's intentions, please speak with your instructor individually.  </p>

<p>Many of our research questions center around this question: "Does having access to a course-specific online social network help students learn?"  To help answer this question, we would also like to collect your grades from your summer course instructors.  Again, <b>we want to assure you that your grades will be held confidentially; data connected to your identity will never be shared outside the research team.</b>  Again, if despite this assurance, you still do not wish your grades to be collected from your instructors for the purposes of research, you can specify your decision here:
<table><tr><td style="vertical-align: top;"><input type="radio" name="grade" checked value="1" /></td>
<td>YES - I understand that my grades will be held confidentially,  I consent to their collection from my instructors for academic research, and I wish to be considered for the eleven (11) $50 to $100 cash awards.</td>
<tr>
<tr><td style="vertical-align: top;"><input type="radio" name="grade" value="0" /></td>
<td>NO - I understand that my grades will be held confidentially, but I still <strong>do not</strong> wish them to be collected from my instructors, and I <strong>do not</strong> wish to be considered for the eleven (11) $50 to $100 cash awards.</td>
</tr></table>
<p>To verify your responses here, please type your name here as an electronic signature affirming the choices given above.<br />
  <strong>Electronic Signature:</strong> 
  <input type="text" style="width:200px;" name="signature" /> 
[<? echo $_SESSION["first"] . " " . $_SESSION["last"]; ?>]
</p>
<p>When you have made your decisions, please continue to next stage of the signup process by clicking on the button below.</p>
<p>
  <input type="submit" value="Continue to Profile Setup">
</p>
</form>
<?

endbody();

?>
