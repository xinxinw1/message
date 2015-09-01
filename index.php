<?php header("Cache-Control: no-cache"); ?>
<?php $version = "2.0"; ?>
<?php require "base64url.php"; ?>
<?php
if (isset($_REQUEST['type'])){
  function addData($doc, $data){
    $file = "docs/" . base64urlencode($doc);
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
    return date("l F j, Y|h:i:s A");
  }
  
  function sendMessage($doc, $name, $text){
    //$doc = rawurlencode($doc);
    $name = rawurlencode($name);
    $text = rawurlencode($text);
    $time = rawurlencode(getTime());
    $data = "message|$time|$name|$text\n";
    return addData($doc, $data);
  }
  
  function sendNotice($doc, $text){
    //$doc = rawurlencode($doc);
    $text = rawurlencode($text);
    $time = rawurlencode(getTime());
    $data = "notice|$time|$text\n";
    return addData($doc, $data);
  }
  
  function clearText($doc){
    //$doc = rawurlencode($doc);
    $file = "docs/" . base64urlencode($doc);
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
    $doc = $_POST['doc'];
    $name = $_POST['name'];
    $text = $_POST['text'];
    die(sendMessage($doc, $name, $text));
  }
  
  if ($_REQUEST['type'] == "sendNotice"){
    $doc = $_POST['doc'];
    $text = $_POST['text'];
    die(sendNotice($doc, $text));
  }
  
  if ($_REQUEST['type'] == "clearText"){
    $doc = $_POST['doc'];
    $name = $_POST['name'];
    clearText($doc);
    usleep(11000);
    die(sendNotice($doc, "$name cleared the text."));
  }
  
  if ($_REQUEST['type'] == "checkConnection"){
    ignore_user_abort(true);
    set_time_limit(0);
    $doc = $_GET['doc'];
    $name = $_GET['name'];
    sendNotice($doc, "$name is online.");
    while (true){
      echo ".";
      //echo getTime() . "\n";
      ob_flush();
      flush();
      if (connection_status() != CONNECTION_NORMAL){
        break;
      }
      usleep(100000); // usleep(1000) = sleep for 1 millisecond
    }
    die(sendNotice($doc, "$name is offline."));
  }
}

$name = isset($_GET['name'])?rawurlencode($_GET['name']):"";
$doc = isset($_GET['doc'])?rawurlencode($_GET['doc']):"";
$data = ""; $pos = 0;
if (isset($_GET['name']) && isset($_GET['doc'])){
  $file = "docs/" . base64urlencode(rawurldecode($doc));
  if (!is_dir("docs"))mkdir("docs");
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
<!DOCTYPE html>
<html>

<head>
  <title>Simple Instant Message <?php echo $version ?></title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="message.css">
  <script src="webtoolkit.base64.js"></script>
  <script src="message.js"></script>
  <script>
  var name = decodeURIComponent("<?php echo $name ?>");
  var doc = decodeURIComponent("<?php echo $doc ?>");
  var data = decodeURIComponent("<?php echo $data ?>");
  var pos = <?php echo $pos ?>;
  </script>
</head>

<body>
  <div id="top"><a href="index.php" onclick="newName(); return false;">Name: <span id="name"></span></a> | <a href="index.php" onclick="newDoc(); return false;">Doc: <span id="doc"></span></a></div>
  <div id="buttons">
    <input type="button" value="View Source" id="source">
    <input type="button" value="Clear Text" id="clear">
  </div>
  <div id="hist"></div>
  <div id="bot"><textarea name="text" id="text"></textarea></div>
  <script type="text/javascript">runOnload();</script>
</body>

</html>
