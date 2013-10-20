<?
require('functionlib.php');
require('config.php');

printhead("p { margin: 1em 0 1em 0; }");
startbody();

if (!isset($_POST["id"])) {
	echo '<p>The link you have followed is not valid.  Please check your e-mail and copy/paste the link you were given exactly.</p>';
	endbody();
	die();
}

$sqldata = array(
'sK'=>intval($_POST["sK"]),
'id'=>$_POST["id"],
'classes'=>intval($_POST["classes"]),
'commit1'=>intval($_POST["commit1"]),
'commit2'=>intval($_POST["commit2"]),
'commit3'=>intval($_POST["commit3"]),
'commit4'=>intval($_POST["commit4"]),
'commit5'=>intval($_POST["commit5"]),
'commit6'=>intval($_POST["commit6"]),
'commit7'=>intval($_POST["commit7"]),
'commit8'=>intval($_POST["commit8"]),
'commit9'=>intval($_POST["commit9"]),
'commit10'=>intval($_POST["commit10"]),
'commit11'=>intval($_POST["commit11"]),
'commit12'=>intval($_POST["commit12"]),
'commit13'=>intval($_POST["commit13"]),
'commit14'=>intval($_POST["commit14"]),
'commit15'=>intval($_POST["commit15"]),
'commit16'=>intval($_POST["commit16"]),
'commit17'=>intval($_POST["commit17"]),
'commit18'=>intval($_POST["commit18"]),
'commit19'=>intval($_POST["commit19"]),
'commit20'=>intval($_POST["commit20"]),
'commit21'=>intval($_POST["commit21"]),
'commit22'=>intval($_POST["commit22"]),
'commit23'=>intval($_POST["commit23"]),
'commit24'=>intval($_POST["commit24"]),
'commit25'=>intval($_POST["commit25"]),
'commit26'=>intval($_POST["commit26"]),
'commit27'=>intval($_POST["commit27"]),
'commit28'=>intval($_POST["commit28"]),
'commit29'=>intval($_POST["commit29"]),
'commit30'=>intval($_POST["commit30"]),
'commit31'=>intval($_POST["commit31"]),
'commit32'=>intval($_POST["commit32"]),
'commit33'=>intval($_POST["commit33"]),
'commit34'=>intval($_POST["commit34"]),
'commit35'=>intval($_POST["commit35"]),
'commit36'=>intval($_POST["commit36"]),
'commit37'=>intval($_POST["commit37"]),
'commit38'=>intval($_POST["commit38"]),
'commit39'=>intval($_POST["commit39"]),
'commit40'=>intval($_POST["commit40"]),
'commit41'=>intval($_POST["commit41"]),
'commit42'=>intval($_POST["commit42"]),
'commit43'=>intval($_POST["commit43"]),
'commit44'=>intval($_POST["commit44"]),
'commit45'=>intval($_POST["commit45"]),
'commit46'=>intval($_POST["commit46"]),
'commit47'=>intval($_POST["commit47"]),
'commit48'=>intval($_POST["commit48"]),
'commit49'=>intval($_POST["commit49"]),
'commit50'=>intval($_POST["commit50"]),
'commit51'=>intval($_POST["commit51"]),
'commit52'=>intval($_POST["commit52"]),
'commit53'=>intval($_POST["commit53"]),
'commit54'=>intval($_POST["commit54"]),
'commit55'=>intval($_POST["commit55"]),
'commit56'=>intval($_POST["commit56"]),
'commit57'=>intval($_POST["commit57"]),
'commit58'=>intval($_POST["commit58"]),
'commit59'=>intval($_POST["commit59"]),
'commit60'=>intval($_POST["commit60"]),
'doagainyes'=>intval($_POST["doagainyes"]),
'doagainno'=>intval($_POST["doagainno"]),
'social1'=>intval($_POST["social1"]),
'social2'=>intval($_POST["social2"]),
'social3'=>intval($_POST["social3"]),
'social4'=>intval($_POST["social4"]),
'social5'=>intval($_POST["social5"]),
'social6'=>intval($_POST["social6"]),
'social7'=>intval($_POST["social7"]),
'social8'=>intval($_POST["social8"]),
'social9'=>intval($_POST["social9"]),
'social10'=>intval($_POST["social10"]),
'social11'=>intval($_POST["social11"]),
'social12'=>intval($_POST["social12"]),
'social13'=>intval($_POST["social13"]),
'social14'=>intval($_POST["social14"]),
'social15'=>intval($_POST["social15"]),
'social16'=>intval($_POST["social16"]),
'social17'=>intval($_POST["social17"]),
'social18'=>intval($_POST["social18"]),
'social19'=>intval($_POST["social19"]),
'social20'=>intval($_POST["social20"]),
'social21'=>intval($_POST["social21"]),
'social22'=>intval($_POST["social22"]),
'social23'=>intval($_POST["social23"]),
'social24'=>intval($_POST["social24"]),
'social25'=>intval($_POST["social25"]),
'best'=>$_POST["best"],
'worst'=>$_POST["worst"],
'changeit'=>$_POST["changeit"],
'comments'=>$_POST["comments"],
'focus'=>intval($_POST["focus"]),
);

mysql_insert_array("posttest_main", $sqldata);

if (isset($_POST["course_9_cK"])) $courses = 9;
elseif (isset($_POST["course_8_cK"])) $courses = 8;
elseif (isset($_POST["course_7_cK"])) $courses = 7;
elseif (isset($_POST["course_6_cK"])) $courses = 6;
elseif (isset($_POST["course_5_cK"])) $courses = 5;
elseif (isset($_POST["course_4_cK"])) $courses = 4;
elseif (isset($_POST["course_3_cK"])) $courses = 3;
elseif (isset($_POST["course_2_cK"])) $courses = 2;
elseif (isset($_POST["course_1_cK"])) $courses = 1;
else die();

for ($i = 1; $i <= $courses; $i++) {
	$sqldata = array(
		'cK'=>intval($_POST["course_" . $i . "_cK"]),
		'sicK'=>$_POST["course_" . $i . "_sicK"],
		'sK'=>intval($_POST["course_" . $i . "_sK"]),
		'status'=>intval($_POST["course_" . $i . "_status"]),
		'retake'=>intval($_POST["course_" . $i . "_retake"]),
		'reaction1'=>intval($_POST["course_" . $i . "_reaction1"]),
		'reaction2'=>intval($_POST["course_" . $i . "_reaction2"]),
		'satisf1'=>intval($_POST["course_" . $i . "_satisf1"]),
		'satisf2'=>intval($_POST["course_" . $i . "_satisf2"]),
		'satisf3'=>intval($_POST["course_" . $i . "_satisf3"]),
		'satisf4'=>intval($_POST["course_" . $i . "_satisf4"]),
		'satisf5'=>intval($_POST["course_" . $i . "_satisf5"]),
		'satisf6'=>intval($_POST["course_" . $i . "_satisf6"]),
		'satisf7'=>intval($_POST["course_" . $i . "_satisf7"]),
		'satisf8'=>intval($_POST["course_" . $i . "_satisf8"]),
		'satisf9'=>intval($_POST["course_" . $i . "_satisf9"]),
		'satisf10'=>intval($_POST["course_" . $i . "_satisf10"]),
		'satisf11'=>intval($_POST["course_" . $i . "_satisf11"]),
		'satisf12'=>intval($_POST["course_" . $i . "_satisf12"]),
	);
	mysql_insert_array("posttest_classes", $sqldata);
}
$logresult = write_log(intval($_POST["sK"]) . " completed the post survey");

echo '<p style="font-weight:bold; font-size: 250%; line-height: 100%">End of Course socialPsych Survey</p>';
echo '<p>Thank you for submitting the end-of-course survey.  If you are participating in courses in the second half of the summer as well, you will need to fill this survey out again in August.</p>';
echo '<p>Cash awards for participation will be made at the end of the summer.  You are free to continue participating (completing certification exams, chatting with classmates, etc) to increase your chances until then.  If you are a winner, you\'ll be contacted through your ODU e-mail address.';

endbody();
?>
