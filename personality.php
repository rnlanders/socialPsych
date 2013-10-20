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
 
		$query  = sprintf('select `conscientiousness`,`extraversion`,`agreeableness`,`emotionalStability`, `openness` from `student` where `studentKey` = %d', $id);
        $result = myquery($query);
		$profile = mysql_fetch_array($result);
		$c = $profile["conscientiousness"];
		$e = $profile["extraversion"];
		$a = $profile["agreeableness"];
		$o = $profile["openness"];
		$es = $profile["emotionalStability"];
		mysql_close();
	
		$image = @imagecreatetruecolor(200,200) or die("Profile creation error.");
		$white = imagecolorallocate($image, 255, 255, 255);
		$black = imagecolorallocate($image, 0, 0, 0);
		$red = imagecolorallocate($image, 255, 0, 0);

		$bar1 = $bar2 = $bar3 = $bar4 = $bar5 = imagecolorallocate($image, 255, 255, 255);

		/*
		$bar1 = imagecolorallocate($image, 255, 128, 0);
		$bar2 = imagecolorallocate($image, 0, 255, 128);
		$bar3 = imagecolorallocate($image, 255, 0, 128);
		$bar4 = imagecolorallocate($image, 125, 255, 0);
		$bar5 = imagecolorallocate($image, 0, 125, 255);
		*/
		imagefill($image, 0, 0, $white);
		//imageline($image, 0, 0, 0, 199, $black);
		imageline($image, 15, 199, 199, 199, $black);
		//imageline($image, 199, 199, 199, 0, $black);
		//imageline($image, 199, 0, 0, 0, $black);
		$font_file = './arial.ttf';
		/*
		imagefttext($image, 12, 90, 15, 199, $black, $font_file, 'Openness');
		imagefttext($image, 12, 90, 55, 198, $black, $font_file, 'Conscientiousness');
		imagefttext($image, 12, 90, 95, 197, $black, $font_file, 'Extraversion');
		imagefttext($image, 12, 90, 135, 196, $black, $font_file, 'Agreeableness');
		imagefttext($image, 12, 90, 175, 195, $black, $font_file, 'Emotional Stability');
		*/
		
		imagefttext($image, 12, 90, 15, 199, $black, $font_file, 'O');
		imagefttext($image, 12, 90, 55, 198, $black, $font_file, 'C');
		imagefttext($image, 12, 90, 95, 197, $black, $font_file, 'E');
		imagefttext($image, 12, 90, 135, 196, $black, $font_file, 'A');
		imagefttext($image, 12, 90, 175, 195, $black, $font_file, 'ES');
		
		if ($o != 0) {
			$height = (199 * $o) / 9;
			$height = 199 - $height;
			imagerectangle($image, 15, $height, 39, 199, $black);
			imagefill($image, 16, 198, $bar1);
		} else imagefttext($image, 12, 90, 30, 199, $red, $font_file, 'N/A');
		if ($c != 0) {
			$height = (199 * $c) / 9;
			$height = 199 - $height;
			imagerectangle($image, 55, $height, 79, 199, $black);
			imagefill($image, 56, 198, $bar2);
		} else imagefttext($image, 12, 90, 70, 199, $red, $font_file, 'N/A');
		if ($e != 0) {
			$height = (199 * $e) / 9;
			$height = 199 - $height;
			imagerectangle($image, 95, $height, 119, 199, $black);
			imagefill($image, 96, 198, $bar3);
		} else imagefttext($image, 12, 90, 110, 199, $red, $font_file, 'N/A');
		if ($a != 0) {
			$height = (199 * $a) / 9;
			$height = 199 - $height;
			imagerectangle($image, 135, $height, 159, 199, $black);
			imagefill($image, 136, 198, $bar4);
		} else imagefttext($image, 12, 90, 150, 199, $red, $font_file, 'N/A');
		if ($es != 0) {
			$height = (199 * $es) / 9;
			$height = 199 - $height;
			imagerectangle($image, 175, $height, 199, 199, $black);
			imagefill($image, 176, 198, $bar5);								
		} else imagefttext($image, 12, 90, 190, 199, $red, $font_file, 'N/A');
		//imagerectangle($image, 15, 0, 39, 199, $black);
		//imagerectangle($image, 55, 0, 79, 199, $black);
		//imagerectangle($image, 95, 0, 119, 199, $black);
		//imagerectangle($image, 135, 0, 159, 199, $black);
		//imagerectangle($image, 175, 0, 199, 199, $black);
		//$bar1 = 1;
	    header('Content-type: image/jpeg');	
		imagejpeg($image, null, 80);
		

    }
    catch (Exception $ex) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
?>
