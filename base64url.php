<?php
// http://stackoverflow.com/questions/11449577/why-is-base64-encode-adding-a-slash-in-the-result
function base64urlencode($s){
  return str_replace(array("+", "/"), array("-", "_"), base64_encode($s));
}

function base64urldecode($a){
  return base64_decode(str_replace(array("-", "_"), array("+", "/"), $a));
}
?>
