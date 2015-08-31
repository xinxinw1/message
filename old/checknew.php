<?php /****** Simple Instant Message Devel ******/ ?>
<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

if (isset($_GET['doc'])){
  $file = "../docs/" . $_GET['doc'];
  $pos = filesize($file);
  while (true){
    $fd = inotify_init();
    $watch = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
    while (true){
      $events = inotify_read($fd);
      foreach ($events as $event => $details){
        if ($details["mask"] & IN_CLOSE_WRITE){
          inotify_rm_watch($fd, $watch);
          fclose($fd);
          $fp = fopen($file, "r");
          fseek($fp, $pos);
          $text = "";
          while (!feof($fp)){
            $text .= fread($fp, 8192);
          }
          $pos = ftell($fp);
          fclose($fp);
          $text = str_replace("\n", "\ndata: ", $text);
          echo "data: $text\n";
          echo "\n\n";
          ob_flush();
          flush();
          break 2;
        }
        if ($details["mask"] & IN_IGNORED){
          echo "event: clear\n";
          echo "data: \n";
          echo "\n\n";
          ob_flush();
          flush();
          $watch = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
          $pos = 0;
          break 2;
        }
      }
    }
  }
}
?>