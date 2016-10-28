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
	//alert("Es werden neue Challenges kreiirt");
	
	const credAlgorithm = "RSASSA-PKCS1-v1_5";
	
	var userAccountInformation = { 
			  rpDisplayName: "Test Site",
			  displayName: "Adrian Bateman",
			  //accoutnName: "u114415"
			};
	var cryptoParams = [
	              	  { 
	              	    type: "ScopedCred",
	              	    algorithm: credAlgorithm,
	              	  }
	              	];

	// Note: The following call will cause the authenticator to display UI.
	
	/*
	 * Rückgabewert von navigator.authentication.makeCredential bei Erfolg:
	 */
	navigator.authentication.makeCredential(userAccountInformation, cryptoParams).then(function (result) {
		
		/*
		 * Ich schicke gleich das ganze result Objekt an den Server und parse es dort.
		 */
		
		var params = "credentials="+JSON.stringify(result);
		sendCredentials(params,"../PHP/processCreds.php");
		
//		var idToServer = JSON.stringify(result.credential.id);
//		var keyToServer = JSON.stringify(result.publicKey.n);

		console.log("Credentials wurden erstellt und in der Indexed DB gespeichert. Folgendes Objekt wurde an den Server übertragen:")
		console.log(JSON.stringify(result));
				
		navigator.authentication.readDB().then(function(credList){
			console.log(credList);
		}); //nun muss dies zurückmelden, dass Einträge existieren. Nun wird eine Meldung auf die Seite geschrieben, dass Keys erstellt wurden
		
		document.getElementById('status').innerHTML = "Keymaterial wurde erstellt. Bitte loggen Sie sich ein mit der Eingabe ihres Benutzernamens."
	    
	    
	}).catch(function (err) {
	    // No acceptable authenticator or user refused consent. Handle appropriately.
	    alert(err);
	});


}





/*
 * Übertragen der Credentials
 */

function sendCredentials(params, url) {
	
	xmlhttp = new XMLHttpRequest();
	 
	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	      console.log(xmlhttp.responseText);
	      
	    }
	    
	    else if (xmlhttp.status === 401) {

		    }
	
	}
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  
	  xmlhttp.send(params);
}




/*
 * Handeln des übermitteln vom Usernamen
 */

function postAjaxCall(params, url) {
	
	xmlhttp = new XMLHttpRequest();
	 
	  //Events für Response
	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	      console.log("Responsetext: "+xmlhttp.responseText);
	      var response = JSON.parse(xmlhttp.responseText); //ein JSON das zurückkommt
	      
	      console.log("Username ist okay, und falls Policy 1/2 existiert sind angeblich auch Public Keys auf dem Server." +
	      		"jetzt wird geprüft, ob die Indexed-DB Einträge enthält!");
	     
	    //Prüfen, ob in der indexedDB Credentials existieren
	  	navigator.authentication.readDB().then(function(credList){
			console.log(credList);
			console.log("User hat Policy: "+response.policy);
			if(response.policy == 0)
				window.location = "../PHP/login.php";
		});
	  	
	  	
		  	
	    }
	    
	   
	    
	    else if (xmlhttp.status === 401) {
	    	document.getElementById('status').innerHTML = "wrong username"; 
		    }
	    else if (xmlhttp.status === 202) {
	    	document.getElementById('status').innerHTML = "Für den Benutzer wurde die Policy 1 oder 2 aktiviert, aber es sind noch keine Public Keys auf dem Server vorhanden." +
	    			"<br>"+
	    			"<div id='makeCredButton'><br>"+
	    			"<button id='makeCredButtonID' onclick='makeCredentials()'>Make Credentials</button></div>";
	    			
	    	
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