<?php header("Cache-Control: no-cache"); ?>
<?php
if (isset($_REQUEST['type'])){
  if ($_REQUEST['type'] == "sendMessage"){
    $doc = rawurlencode($_POST['doc']);
    $file = "../docs/$doc";
    $name = rawurlencode($_POST['name']);
    $text = rawurlencode($_POST['text']);
    date_default_timezone_set("America/Toronto");
    $time = rawurlencode(date("h:i:s A"));
    $data = "message|$time|$name|$text\n";
    while (!file_exists($file))usleep(10000);
    $fp = fopen($file, "a");
    flock($fp, LOCK_EX);
    fwrite($fp, $data);
    fflush($fp);
    flock($fp, LOCK_UN);
    die(fclose($fp));
  }
  if ($_REQUEST['type'] == "sendNotice"){
    $doc = rawurlencode($_POST['doc']);
    $file = "../docs/$doc";
    $text = rawurlencode($_POST['text']);
    date_default_timezone_set("America/Toronto");
    $time = rawurlencode(date("h:i:s A"));
    $data = "notice|$time|$text\n";
    while (!file_exists($file))usleep(10000);
    $fp = fopen($file, "a");
    flock($fp, LOCK_EX);
    fwrite($fp, $data);
    fflush($fp);
    flock($fp, LOCK_UN);
    die(fclose($fp));
  }
  if ($_REQUEST['type'] == "clearText"){
    $doc = rawurlencode($_GET['doc']);
    $file = "../docs/$doc";
    if (file_exists($file)){
      $fp = fopen($file, "a");
      flock($fp, LOCK_EX);
      unlink($file);
      $fp = fopen($file, "a");
      flock($fp, LOCK_UN);
      die(fclose($fp));
    } else {
      $fp = fopen($file, "a");
      die(fclose($fp));
    }
  }
} else {
  $name = isset($_GET['name'])?rawurlencode($_GET['name']):"";
  $doc = isset($_GET['doc'])?rawurlencode($_GET['doc']):"";
  $file = "../docs/$doc";
  if (!file_exists($file)){
    $fp = fopen($file, "a");
    fclose($fp);
  }
  $fp = fopen($file, "r");
  flock($fp, LOCK_SH);
  $data = rawurlencode(file_get_contents($file));
  $pos = filesize($file);
  flock($fp, LOCK_UN);
  fclose($fp);
}
?>
<?php $updated = "Wed.May.22.2013.22.52"; ?>
<!DOCTYPE html>
<html>

<head>
  <title>Simple Instant Message 1.3</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" href="message.css?<?php echo $updated ?>">
  <script src="message.js?<?php echo $updated ?>" type="text/javascript" defer></script>
  <script type="text/javascript">
  var name = decodeURIComponent("<?php echo $name ?>");
  var doc = decodeURIComponent("<?php echo $doc ?>");
  var data = decodeURIComponent("<?php echo $data ?>");
  var pos = <?php echo $pos ?>;
  </script>
</head>

<body>
  <div id="top"></div>
  <div id="clear-button">
    <input type="button" value="Clear Text" id="clear">
  </div>
  <div id="hist"></div>
  <div id="bot"><textarea name="text" id="text"></textarea></div>
</body>

</html>
