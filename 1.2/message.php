<?php
header("Cache-Control: no-cache");

if (isset($_REQUEST['type'])){
  if ($_REQUEST['type'] == "addtext"){
    $doc = rawurlencode(rawurldecode($_POST['doc']));
    $file = "../docs/$doc";
    $text = $_POST['text'] . "\n";
    $fp = fopen($file, "a");
    fwrite($fp, $text);
    die(fclose($fp));
  }
  if ($_REQUEST['type'] == "cleartext"){
    $doc = rawurlencode(rawurldecode($_GET['doc']));
    $file = "../docs/$doc";
    if (file_exists($file))unlink($file);
    $fp = fopen($file, "a");
    die(fclose($fp));
  }
} else {
  $name = isset($_GET['name'])?rawurlencode(rawurldecode($_GET['name'])):"";
  $doc = isset($_GET['doc'])?rawurlencode(rawurldecode($_GET['doc'])):"";
  $file = "../docs/$doc";
  if (!file_exists($file)){
    $fp = fopen($file, "a");
    fclose($fp);
  }
  $hist = rawurlencode(file_get_contents($file));
  $pos = filesize($file);
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Simple Instant Message 1.2</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" href="message.css">
  <script src="message.js" type="text/javascript" defer></script>
  <script type="text/javascript">
  var name = decodeURIComponent("<?php echo $name ?>");
  var doc = decodeURIComponent("<?php echo $doc ?>");
  var hist = decodeURIComponent("<?php echo $hist ?>");
  var pos = <?php echo $pos ?>;
  </script>
</head>

<body>
  <div id="top"></div>
  <div id="clear-button">
    <input type="button" value="Clear Text" id="clear">
  </div>
  <div id="mid"><pre id="hist"></pre></div>
  <div id="bot"><textarea name="text" id="text"></textarea></div>
</body>

</html>
