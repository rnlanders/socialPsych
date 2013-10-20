<?
	require_once('logging.php');

	function safe($str) {
		return mysql_real_escape_string(trim($str));
	}

	function safearray($array) {
		foreach ($array as $key=>$value) $returnarray[$key] = safe($value);
		return $returnarray;
	}

	function myquery($query) {
		$debug = FALSE;
		$result = mysql_query($query) or $error = TRUE;
		if (isset($error)) {
			if ($debug == TRUE) die(mysql_error() . '<br />' . $query);
			else { 
				$logresult = write_log(mysql_error(), "/home/filedraw/logs/sqlerrors.log");
				die('A database error has occurred.  Please contact <a href="mailto:tntlab@odu.edu">tntlab@odu.edu</a> and describe what specific actions you took that led to this error.');
			}
		} else return ($result);
	}
		
	function redirect($page) {
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/$page");
		exit;
	}

	function meanof8($val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8) {
		$numzeros = 0;
		$items = array($val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8);
		foreach ($items as $num) if ($num == 0) $numzeros++;
		if ($numzeros == 8) return(0);
		return(array_sum($items) / (8 - $numzeros));
	}
	
	function mysql_insert_array ($my_table, $my_array, $test = FALSE, $quoted = TRUE) {
		if ($quoted == TRUE) {
			$sep = '","'; 
			$endquote = '"';
		} else {
			$sep = ',';
			$endquote = '';
		}
		
	    $keys = array_keys($my_array);
	    $values = array_values($my_array);
	    if ($quoted == TRUE) $values = safearray($values);
		$sql = 'INSERT INTO ' . $my_table . ' (' . safe(implode(',', $keys)) . ') VALUES (' . $endquote . implode($sep, $values) . $endquote . ')';
	    if ($test == FALSE) return(myquery($sql));
		else die($sql);
		return(0);
	} 
	
	function linkify($str) {
		return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" target=\"blank\" rel=\"nofollow\">\\0</a>", $str);
	}
	
	function mysql_update_array ($my_table, $my_array, $whereclause, $test = FALSE, $quoted = TRUE) {
		if ($quoted == TRUE) $sep = '"'; else $sep = '';
	    $sql = 'UPDATE `' . $my_table . '` SET ';
		foreach ($my_array as $key=>$value) {
			if ($quoted == TRUE) $value = safe($value);
			$sql .= safe($key) . ' = ' . $sep . $value . $sep . ', ';
		}
		$sql = substr($sql, 0, -2) . " WHERE " . $whereclause;
	    if ($test == FALSE) return(myquery($sql));
		else die($sql);
		return(0);
	} 
	
	function mailer($to, $subject, $message, $type) {
		$headers = 'From: ODU socialPsych <tntlab@odu.edu>' . "\r\n" .
				    'Reply-To: ODU socialPsych <tntlab@odu.edu>' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
		
		$sqldata = array(
			"`to`"=>"'" . $to . "'",
			"`subject`"=>"'" . $subject . "'",
			"`message`"=>"'" . safe($message) . "'",
			"`headers`"=>"'" . $headers . "'",
			"`dateAdded`"=>"NOW()",
			"`type`"=>$type
		);
		
		mysql_insert_array("mailQueue", $sqldata, FALSE, FALSE);
	}


	function unixToText($unixDate) {
		if ($unixDate - time() > -30) return 'A few seconds ago';
		elseif ($unixDate - time() > -90) return 'About a minute ago';
		elseif ($unixDate - time() > -150) return 'About two minutes ago';
		elseif ($unixDate - time() > -300) return 'Less than five minutes ago';
		elseif ($unixDate - time() > -600) return 'Less than ten miunutes ago';
		elseif ($unixDate - time() > -1800) return 'Less than half an hour ago';
		elseif ($unixDate - time() > -3600) return 'Less than an hour ago';
		elseif ($unixDate - time() > -5400) return 'About an hour ago';
		elseif ($unixDate - time() > -86400) return 'About ' . round(-($unixDate - time()) / 3600) . ' hours ago';
		elseif (date("Ymd",$unixDate) >= date("Ymd",time()-86400)) return 'Yesterday';
		elseif (date("Ymd",$unixDate) >= date("Ymd",time()-(86400*2))) return 'Two days ago';
		elseif (date("Ymd",$unixDate) >= date("Ymd",time()-(86400*3))) return 'Three days ago';
		elseif (date("Ymd",$unixDate) >= date("Ymd",time()-(86400*4))) return 'Four days ago';
		elseif (date("Ymd",$unixDate) >= date("Ymd",time()-(86400*5))) return 'Five days ago';
		elseif (date("Ymd",$unixDate) >= date("Ymd",time()-(86400*6))) return 'Six days ago';
		elseif (date("YW",$unixDate) >= date("YW",time()-(86400*7*1.5))) return 'About a week ago';
		elseif (date("YW",$unixDate) >= date("YW",time()-floor((86400*7*2.5)))) return 'About two weeks ago';
		elseif (date("YW",$unixDate) >= date("YW",time()-floor((86400*7*3.5)))) return 'About three weeks ago';
		elseif (date("YW",$unixDate) >= date("YW",time()-floor((86400*7*5)))) return 'About a month ago';
		else return date('F jS, Y', $unixDate+7200);
	}
	
	function printhead($style = NULL) {
		echo '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Expires" content="Fri, Jun 1 1999 12:00:00 GMT">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>socialPsych</title>
<meta name="Description" content="socialPsych: Online Social Network of the ODU Department of Psychology">
<meta name="Keywords" content="ODU, psychology, social network">
';
if ($_SERVER['PHP_SELF'] == "/certexam.php") echo '<script language="javascript" type="text/javascript" src="countDown.js"></script>';
echo '<script language="javascript" type="text/javascript">
function textLimit(field, maxlen) {
if (field.value.length > maxlen) {
field.value = field.value.substring(0, maxlen);
} }
</script>
<link type="text/css" rel="stylesheet" href="style.css" title="style">';
		if ($style != NULL) {
			echo '<style type="text/css">';
			echo $style;
			echo '</style>';
		}
		echo '</head>';
	}
	
		function newstartbody($imagebit = 0) {
		echo '
  <body bgcolor="#F6F6F6">
    <div align="center">
      <table id="maintable" width="763" border="0" cellspacing="0" cellpadding="0">

        <tr>
          <td width="1" valign="top" bgcolor="#666666"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
          <td width="100%" valign="top" bgcolor="#FFFFFF">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td bgcolor="#666666"><img id="mainspacer" src="images/spacer.gif" width="757" height="1" border="0" alt=""></td>
              </tr>
              <tr>
                <td align="right">

                  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#002B5F">
                    <tr>
                      <td align="left" valign="middle">
                        <a href="http://www.odu.edu/">
                          <div><img src="" alt="Old Dominion University" width="235" height="27" border="0"></div>
                        </a>                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td bgcolor="#FFFFFF"><img src="images/spacer.gif" width="1" height="1" border="0" alt=""></td>

              </tr>
              <tr>
                <td bgcolor="#7F95AF"><img src="images/spacer.gif" width="1" height="1" border="0" alt=""></td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="middle">
                  <a href="http://sci.odu.edu/">

                    <div><img border="0" src="" alt=""></div>
                  </a>
                </td>
                <td class="printfriendly" align="right" valign="middle"></td>
              </tr>
            </table>
            <table width="100%" valign="top" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" bgcolor="#002B5F"><img src="images/spacer.gif" width="1" height="5" alt=""><br>

    </td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#FFFFFF"><img src="images/spacer.gif" width="1" height="1" alt=""><br>
    </td>
  </tr>
  <tr>
    <td width="100%" style="background-image: url(\'\')">
      <table valign="top" border="0" cellpadding="0" cellspacing="0">

        <tr>

		<td>
            <a href="http://sci.odu.edu/psychology">
									<img src="" width="207" height="29" alt="" border="0"><br>
            </a>
          </td>
	
</tr>
      </table>

    </td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#FFFFFF"><img src="images/spacer.gif" width="1" height="1" alt=""><br>
    </td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#002B5F"><img src="images/spacer.gif" width="1" height="5" alt=""><br>
    </td>

  </tr>
</table>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left"><img src="images/spacer.gif" width="1" height="10" alt=""></td>
              </tr>
            </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="10" align="left"><img src="images/spacer.gif" width="10" height="1" alt=""></td>
                            <td align="left" valign="top">
                                          ';
										   if ($imagebit == 1) echo '<p><center><img alt="" src=""></center></p>';		
	}

	function startbody($imagebit = 0, $onload = FALSE) {
		echo '
  <body ';
  		if ($onload != FALSE) echo $onload . " ";
  		echo 'bgcolor="#F6F6F6">
    <div align="center">
      <table id="maintable" width="763" border="0" cellspacing="0" cellpadding="0">

        <tr>
          <td width="1" valign="top" bgcolor="#666666"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
          <td width="100%" valign="top" bgcolor="#FFFFFF">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td bgcolor="#666666"><img id="mainspacer" src="images/spacer.gif" width="757" height="1" border="0" alt=""></td>
              </tr>
              <tr>
                <td align="right">

                  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#002B5F">
                    <tr>
                      <td align="left" valign="middle">
                        <a href="http://www.odu.edu/">
                          <div><img src="images/gfx-logo-odu-crown.gif" alt="Old Dominion University" width="235" height="27" border="0"></div>
                        </a>                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td bgcolor="#FFFFFF"><img src="images/spacer.gif" width="1" height="1" border="0" alt=""></td>

              </tr>
              <tr>
                <td bgcolor="#7F95AF"><img src="images/spacer.gif" width="1" height="1" border="0" alt=""></td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="middle">
                  <a href="http://sci.odu.edu/">

                    <div><img border="0" src="images/logo-cos.gif" alt="College of Sciences"></div>
                  </a>
                </td>
                <td class="printfriendly" align="right" valign="middle"></td>
              </tr>
            </table>
            <table width="100%" valign="top" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" bgcolor="#002B5F"><img src="images/spacer.gif" width="1" height="5" alt=""><br>

    </td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#FFFFFF"><img src="images/spacer.gif" width="1" height="1" alt=""><br>
    </td>
  </tr>
  <tr>
    <td width="100%" style="background-image: url(\'images/hmenu_bg_dept1.gif\')">
      <table valign="top" border="0" cellpadding="0" cellspacing="0">

        <tr>

		<td>
            <a href="http://sci.odu.edu/psychology">
									<img src="images/hmenu_department_of_psychology.png" width="207" height="29" alt="Department of Psychology" border="0"><br>
            </a>
          </td>
	
</tr>
      </table>

    </td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#FFFFFF"><img src="images/spacer.gif" width="1" height="1" alt=""><br>
    </td>
  </tr>
  <tr>
    <td width="100%" bgcolor="#002B5F"><img src="images/spacer.gif" width="1" height="5" alt=""><br>
    </td>

  </tr>
</table>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left"><img src="images/spacer.gif" width="1" height="10" alt=""></td>
              </tr>
            </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="10" align="left"><img src="images/spacer.gif" width="10" height="1" alt=""></td>
                            <td align="left" valign="top">
                                          ';
										   if ($imagebit == 1) echo '<p><center><img alt="" src="images/home-header.jpg"></center></p>';		
	}
	
	function endbody() {
		echo '</td>
                          <td width="10" align="left"><img src="images/spacer.gif" width="10" height="1" alt=""></td>
						  </tr>
						  
                        </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">

              <tr>
                <td bgcolor="#000000"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
              </tr>
              <tr>
                <td align="center" class="footer" bgcolor="#002B5F"><img src="images/spacer.gif" width="1" height="7" alt=""><br>
					 &copy; 2006, 2010 <a class="footerlink" href="http://odu.edu">Old Dominion University</a>, Norfolk, VA 23529
                  <br>
                  <img src="images/spacer.gif" width="1" height="7" alt=""><br>
                </td>
              </tr>
              <tr>
                <td bgcolor="#666666"><img src="images/spacer.gif" width="1" height="1" alt=""></td>

              </tr>
            </table>
          </td>
          <td width="1" valign="top" bgcolor="#666666"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
          <td width="4" valign="top" style="background-image:url(images/shadow-r.gif)"><img src="images/shadow-tr.gif" width="4" height="4" alt=""></td>
        </tr>
        <tr>
          <td colspan="4">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">

              <tr>
                <td width="50%" valign="top" align="left" style="background-image:url(images/shadow-b.gif)"><img src="images/shadow-bl.gif" width="4" height="5" alt=""></td>
                <td width="50%" valign="top" align="right" style="background-image:url(images/shadow-b.gif)"><img src="images/shadow-br.gif" width="4" height="5" alt=""></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>

    <div align="center">
      <table width="763" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center"></td>
        </tr>
      </table>
    </div>
  </body>
</html> ';
	}
	
function assertValidUpload($code)
    {
        if ($code == UPLOAD_ERR_OK) {
            return;
        }
 
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $msg = 'The image you uploaded was too large.  Please try again with a smaller file.';
                break;
 
            case UPLOAD_ERR_PARTIAL:
                $msg = 'Your upload halted early due to an error.  Please try again.';
                break;
 
            case UPLOAD_ERR_NO_FILE:
                $msg = 'You did not upload an image.  Please select a valid image file and try again.';
                break;
 
            case UPLOAD_ERR_NO_TMP_DIR:
                $msg = 'Server error.  Please try again.';
                break;
 
            case UPLOAD_ERR_CANT_WRITE:
                $msg = 'Server error.  Please try again.';
                break;
 
            case UPLOAD_ERR_EXTENSION:
                $msg = 'Server error.  Please try again.';
                break;
 
            default:
                $msg = 'Server error.  Please try again.';
        }
 
        throw new Exception($msg);
    }
	
	function printmenu_start() {
		require('leftmenu.php');
	}
	function printmenu_end() {
		echo'</td></tr></table>';
	}

?>
