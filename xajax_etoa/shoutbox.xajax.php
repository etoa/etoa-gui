<?PHP

function getXMLHTTP() {
  var result = false;
  if( typeof XMLHttpRequest != "undefined" ) {
    result = new XMLHttpRequest();
  } else {
    try {
        result = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            result = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (ie) {}
    }
  }
  if (typeof netscape != 'undefined' && typeof netscape.security !=
      'undefined') {
      try {
          netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
      }
      catch (e) {
      }
  }
	return result;
}
//Shout something
function shout(){
	document.getElementById("ajax_butt").value = "Sende...";
	document.getElementById("ajax_butt").disabled = true; 
	var timestamp = new Date().getTime();
	xmlget = getXMLHTTP();
	// 	xmlget.overrideMimeType('text/xml; charset=ISO-8859-1');   //Funktioniert nur im Mozilla, ist hier auch nicht nötig
	xmlget.open("GET", "backend.php?action=write&nick="+escape(document.getElementById("ajax_nick").value)+"&msg="+escape(document.getElementById("ajax_msg").value));
	xmlget.onreadystatechange = function(){
		if ( xmlget.readyState == 4 ) {
				document.getElementById("ajax_butt").value = "Senden";
				document.getElementById("ajax_butt").disabled = false; 
				document.getElementById("ajax_nick").value = "";
				document.getElementById("ajax_msg").value = "";
		}
	}
	xmlget.send(null);
	return true;
}
 
//Fetch entries of the shoutbox
function fetch(){
	var timestamp = new Date().getTime();
	xmlget = getXMLHTTP();
	//xmlget.overrideMimeType('text/xml; charset=ISO-8859-1');
	xmlget.open("GET", "backend.php?action=fetch");
	xmlget.onreadystatechange = function(){
		if ( xmlget.readyState == 4 && xmlget.responseText) {
				if( document.getElementById("ajax_shoutbox").innerHTML != xmlget.responseText){
					var eintraege = xmlget.responseText.split("||||");
					var show = "";
					for(var i = 0; i < eintraege.length; i++){
						var things = eintraege[i].split("|||");
						if(things[0]!="" && things[1]!="" && things[2]!=""){
						show = show+'<span style="cursor: help; font-weight: bold;" title="'+things[1]+'">'+things[0]+':</span><span> &laquo;'+things[2]+'&raquo;</span><br />';
						}
					}
					document.getElementById("ajax_shoutbox").innerHTML = show;
				}
		}
	}
	xmlget.send(null);
	return true;
}
window.onload = "fetch()";
interval = window.setInterval("fetch();", 5000);



?>