<?php /****** Simple Instant Message Devel ******/ ?>
<?php
header("Cache-Control: no-cache");

if (isset($_GET['doc'])){
  $doc = rawurlencode(rawurldecode($_GET['doc']));
  $file = "../docs/$doc";
  $pos = intval($_GET['pos']);
  if (function_exists("inotify_init")){
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
        die(rawurlencode($text) . "\n" . $pos);
      } else if (filesize($file) == 0 && $pos != 0){
        die("\n0");
      }
    }
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
          die(rawurlencode($text) . "\n" . $pos);
        }
        if ($details["mask"] & IN_IGNORED){
          die("\n0");
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
          die(rawurlencode($text) . "\n" . $pos);
        } else if (filesize($file) == 0 && $pos != 0){
          die("\n0");
        }
      }
      usleep(10000);
    }
  }
}
?>