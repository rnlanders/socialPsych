<?
require('functionlib.php');
session_start();

if (!isset($_SESSION["studentKey"])) { redirect('index.php'); die(); }
if ($_SESSION["signupStage"] <> 5) { redirect('index.php'); die(); }

require('config.php');

if (isset($_POST["check5a"])) {
	$errors = array();
    try {
        if (!array_key_exists('imageupload', $_FILES)) {
            throw new Exception('Image not found in uploaded data');
        }
 
        $image = $_FILES['imageupload'];
 
        assertValidUpload($image['error']);
 
        if (!is_uploaded_file($image['tmp_name'])) {
            throw new Exception('You did not select a file to upload.');
        }
 
        $info = getImageSize($image['tmp_name']);
 
        if (!$info) {
            throw new Exception('The file you uploaded is corrupt.');
        }
		
		if ($info['mime'] == 'image/jpeg') {
			$workingImage = imagecreatefromjpeg($image['tmp_name']);
		} elseif ($info['mime'] == 'image/gif') {
			$workingImage = imagecreatefromgif($image['tmp_name']);
		} elseif ($info['mime'] == 'image/png') {
			$workingImage = imagecreatefrompng($image['tmp_name']);
		} 
		$thumbImage = $workingImage;
		
		if (!is_resource($workingImage)) throw new Exception('File type not recognized.  Please make sure you upload a JPEG, GIF or PNG.');
		
		$new_w = 200;
		$new_h = 200;
		
		$old_x=imageSX($workingImage);
		$old_y=imageSY($workingImage);
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		$resampleSuccess = imagecopyresampled($dst_img,$workingImage,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		ob_start();
		imagejpeg($dst_img, null, 80);
		$imageblob = ob_get_contents();
		ob_clean();

		$new_w = 50;
		$new_h = 50;
		
		$old_x=imageSX($thumbImage);
		$old_y=imageSY($thumbImage);
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		$resampleSuccess = imagecopyresampled($dst_img,$thumbImage,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		ob_start();
		imagejpeg($dst_img, null, 80);
		$thumbimageblob = ob_get_contents();
		ob_clean();

    }
    catch (Exception $ex) {
        $errors[] = $ex->getMessage();
	}
	
    if (count($errors) == 0) {
       $query = "UPDATE `student` SET `profilePicture`='" . mysql_real_escape_string($imageblob) . 
	      "', `profilePictureThumb`='" . mysql_real_escape_string($thumbimageblob) . "' WHERE `studentKey` = " . $_SESSION["studentKey"] . ";";
		myquery($query);
    } else $error = $errors[0];


} elseif (isset($_POST["check5b"])) {
	$_SESSION["signupStage"] = 9;
	$_SESSION["lastLogin"] = $student["lastLogin"];
	$query = "UPDATE `student` SET `signupStage` = 9,`totalLogins`=1,`emailDirect`=1,`emailMentoring`=1,`emailClasses`=1,`lastLogin` = NOW() WHERE `studentKey` = " . $_SESSION["studentKey"] . ";";
	$result = myquery($query);
	mysql_close();
	redirect('tutorial.php'); 
}
printhead();
startbody();

?>
<h2>socialPsych, the ODU Department of Psychology's Online Social Network</h2>
<p style="text-align: center">You are currently logged in.  Your selections on the previous pages have been saved.  If you wish to stop and return to the setup process later, you must logout by <a href="index.php?logoff=1">clicking on this link</a> BEFORE working on the rest of this page.</p>
<h4>Profile Picture</h4>
<? if (isset($error)) echo '<p style="color:red">' . $error . '</p>'; ?>
<p>Please pick a picture to represent you in socialPsych. You can change your profile picture later.</p>
<? 
	echo '<img src="profilePic.php?id=' . $_SESSION["studentKey"] . '"> <img src="profilePic.php?thumb=1&id=' . $_SESSION["studentKey"] . '">';
?>
<form enctype="multipart/form-data" method="post" action="signup5.php">
  <p>
  <input type="file" name="imageupload" />
  <input type="hidden" name="check5a" value="1" />
  <input type="submit" value="Upload Picture" /> (upload may take a few minutes - please click once and wait)
  <br />
  <strong>Acceptable Image Types: </strong>GIF, JPEG, PNG<br />
  <strong>Max File Size: </strong>2 MB
  </p>
</form>
<form method="post" action="signup5.php">
<input type="hidden" name="check5b" value="1" />
<input type="submit" value="<?
if (isset($_POST["check5a"])) echo "Finish Profile Setup";
else echo "Skip Upload";
?>" />
</form>
<br />
<?

endbody();

?>
