<?php /****** Simple Instant Message Devel ******/ ?>
<?php
if (isset($_REQUEST['doc'])){
  $doc = rawurlencode($_REQUEST['doc']);
  $file = "../docs/$doc";
  if (file_exists($file)){
    $fp = fopen($file, "a");
    flock($fp, LOCK_EX);
    unlink($file);
    $fp = fopen($file, "a");
    flock($fp, LOCK_UN);
    fclose($fp);
  } else {
    $fp = fopen($file, "a");
    fclose($fp);
  }
}
?>
