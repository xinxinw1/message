<?php /****** Simple Instant Message Devel ******/ ?>
<?php header("Cache-Control: no-cache"); ?>
<?php
if (isset($_REQUEST['doc']) && isset($_REQUEST['text'])){
  $doc = rawurlencode($_REQUEST['doc']);
  $file = "../docs/$doc";
  $text = rawurlencode($_REQUEST['text']) . "\n";
  $fp = fopen($file, "a");
  flock($fp, LOCK_EX);
  fwrite($fp, $text);
  fflush($fp);
  flock($fp, LOCK_UN);
  fclose($fp);
}
?>
