<?
require('config.php');
require('functionlib.php');

try {
        if (!isset($_GET['id'])) {
            throw new Exception('ID not specified');
        }
 
        $id = intval($_GET['id']);
 
        if ($id <= 0) {
            throw new Exception('Invalid ID specified');
        }
 
        if ($_GET["thumb"] == 0) {
			$query  = sprintf('select `profilePicture` from `student` where `studentKey` = %d', $id);
	 	} else {
			$query  = sprintf('select `profilePictureThumb` as `profilePicture` from `student` where `studentKey` = %d', $id);
		}
        $result = myquery($query);
 
        if (mysql_num_rows($result) == 0) $notfound = TRUE;
		else {
			$image = mysql_fetch_array($result);
			if (strlen($image["profilePicture"]) == 0) $notfound = TRUE;
		}
		
		if ($notfound == TRUE) {
		    header('Content-type: image/jpeg');
            //throw new Exception('Image with specified ID not found');
        	if ($_GET["thumb"] == 0) {
				readfile('images/200pxQues.jpg');
			} else {
				readfile('images/50pxQues.jpg');
			}
			die();
        }
    }
    catch (Exception $ex) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
 
    header('Content-type: image/jpeg'); // . $image['profilePictureMime']);
    //header('Content-length: ' . $image['profilePictureSize']);
 	
	mysql_close();
 
    echo $image['profilePicture'];
?>
