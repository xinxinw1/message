/****** Simple Instant Message 1.1 ******/

function $(a){
  return document.getElementById(a);
}

function checkEnter(e){
  if (window.event)e = event;
  var key = (e.keyCode)?e.keyCode:-1;
  if (!e.shiftKey && key == 13){
    addText(doc, name + ": " + $("text").value);
    return false;
  }
}

$("text").onkeydown = checkEnter;

window.onload = function (){
  if (name == "" || doc == ""){
    while (name == "")name = prompt("Enter your name: ");
    while (doc == "")doc = prompt("Enter document name: ");
    window.location.assign("message.php?name=" + encodeURIComponent(name) + "&doc=" + encodeURIComponent(doc));
  }
  setText($("top"), "Name: " + name + " | Doc: " + doc);
  setText($("hist"), hist);
  checkNew(doc);
}

function checkNew(doc){
  if (typeof(EventSource) !== "undefined"){
    var src = new EventSource("checknew.php?doc=" + encodeURIComponent(doc));
    src.addEventListener("message", function (e){
      var text = decodeURIComponent(e.data);
      $("hist").appendChild(document.createTextNode(text));
      $("mid").scrollTop = $("mid").scrollHeight;
    });
    src.addEventListener("clear", function (e){
      $("hist").innerHTML = "";
    });
  } else {
    checkNewOnce(doc);
  }
}

function checkNewOnce(doc){
  var file = "checknewonce.php";
  var param = "doc=" + encodeURIComponent(doc) + "&pos=" + pos;
  var func = function (resp){
    var resps = resp.split("\n");
    var text = decodeURIComponent(resps[0]);
    pos = resps[1];
    if (pos != 0){
      $("hist").appendChild(document.createTextNode(text));
      $("mid").scrollTop = $("mid").scrollHeight;
    } else {
      $("hist").innerHTML = "";
    }
    
    checkNewOnce(doc);
  }
  var type = "get";
  
  ajaxRequest(file, param, func, type, "checkNewOnce");
}

function addText(doc, text){
  var file = "message.php";
  var param = "type=addtext&doc=" + encodeURIComponent(doc);
  param += "&text=" + encodeURIComponent(text);
  var func = function (resp){
    $("text").value = "";
  }
  var type = "post";
  
  ajaxRequest(file, param, func, type, "addText");
}

$("clear").onclick = function (){
  clearText(doc);
}

function clearText(doc){
  var file = "message.php";
  var param = "type=cleartext&doc=" + encodeURIComponent(doc);
  var func = function (resp){}
  var type = "get";
  
  ajaxRequest(file, param, func, type, "clearText");
}

function ajaxRequest(file, param, func, type, source){
  var ajax;
  if (window.XMLHttpRequest){
    ajax = new XMLHttpRequest();
  } else {
    ajax = new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  ajax.onreadystatechange = function (){
    if (ajax.readyState == 4){
      if (ajax.status == 200){
        func(ajax.responseText);
      } else if (ajax.status != 0){
        alert("An error has occurred! Status: " + ajax.status);
      }
    }
  }
  
  if (type == "get"){
    ajax.open("GET", file + "?" + param, true);
    ajax.send();
  } else if (type == "post"){
    ajax.open("POST", file, true);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(param);
  }
}

function setText(a, text){
  if (a.hasChildNodes()){
    a.firstChild.nodeValue = text;
  } else {
    a.appendChild(document.createTextNode(text));
  }
}
