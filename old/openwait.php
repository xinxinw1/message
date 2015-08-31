<?php /****** Simple Instant Message Devel ******/ ?>
<?php header("Cache-Control: no-cache"); ?>
<?php
if (isset($_REQUEST['doc'])){
  $doc = rawurlencode($_REQUEST['doc']);
  $file = "../docs/$doc";
  $fp = fopen($file, "a");
  if (flock($fp, LOCK_EX)){
    $time = time()+40;
    while (time() < $time){}
    flock($fp, LOCK_UN);
  } else {
    echo "fail";
    ob_flush();
    flush();
  }
  
  fclose($fp);
}
?>
