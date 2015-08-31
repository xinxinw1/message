<?php header("Cache-Control: no-cache"); ?>
<?php
if (isset($_REQUEST['type'])){
  function addData($doc, $data){
    $file = "../docs/$doc";
    while (!file_exists($file))usleep(10000);
    $fp = fopen($file, "a");
    flock($fp, LOCK_EX);
    fwrite($fp, $data);
    fflush($fp);
    flock($fp, LOCK_UN);
    return fclose($fp);
  }
  
  function getTime(){
    date_default_timezone_set("America/Toronto");
    return rawurlencode(date("h:i:s A"));
  }
  
  function sendMessage($doc, $name, $text){
    $time = getTime();
    $data = "message|$time|$name|$text\n";
    return addData($doc, $data);
  }
  
  function sendNotice($doc, $text){
    $time = getTime();
    $data = "notice|$time|$text\n";
    return addData($doc, $data);
  }
  
  function clearText($doc){
    $file = "../docs/$doc";
    if (file_exists($file)){
      $fp = fopen($file, "a");
      flock($fp, LOCK_EX);
      unlink($file);
      $fp = fopen($file, "a");
      flock($fp, LOCK_UN);
      return fclose($fp);
    } else {
      $fp = fopen($file, "a");
      return fclose($fp);
    }
  }
  
  if ($_REQUEST['type'] == "sendMessage"){
    $doc = rawurlencode($_POST['doc']);
    $name = rawurlencode($_POST['name']);
    $text = rawurlencode($_POST['text']);
    die(sendMessage($doc, $name, $text));
  }
  
  if ($_REQUEST['type'] == "sendNotice"){
    $doc = rawurlencode($_POST['doc']);
    $text = rawurlencode($_POST['text']);
    die(sendNotice($doc, $text));
  }
  
  if ($_REQUEST['type'] == "clearText"){
    $doc = rawurlencode($_POST['doc']);
    die(clearText($doc));
  }
  
  if ($_REQUEST['type'] == "checkConnection"){
    ignore_user_abort(true);
    set_time_limit(0);
    date_default_timezone_set("America/Toronto");
    $doc = rawurlencode($_GET['doc']);
    $name = rawurlencode($_GET['name']);
    sendNotice($doc, "$name is online.");
    while (true){
      echo date("h:i:s A") . "\n";
      ob_flush();
      flush();
      if (connection_status() != CONNECTION_NORMAL){
        break;
      }
      usleep(10000);
    }
    die(sendNotice($doc, "$name is offline."));
  }
}

$name = isset($_GET['name'])?rawurlencode($_GET['name']):"";
$doc = isset($_GET['doc'])?rawurlencode($_GET['doc']):"";
$data = ""; $pos = 0;
if (isset($_GET['name']) && isset($_GET['doc'])){
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
<?php $updated = "Fri.Jul.12.2013.13.37"; ?>
<!DOCTYPE html>
<html>

<head>
  <title>Simple Instant Message 1.6</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" href="message.css?<?php echo $updated ?>">
  <script type="text/javascript" src="message.js?<?php echo $updated ?>"></script>
  <script type="text/javascript">
  var name = decodeURIComponent("<?php echo $name ?>");
  var doc = decodeURIComponent("<?php echo $doc ?>");
  var data = decodeURIComponent("<?php echo $data ?>");
  var pos = <?php echo $pos ?>;
  </script>
</head>

<body>
  <div id="top"><a href="message.php" onclick="newName(); return false;">Name: <span id="name"></span></a> | <a href="message.php" onclick="newDoc(); return false;">Doc: <span id="doc"></span></a></div>
  <div id="clear-button">
    <input type="button" value="Clear Text" id="clear">
  </div>
  <div id="hist"></div>
  <div id="bot"><textarea name="text" id="text"></textarea></div>
  <script type="text/javascript">runOnload();</script>
</body>

</html>
