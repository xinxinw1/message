<?php
$doc = isset($_GET['doc'])?$_GET['doc']:"test";
$file = "../docs/$doc";
$fd = inotify_init();
$watch = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
while (true){
  $events = inotify_read($fd);
  foreach ($events as $event => $evdetails){
    echo "$event: <br>";
    ob_flush();
    flush();
    foreach ($evdetails as $detname => $detdata){
      echo "$detname: $detdata <br>";
      ob_flush();
      flush();
      if ($detname == "mask" && $detdata & IN_IGNORED){
        while (!file_exists($file))usleep(10000);       
        $watch = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
      }
    }
  }
}
?>
