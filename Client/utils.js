/**
 * 
 */


function hello() {
	document.write("Hallo Welt");
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

/* get Assertion - Helperfunktion */

function getAssertion(challenge) {
	navigator.authentication.getAssertion(challenge).then(function(assertion) {
		console.log('Assertion created');
		
		/*
		 * Eventuell befindet sicher der User auf der loging.php seite, nämlich wenn er policy code = 1 hat. Bei Code = 2 wurde das hier von getAssertion.php aufgerufen
		 */
		
		//document.getElementById('status').innerHTML = assertion.credential.id;
	
		/*Funktionsaufruf für Ajax Call - direkt den call mit anonymer Funktion machen hat nicht geklappt*/
		handleAssertion(JSON.stringify(assertion));				

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
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) { //Userüberprüfung erfolgreich. $responseText = json_encode(array("user" => $username, "policy" => $policy));
	    	
	      console.log("Responsetext: "+xmlhttp.responseText);
	      var response = JSON.parse(xmlhttp.responseText); //ein JSON das zurückkommt: {"user":"x","policy":y}
	      
	      console.log("Username ist okay, und falls Policy 1/2 existiert sind angeblich auch Public Keys auf dem Server." +
	      		"jetzt wird geprüft, ob die Indexed-DB Einträge enthält!");
	     
	    //Prüfen, ob in der indexedDB Credentials existieren
	  	navigator.authentication.readDB().then(function(credList){
			console.log(credList); //gibt das CredList Objekt aus. 
			
			//Prüfen, ob Keys in der indexed DB sind
			if (typeof credList == 'undefined' || credList.length < 1) {
				console.log("Keine Items in der indexed DB!");
			}
			else {
				console.log("Es gibt Einträge in der Indexed DB!");
			}
			
			console.log("User hat Policy: "+response.policy);
			
			if(response.policy == 0 || response.policy == 1)
				window.location = "../PHP/login.php";
			else
				window.location = "../PHP/getAssertion.php";
				
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

function handleAssertion(params){ 

	console.log("handleAssertion was called mit Parameter"+params);
	var params = "assertion="+params;
	var url = "../PHP/handleAssertion.php";
		
	xmlhttp = new XMLHttpRequest();
	console.log("übermitteln von: "+params);

	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    	console.log("USER ERFOLGREICH AUTHENTISIERT mit Status: "+xmlhttp.status+ "  |  Responsetext= "+xmlhttp.responseText);
	    	
	    	//window.location = "../PHP/homepage.php";
	    }
	    
	    else { //Das wird mehrmals aufgerufen, wenn ich keine Condition rein tue, weil der onreadystatechange mehrmals wechselt von 0-4
	    	console.log("State Change:"+xmlhttp.readyState);
		    }  
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  xmlhttp.send(params);

}









