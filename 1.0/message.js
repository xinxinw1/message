/****** Simple Instant Message 1.0 ******/

function $(a){
  return document.getElementById(a);
}

function checkEnter(e){
  if (window.event)e = event;
  var key = (e.keyCode)?e.keyCode:-1;
  if (!e.shiftKey && key == 13){
    $("send").click();
    return false;
  }
}

$("text").onkeydown = checkEnter;

window.onload = function (){
  if (name == "" || doc == ""){
    while (name == "")name = prompt("Enter your name: ");
    while (doc == "")doc = prompt("Enter document name: ");
    window.location.assign("message.php?name=" + name + "&doc=" + doc);
  }
  checkNew(doc);
}

function checkNew(doc){
  if (typeof(EventSource) !== "undefined"){
    var src = new EventSource("checknew.php?doc=" + doc);
    src.addEventListener("message", function (e){
      $("hist").appendChild(document.createTextNode(e.data));
      $("mid").scrollTop = $("mid").scrollHeight;
    });
    src.addEventListener("clear", function (e){
      $("hist").innerHTML = "";
    });
  }
}

$("send").onclick = function (){
  addText(doc, name + ": " + $("text").value);
  $("text").value = "";
}

function addText(doc, text){
  var ajax;
  if (window.XMLHttpRequest){
    ajax = new XMLHttpRequest();
  } else {
    ajax = new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  ajax.onreadystatechange = function (){
    if (ajax.readyState == 4){
      if (ajax.status == 200){
        //alert(ajax.responseText);
      } else {
        alert("An error has occurred! Status: " + ajax.status);
      }
    }
  }
  
  ajax.open("POST", "addtext.php", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("doc=" + doc + "&text=" + text);
}

$("clear").onclick = function (){
  clearText(doc);
}

function clearText(doc){
  var ajax;
  if (window.XMLHttpRequest){
    ajax = new XMLHttpRequest();
  } else {
    ajax = new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  ajax.onreadystatechange = function (){
    if (ajax.readyState == 4){
      if (ajax.status == 200){
        $("hist").innerHTML = "";
      } else {
        alert("An error has occurred! Status: " + ajax.status);
      }
    }
  }
  
  ajax.open("GET", "cleartext.php?doc=" + doc, true);
  ajax.send();
}
