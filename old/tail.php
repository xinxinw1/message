<?php
header("Content-Type: text/plain");

if (isset($_GET['doc'])){
  $doc = rawurlencode($_GET['doc']);
  $file = "../docs/$doc";
  $pos = isset($_GET['pos'])?intval($_GET['pos']):0;
  if (function_exists("inotify_init")){
    $fd = inotify_init();
    while (true){
      while (!file_exists($file))usleep(10000);
      $fp = fopen($file, "r");
      flock($fp, LOCK_SH);
      
      clearstatcache();
      if (filesize($file) > $pos){
        fseek($fp, $pos);
        $data = "";
        while (!feof($fp)){
          $data .= fread($fp, 8192);
        }
        $pos = ftell($fp);
        echo $data;
        ob_flush();
        flush();
      } else if (filesize($file) < $pos){
        echo "-----------------------------\n";
        ob_flush();
        flush();
        $pos = 0;
        break;
      }
      $watch = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
      flock($fp, LOCK_UN);
      fclose($fp);
      while (true){
        $events = inotify_read($fd);
        foreach ($events as $event => $details){
          if ($details["mask"] & IN_CLOSE_WRITE){
            $fp = fopen($file, "r");
            flock($fp, LOCK_SH);
            fseek($fp, $pos);
            $data = "";
            while (!feof($fp)){
              $data .= fread($fp, 8192);
            }
            $pos = ftell($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            echo $data;
            ob_flush();
            flush();
            continue;
          }
          if ($details["mask"] & IN_IGNORED){
            echo "-----------------------------\n";
            ob_flush();
            flush();
            $pos = 0;
            break 2;
          }
        }
      }
    }
  } else {
    while (true){
      while (!file_exists($file))usleep(10000);
      
      clearstatcache();
      if (filesize($file) > $pos){
        $fp = fopen($file, "r");
        flock($fp, LOCK_SH);
        fseek($fp, $pos);
        $data = "";
        while (!feof($fp)){
          $data .= fread($fp, 8192);
        }
        $pos = ftell($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        echo $data;
        ob_flush();
        flush();
      } else if (filesize($file) < $pos){
        echo "-----------------------------\n";
        ob_flush();
        flush();
        $pos = 0;
      }
      usleep(10000);
    }
  }
}
?>
