/**
 * 
 */


function hello() {
	document.write("Hallo Welt");
}

function getAssertion() {
	document.write("Mit Challenge wird ASsertion gebaut");
}


function makeCredentials() {
	document.write("Es werden neue Challenges kreiirt");
}

/*
 * Funktionen für die Welcome Page
 */

function postAjaxCall(params, url) {
	
	xmlhttp = new XMLHttpRequest();
	 console.log("übermitteln von: "+params);
	 
	 
	  //Events für Response
	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	      console.log(xmlhttp.responseText);
	      //forward to login.php mit bestehendem Session Cookie wo ich mitgeben und Server dadurch weiss, dase es user gibt
	      window.location = '../PHP/login.php';
	      
	    }
	    
	    else if (xmlhttp.status === 401) {
	    	document.getElementById('status').innerHTML = "wrong username"; 
		    }  
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  xmlhttp.send(params);
}


function login(){
	var username = document.getElementById("userNameInput").value;
	var params = "username="+username;
	postAjaxCall(params, "../PHP/usercheck.php");
 }




function checkPW(){ //Hier eventuell auch die AjaxCall Funktion brauchen, aber dann muss ich die HTTP Responses noch mitgeben

	var pw = document.getElementById("pwInput").value;
	var params = "password="+pw;
	var url = "../PHP/pwCheck.php";
		
	xmlhttp = new XMLHttpRequest();
	console.log("übermitteln von: "+params);
	 
	 
	  //Events für Response
	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	     eval(xmlhttp.responseText); //Ausführen vom Code, der bei erfolgreichem PW Check vom Server zurückkam
	     /*
	      *  eval(xmlhttp.responseText) führt im Fall von Policy = 0 zum redirect auf den Webflow oder
	      * bei Policy = 1 zum Aufruf vom getAssertion() auf dem Client. Das heisst pwCheck muss den Code dazu zzurückgeben
	      * und auch gleich (als erstes?) <script src="webauthn.js"></script> ?
	      * 
	      *
	      * 
	      * 
	      */ 
	     document.getElementById('pwState').innerHTML = "PASWORT OK"; 
	      
	    }
	    
	    else if (xmlhttp.status === 401) {
	    	document.getElementById('pwState').innerHTML = "wrong password"; 
		    }  
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  xmlhttp.send(params);
	
	
}