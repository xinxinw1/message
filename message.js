/****** Simple Instant Message ******/

var version = "1.8";

function $(a){
  return document.getElementById(a);
}

var origTitle = document.title;
var newTitle = "(!) " + origTitle;
var isBlurred = false;

function runOnload(){
  if (isEmpty(name) || isEmpty(doc)){
    if (isEmpty(name))name = prompt("Enter your name: ", "");
    if (!isEmpty(name)){
      if (isEmpty(doc))doc = prompt("Enter document name: ", "");
      if (!isEmpty(doc)){
        location.assign("?name=" + encodeURIComponent(name) + "&doc=" + encodeURIComponent(doc));
      }
    }
  } else {
    setText($("name"), name);
    setText($("doc"), doc);
    receiveData(data);
    window.onfocus = function (){
      document.title = origTitle;
      isBlurred = false;
    };
    window.onblur = function (){
      isBlurred = true;
    };
    checkNew();
    /*var str = location.pathname;
    if (str.indexOf("latest") == -1 && str.indexOf("devel") == -1){
      var time = getDateTime();
      var msg = "Notice: You are not on the latest version of Simple Instant Message.";
      receiveData("error|" + encodeURIComponent(time) + "|" + encodeURIComponent(msg) + "\n");
      msg = "Please update your bookmarks to ";
      if (str.indexOf("test") != -1)msg += "http://musiclifephilosophy.com/test/message/latest/message.php";
      else msg += "http://musiclifephilosophy.com/codes/message/latest/message.php";
      receiveData("error|" + encodeURIComponent(time) + "|" + encodeURIComponent(msg) + "\n");
    }*/
    checkConnection();
    $("text").onkeydown = checkEnter;
    $("clear").onclick = function (){
      clearText();
    }
    $("source").onclick = function (){
      window.open('docs/' + encodeURIComponent(doc), '_blank', 'height=550, width=900, top=150, left=200, menubar=no, resizable=yes, scrollbars=yes, status=no, toolbar=no');
    }
  }
}

Date.prototype.getMonthName = function (){
  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  return monthNames[this.getMonth()];
}

Date.prototype.getDayName = function (){
  var dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  return dayNames[this.getDay()];
}

function getDateTime(){
  var date = new Date();
  var weekday = date.getDayName();
  var month = date.getMonthName();
  var day = date.getDate();
  var year = date.getFullYear();
  var hours = date.getHours();
  var ampm;
  if (hours >= 12)ampm = "PM";
  else ampm = "AM";
  if (hours > 12)hours -= 12;
  if (hours == 0)hours = 12;
  hours = String(hours);
  if (hours.length <= 1)hours = "0" + hours;
  var mins = String(date.getMinutes());
  if (mins.length <= 1)mins = "0" + mins;
  var secs = String(date.getSeconds());
  if (secs.length <= 1)secs = "0" + secs;
  
  var time = weekday + " " + month + " " + day + ", " + year + "|" + hours + ":" + mins + ":" + secs + " " + ampm;
  return time;
}

function isEmpty(text){
  if (text == "" || text == null || text == "null")return true;
  else return false;
}

function newName(){
  var newName = prompt("Enter your name: ", name);
  if (!isEmpty(newName)){
    location.assign("?name=" + encodeURIComponent(newName) + "&doc=" + encodeURIComponent(doc));
  }
}

function newDoc(){
  var newDoc = prompt("Enter document name: ", doc);
  if (!isEmpty(newDoc)){
    location.assign("?name=" + encodeURIComponent(name) + "&doc=" + encodeURIComponent(newDoc));
  }
}

function newEvent(){
  if (isBlurred)document.title = newTitle;
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
      newEvent();
    });
    src.addEventListener("clear", function (e){
      $("hist").innerHTML = "";
      names = [];
      dates = [];
      newEvent();
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
      dates = [];
    }
    newEvent();
    
    checkNewOnce();
  }
  var type = "GET";
  
  ajaxRequest(file, params, func, type);
}

function clearText(){
  var file = "index.php";
  var params = "type=clearText&doc=" + encodeURIComponent(doc);
  params += "&name=" + encodeURIComponent(name);
  var func = function (resp){}
  var type = "POST";
  
  ajaxRequest(file, params, func, type);
}

function sendMessage(text){
  var file = "index.php";
  var params = "type=sendMessage&doc=" + encodeURIComponent(doc);
  params += "&name=" + encodeURIComponent(name);
  params += "&text=" + encodeURIComponent(text);
  var func = function (resp){
    $("text").value = "";
  }
  var type = "POST";
  
  ajaxRequest(file, params, func, type);
}

function sendNotice(text, async){
  var file = "index.php";
  var params = "type=sendNotice&doc=" + encodeURIComponent(doc);
  params += "&text=" + encodeURIComponent(text);
  var func = function (resp){};
  var type = "POST";
  if (async == undefined)async = true;
  
  ajaxRequest(file, params, func, type, async);
}

function checkConnection(){
  var file = "index.php";
  var params = "type=checkConnection&doc=" + encodeURIComponent(doc);
  params += "&name=" + encodeURIComponent(name);
  var func = function (resp){};
  var type = "GET";
  var async = true;
  
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

function dataToFragment(data){
  if (data == "")return document.createDocumentFragment();
  
  var datas = data.split("|");
  
  var type = datas[0];
  if (type == "message"){
    var datetime = decodeURIComponent(datas[1]);
    var name = decodeURIComponent(datas[2]);
    var text = decodeURIComponent(datas[3]);
    
    var d = document.createDocumentFragment();
    
    d.appendChild(makeDateDiv(datetime));
    
    var colorSpan = document.createElement("span");
    colorSpan.setAttribute("class", "color" + getColor(name));
    
    colorSpan.appendChild(makeTimeSpan(datetime));
    
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
    var datetime = decodeURIComponent(datas[1]);
    var text = decodeURIComponent(datas[2]);
    
    var d = document.createDocumentFragment();
    
    d.appendChild(makeDateDiv(datetime));
    
    d.appendChild(makeTimeSpan(datetime));
    
    d.appendChild(document.createTextNode(" "));
    
    var textSpan = document.createElement("span");
    textSpan.setAttribute("class", "notice");
    textSpan.appendChild(document.createTextNode(text));
    d.appendChild(textSpan);
    
    return d;
  }
  if (type == "error"){
    var datetime = decodeURIComponent(datas[1]);
    var text = decodeURIComponent(datas[2]);
    
    var d = document.createDocumentFragment();
    
    d.appendChild(makeDateDiv(datetime));
    
    var colorSpan = document.createElement("span");
    colorSpan.setAttribute("class", "error");
    
    colorSpan.appendChild(makeTimeSpan(datetime));
    
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

function makeTimeSpan(datetime){
  var time = datetime.split("|")[1];
  if (time == undefined)time = datetime.split("|")[0];
  var timeSpan = document.createElement("span");
  timeSpan.setAttribute("class", "time");
  timeSpan.appendChild(document.createTextNode("(" + time + ")"));
  return timeSpan;
}

var dates = [];
function makeDateDiv(datetime){
  var date = datetime.split("|")[0];
  if (datetime.split("|")[1] == undefined)return document.createTextNode("");
  if (dates.indexOf(date) != -1)return document.createTextNode("");
  else dates.push(date);
  
  var dateDiv = document.createElement("div");
  dateDiv.setAttribute("class", "date");
  dateDiv.appendChild(document.createTextNode(date));
  return dateDiv;
}

var names = [];
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
  
  if (type == "GET"){
    ajax.open("GET", file + "?" + params, true);
    ajax.send();
  } else if (type == "POST"){
    ajax.open("POST", file, true);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(params);
  }
  
  if (!async)func(ajax.responseText);
}
