<?
// Filename of log to use when none is given to write_log
define("DEFAULT_LOG","/home/filedraw/logs/social_" . date("Ymd", time() + 7200) . ".log");
 
/**
  * write_log($message[, $logfile])
  *
  * Author(s): thanosb, ddonahue
  * Date: May 11, 2008
  * 
  * Writes the values of certain variables along with a message in a log file.
  *
  * Parameters:
  *  $message:   Message to be logged
  *  $logfile:   Path of log file to write to.  Optional.  Default is DEFAULT_LOG.
  *
  * Returns array:
  *  $result[status]:   True on success, false on failure
  *  $result[message]:  Error message
  */
 
function write_log($message, $logfile=DEFAULT_LOG) {
  // Get time of request
  if( ($time = $_SERVER['REQUEST_TIME']) == '') {
    $time = time();
  }
 
  // Get IP address
  if( ($remote_addr = $_SERVER['REMOTE_ADDR']) == '') {
    $remote_addr = "REMOTE_ADDR_UNKNOWN";
  }
 
  // Get requested script
  if( ($request_uri = $_SERVER['REQUEST_URI']) == '') {
    $request_uri = "REQUEST_URI_UNKNOWN";
  }
 
  // Format the date and time
  $date = date("Y-m-d H:i:s", $time + 7200);
 
  // Append to the log file
  if($fd = @fopen($logfile, "a")) {
    $result = fputcsv($fd, array($date, $remote_addr, $request_uri, $message));
    fclose($fd);
 
    if($result > 0)
      return array(status => true);  
    else
      return array(status => false, message => 'Unable to write to '.$logfile.'!');
  }
  else {
    return array(status => false, message => 'Unable to open log '.$logfile.'!');
  }
}
?>
