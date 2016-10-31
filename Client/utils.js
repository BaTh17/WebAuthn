/**
 *utils.js: Main Scriptfile für den Client
 * 
 */

function makeCredentials() {
	//alert("Es werden neue Challenges kreiirt");
	
	const credAlgorithm = "RSASSA-PKCS1-v1_5";
	
	var userAccountInformation = { 
			  rpDisplayName: "IHEG1-2-17",
			  displayName: "Fabian Schwab",
			  //accoutnName: "u114415"
			};
	var cryptoParams = [
	              	  { 
	              	    type: "ScopedCred",
	              	    algorithm: credAlgorithm,
	              	  }
	              	];


	/* Rückgabewert von navigator.authentication.makeCredential bei Erfolg: Ein Promise vom Typ MSAssertion */
	navigator.authentication.makeCredential(userAccountInformation, cryptoParams).then(function (result) {
		
		/* Ich schicke gleich das ganze result Objekt an den Server und parse es dort. */
		
		var params = "credentials="+JSON.stringify(result);
		sendCredentials(params,"../PHP/processCreds.php");
		
//		var idToServer = JSON.stringify(result.credential.id);
//		var keyToServer = JSON.stringify(result.publicKey.n);

		console.log("Credentials erstellt. Eintrag in der Indexed DB gemacht. Folgendes Objekt wurde an den Server übertragen: "+JSON.stringify(result))
				
		navigator.authentication.readDB().then(function(credList){
			console.log("Einträge in der Indexed DB: "+credList);
		}); //nun muss dies zurückmelden, dass Einträge existieren. Nun wird eine Meldung auf die Seite geschrieben, dass Keys erstellt wurden
		
		document.getElementById('status').innerHTML = "Keymaterial wurde erstellt. Bitte loggen Sie sich ein mit der Eingabe ihres Benutzernamens."
	    
	    
	}).catch(function(reason) {
		console.log('catch Function called');
        // Windows Hello isn't setup, show dialog explaining how to set it up
        console.log(reason.message);
		if (reason.message === 'NotSupportedError') {
            //showSetupWindowsHelloDialog(true);
            console.log("showSetupWindowsHelloDialog hätte kommen sollen");
            console.log('Windows Hello failed (' + reason.message + ').');
            document.getElementById('helloState').innerHTML =  "Windows Hello wurde noch nicht eingerichtet! Mach das und das.<br><button id='helloSetupOK' onclick='makeCredentials()'>Done and Done</button>";
        }
        else {
        	console.log('other problems: '+reason.message);
        }
        
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
		sendAssertion(JSON.stringify(assertion));				

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
	    	
	      //console.log("Responsetext: "+xmlhttp.responseText);
	      var response = JSON.parse(xmlhttp.responseText); //ein JSON das zurückkommt: {"user":"x","policy":y}
	      
	      console.log("Username ist okay, und falls Policy 1/2 existiert sind angeblich auch Public Keys auf dem Server." +
	      		"jetzt wird geprüft, ob die Indexed-DB Einträge enthält!");
	     
	      	//Prüfen, ob in der indexedDB Credentials existieren. Wir gehen davon aus, dass der passende Public Key auf dem Server liegt
	  		navigator.authentication.readDB().then(function(credList){
			
	  			console.log(credList); //gibt das CredList Objekt aus. Ein Array mit (mehreren) Scoped Credentials: [{"type":"FIDO_2_0","id":"4BDCC1AF-3169-45CD-A97A-5EDAD7BCCFD2"}]
			
				//Prüfen, ob Keys in der indexed DB sind
				if (response.policy != 0 && typeof credList == 'undefined' || credList.length < 1) {
					console.log("Keine Items in der indexed DB!");
					document.getElementById('status').innerHTML = 
						"In der IndexDB wurden keine Key-ID's gefunden. Es muss neues Material erstellt werden.<br>"+
		    			"<div id='makeCredButton'><br>"+
		    			"<button id='makeCredButtonID' onclick='makeCredentials()'>Make Credentials</button></div>"; 
					return;
				}
				else {
					console.log("Es gibt Einträge in der Indexed DB!");
				}
				
				console.log("User hat Policy: "+response.policy);
			
					if(response.policy == 0 || response.policy == 1)
						window.location = "../PHP/login.php";
					else
						window.location = "../PHP/getAssertion.php";
				
	  		});//Ende des readDB()-Aufufs.
	  		
	 }//Ende der Schleife, die einen erfolgreichen Account check weitertreibt
	    
	    
	    else if (xmlhttp.status === 401) {
	    	document.getElementById('status').innerHTML = "wrong username"; 
		    }
	    else if (xmlhttp.status === 202) { //Username okay, aber es kann nicht weitergemacht werden, weil auf dem Server keine Public Keys liegen
	    	document.getElementById('status').innerHTML = "Für den Benutzer wurde die Policy 1 oder 2 aktiviert, aber es sind noch keine Public Keys auf dem Server vorhanden." +
	    			"<br>"+
	    			"<div id='makeCredButton'><br>"+
	    			"<button id='makeCredButtonID' onclick='makeCredentials()'>Make Credentials</button> " +
	    			"<br><br><p id='helloState'></p></div>";	    	
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

function sendAssertion(assertion){ 

	console.log("utils.js hat sendAssertion() aufgerufen mit Parameter"+assertion);
	var params = "assertion="+assertion;
	var url = "../PHP/handleAssertion.php";
		
	xmlhttp = new XMLHttpRequest();
	console.log("übermitteln von: "+params);

	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    	console.log("USER ERFOLGREICH AUTHENTISIERT mit Status: "+xmlhttp.status+ "\n" + "Responsetext: "+xmlhttp.responseText);
	    	document.getElementById('assertionState').innerHTML = "ASSERTION ERFOLGREICH VALIDIERT";
	    }
	    
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 400) {
	    	console.log("Validierung der Assertion fehlgeschlagen:   |  Responsetext= "+xmlhttp.responseText);
	    	 document.getElementById('assertionState').innerHTML = "Validierung fehlgeschlagen. Try again [BUTTON]"; 
	    }
	    
	    else { //Das wird mehrmals aufgerufen, wenn ich keine Condition rein tue, weil der onreadystatechange mehrmals wechselt von 0-4
	    	console.log("Change in State:"+xmlhttp.readyState);
		    }  
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  xmlhttp.send(params);

}









