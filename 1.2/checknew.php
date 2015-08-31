<?php /****** Simple Instant Message 1.2 ******/ ?>
<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

if (isset($_GET['doc'])){
  $doc = rawurlencode(rawurldecode($_GET['doc']));
  $file = "../docs/$doc";
  $pos = filesize($file);
  if (function_exists("inotify_init")){
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
            $text = rawurlencode($text);
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
  } else {
    while (true){
      clearstatcache();
      if (file_exists($file)){
        if (filesize($file) > $pos){
          $fp = fopen($file, "r");
          fseek($fp, $pos);
          $text = "";
          while (!feof($fp)){
            $text .= fread($fp, 8192);
          }
          $pos = ftell($fp);
          fclose($fp);
          $text = rawurlencode($text);
          echo "data: $text\n";
          echo "\n\n";
          ob_flush();
          flush();
        } else if (filesize($file) == 0 && $pos != 0){
          echo "event: clear\n";
          echo "data: \n";
          echo "\n\n";
          ob_flush();
          flush();
          $pos = 0;
        }
      }
      usleep(10000);
    }
  }
}
?>