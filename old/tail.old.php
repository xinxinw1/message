<?php
function tail($file){
  // get the size of the file
  $pos = filesize($file);
  // Open an inotify instance
  $fd = inotify_init();
  // Watch $file for changes.
  $watch_descriptor = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
  // Loop forever (breaks are below)
  while (true){
    // Read events (inotify_read is blocking!)
    $events = inotify_read($fd);
    // Loop though the events which occured
    foreach ($events as $event=>$evdetails){
      // React on the event type
      switch (true){
        // File was modified
        case ($evdetails['mask'] & IN_MODIFY):
          // Stop watching $file for changes
          inotify_rm_watch($fd, $watch_descriptor);
          // Close the inotify instance
          fclose($fd);
          // open the file
          $fp = fopen($file, 'r');
          if (!$fp) return false;
          // seek to the last EOF position
          fseek($fp, $pos);
          // read until EOF
          while (!feof($fp)){
            $buf .= fread($fp, 8192);
          }
          // save the new EOF to $pos
          $pos = ftell($fp); // (remember: $pos is called by reference)
          // close the file pointer
          fclose($fp);
          // return the new data and leave the function
          return $buf;
          // be a nice guy and program good code ;-)
          break;
          
          // File was moved or deleted
        case ($evdetails['mask'] & IN_MOVE):
        case ($evdetails['mask'] & IN_MOVE_SELF):
        case ($evdetails['mask'] & IN_DELETE):
        case ($evdetails['mask'] & IN_DELETE_SELF):
          // Stop watching $file for changes
          inotify_rm_watch($fd, $watch_descriptor);
          // Close the inotify instance
          fclose($fd);
          // Return a failure
          return false;
          break;
      }
    }
  }
}

while (true){
  echo tail("../docs/" . $_GET["file"]);
}
?>
