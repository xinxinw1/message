<?php /****** Simple Instant Message Devel ******/ ?>
<?php header("Cache-Control: no-cache"); ?>
<?php
if (isset($_REQUEST['doc'])){
  $doc = rawurlencode($_REQUEST['doc']);
  $file = "../docs/$doc";
  $text = file_get_contents($file);
  if (file_exists($file))unlink($file);
  $fp = fopen($file, "a");
  fwrite($fp, $text);
  fclose($fp);
}
?>
