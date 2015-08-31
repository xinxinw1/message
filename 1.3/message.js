/****** Simple Instant Message 1.3 ******/

function $(a){
  return document.getElementById(a);
}

$("text").onkeydown = checkEnter;

window.onload = function (){
  if (name == "" || doc == ""){
    while (name == "")name = prompt("Enter your name: ");
    while (doc == "")doc = prompt("Enter document name: ");
    window.location.assign("message.php?name=" + encodeURIComponent(name) + "&doc=" + encodeURIComponent(doc));
  }
  setText($("top"), "Name: " + name + " | Doc: " + doc);
  receiveData(data);
  checkNew();
  var str = location.pathname;
  if (str.indexOf("latest") == -1 && str.indexOf("devel") == -1){
    var date = new Date();
    var time = date.toLocaleTimeString(); 
    var msg = "Notice: You are not on the latest version of Simple Instant Message.";
    receiveData("error|" + encodeURIComponent(time) + "|" + encodeURIComponent(msg) + "\n");
    msg = "Please update your bookmarks to ";
    if (str.indexOf("test") != -1)msg += "http://musiclifephilosophy.com/test/message/latest/message.php";
    else msg += "http://musiclifephilosophy.com/codes/message/latest/message.php";
    receiveData("error|" + encodeURIComponent(time) + "|" + encodeURIComponent(msg) + "\n");
  }
  sendNotice(name + " is online.");
}

window.onbeforeunload = function (){
  sendNotice(name + " is offline.", false);
}

$("clear").onclick = function (){
  clearText();
}

function checkEnter(e){
  if (window.event)e = event;
  var key = (e.keyCode)?e.keyCode:-1;
  if (!e.shiftKey && key == 13){
    sendMessage($("text").value);
    return false;
  }
}

function setText(a, text){
  if (a.hasChildNodes()){
    a.firstChild.nodeValue = text;
  } else {
    a.appendChild(document.createTextNode(text));
  }
}

function checkNew(){
  if (typeof(EventSource) !== "undefined"){
    var file = "checknew.php";
    var params = "doc=" + encodeURIComponent(doc);
    params += "&pos=" + pos;
    var src = new EventSource(file + "?" + params);
    src.addEventListener("message", function (e){
      receiveData(decodeURIComponent(e.data));
    });
    src.addEventListener("clear", function (e){
      $("hist").innerHTML = "";
      names = [];
    });
  } else {
    checkNewOnce();
  }
}

function checkNewOnce(){
  var file = "checknewonce.php";
  var params = "doc=" + encodeURIComponent(doc) + "&pos=" + pos;
  var func = function (resp){
    var resps = resp.split("\n");
    var data = decodeURIComponent(resps[0]);
    pos = resps[1];
    if (pos != 0){
      receiveData(decodeURIComponent(data));
    } else {
      $("hist").innerHTML = "";
      names = [];
    }
    
    checkNewOnce();
  }
  var type = "get";
  
  ajaxRequest(file, params, func, type);
}

function clearText(){
  var file = "message.php";
  var params = "type=clearText&doc=" + encodeURIComponent(doc);
  var func = function (resp){}
  var type = "get";
  
  ajaxRequest(file, params, func, type);
}

function sendMessage(text){
  var file = "message.php";
  var params = "type=sendMessage&doc=" + encodeURIComponent(doc);
  params += "&name=" + encodeURIComponent(name);
  params += "&text=" + encodeURIComponent(text);
  var func = function (resp){
    $("text").value = "";
  }
  var type = "post";
  
  ajaxRequest(file, params, func, type);
}

function sendNotice(text, async){
  var file = "message.php";
  var params = "type=sendNotice&doc=" + encodeURIComponent(doc);
  params += "&text=" + encodeURIComponent(text);
  var func = function (resp){};
  var type = "post";
  if (async == undefined)async = true;
  
  ajaxRequest(file, params, func, type, async);
}

function receiveData(data){
  var datas = data.split("\n");
  var d = document.createDocumentFragment();
  for (var i = 0; i < datas.length-1; i++){
    d.appendChild(dataToFragment(datas[i] + "\n"));
  }
  
  $("hist").appendChild(d);
  $("hist").scrollTop = $("hist").scrollHeight;
}

var names = [];
function dataToFragment(data){
  if (data == "")return document.createDocumentFragment();
  
  var datas = data.split("|");
  
  var type = datas[0];
  if (type == "message"){
    var time = decodeURIComponent(datas[1]);
    var name = decodeURIComponent(datas[2]);
    var text = decodeURIComponent(datas[3]);
    
    var d = document.createDocumentFragment();
    
    var colorSpan = document.createElement("span");
    colorSpan.setAttribute("class", "color" + getColor(name));
    
    var timeSpan = document.createElement("span");
    timeSpan.setAttribute("class", "time");
    timeSpan.appendChild(document.createTextNode("(" + time + ")"));
    colorSpan.appendChild(timeSpan);
    
    colorSpan.appendChild(document.createTextNode(" "));
    
    var nameSpan = document.createElement("span");
    nameSpan.setAttribute("class", "name");
    nameSpan.appendChild(document.createTextNode(name + ": "));
    colorSpan.appendChild(nameSpan);
    
    d.appendChild(colorSpan);
    d.appendChild(document.createTextNode(text));
    
    return d;
  }
  if (type == "notice"){
    var time = decodeURIComponent(datas[1]);
    var text = decodeURIComponent(datas[2]);
    
    var d = document.createDocumentFragment();
    
    var timeSpan = document.createElement("span");
    timeSpan.setAttribute("class", "time");
    timeSpan.appendChild(document.createTextNode("(" + time + ")"));
    d.appendChild(timeSpan);
    
    d.appendChild(document.createTextNode(" "));
    
    var textSpan = document.createElement("span");
    textSpan.setAttribute("class", "notice");
    textSpan.appendChild(document.createTextNode(text));
    d.appendChild(textSpan);
    
    return d;
  }
  if (type == "error"){
    var time = decodeURIComponent(datas[1]);
    var text = decodeURIComponent(datas[2]);
    
    var d = document.createDocumentFragment();
    
    var colorSpan = document.createElement("span");
    colorSpan.setAttribute("class", "error");
    
    var timeSpan = document.createElement("span");
    timeSpan.setAttribute("class", "time");
    timeSpan.appendChild(document.createTextNode("(" + time + ")"));
    colorSpan.appendChild(timeSpan);
    
    colorSpan.appendChild(document.createTextNode(" "));
    
    var textSpan = document.createElement("span");
    textSpan.setAttribute("class", "notice");
    textSpan.appendChild(document.createTextNode(text));
    colorSpan.appendChild(textSpan);
    
    d.appendChild(colorSpan);
    
    return d;
  }
  
  return document.createDocumentFragment();
}

function getColor(name){
  if (names.indexOf(name) != -1)return names.indexOf(name);
  else return names.push(name)-1;
}

function ajaxRequest(file, params, func, type, async){
  var ajax;
  if (window.XMLHttpRequest){
    ajax = new XMLHttpRequest();
  } else {
    ajax = new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  if (async == undefined)async = true;
  if (async){
    ajax.onreadystatechange = function (){
      if (ajax.readyState == 4){
        if (ajax.status == 200){
          func(ajax.responseText);
        } else if (ajax.status != 0){
          alert("An error has occurred! Status: " + ajax.status);
        }
      }
    }
  }
  
  if (type == "get"){
    ajax.open("GET", file + "?" + params, true);
    ajax.send();
  } else if (type == "post"){
    ajax.open("POST", file, true);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(params);
  }
  
  if (!async)func(ajax.responseText);
}
