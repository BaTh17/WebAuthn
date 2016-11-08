/**
 * Main script for client side
 * @author F. Schwab
 */


/**
 * Takes the users input from the login form and calls checkUsername() for submitting the data to the server
 * and handling the response.
 * @param: {void}
 * @returns: {void}
 */

function login(){
	var username = document.getElementById("userNameInput").value;
	var params = "username="+username;
	checkUsername(params, "../PHP/usercheck.php");
 }


/**
 * Helper function for creating new credentials and sending them to the server for registration.
 * Calls makeCredential() from navigator.authentication in the (modified) polyfill file webauthn.js from Microsoft.
 * @param: {string} username, {string} userId
 * @returns: {MSAssertion} result
 */
function makeCredentials(username, userId) {
	
	console.log("makeCredentials() aus utils.js aufgerufen mit Parameter: "+username+ " und "+userId);
	const credAlgorithm = "RSASSA-PKCS1-v1_5";
	
	var userAccountInformation = { 
			  rpDisplayName: "FIVE Webflow",
			  displayName: username,
			  userId: userId
			};
	
	var cryptoParams = [
	              	  { 
	              	    type: "ScopedCred",
	              	    algorithm: credAlgorithm,
	              	  }
	              	];
	
	/* If the promise becomes fullfilled it returns an MSAssertion Object*/
	navigator.authentication.makeCredential(userAccountInformation, cryptoParams).then(function (result) {
				
		var params = "credentials="+JSON.stringify(result);
		sendCredentials(params,"../PHP/processCreds.php");
		console.log("Credentials erstellt. Eintrag in der Indexed DB gemacht. Folgendes Objekt wurde an den Server übertragen: "+JSON.stringify(result))
		
		/*verification whether the new credential object was added to the indexedDB*/
		navigator.authentication.readDB().then(function(credList){
			console.log("Einträge in der Indexed DB: "+credList);
		});
		
		document.getElementById('status').innerHTML = "Keymaterial wurde erstellt. Bitte geben Sie erneut ihren Benutzernamen an."
	    
	    
	}).catch(function(reason) {
		console.log('catch Function called');
        // Windows Hello isn't setup, show dialog explaining how to set it up
        console.log(reason.message);
		if (reason.message === 'NotSupportedError') {
            //showSetupWindowsHelloDialog(true);
            console.log('Windows Hello failed (' + reason.message + ').');
            document.getElementById('status').innerHTML =  
            	'Windows Hello wurde noch nicht eingerichtet. Bitte nachholen. <br><br><button id="helloSetupOK" onclick="makeCredentials(\'' + username + '\' , \'' + userId + '\')">Done and Done</button>';
   
		}
        else {
        	console.log('other problems: '+reason.message);
        }
        
    });

}


/**
 * Helper function for creating an assertion and sending it to the server encoded as JSON.
 * Calls getAssertion() from navigator.authentication in the (modified) polyfill file webauthn.js from Microsoft.
 * @param: {string} challenge
 * @returns: {MSAssertion} assertion
 */

function getAssertion(challenge) {
	navigator.authentication.getAssertion(challenge).then(function(assertion) {
		console.log('Assertion created');
		/*function call for AJAX - direct call with anonymous function didn't work*/
		sendAssertion(JSON.stringify(assertion));
	});
}

/**
 * Takes newly created credentials and sends them to the server by AJAX call.
 * @param: {string} params, url
 * @returns: {void}
 */

function sendCredentials(params, url) {
	
	xmlhttp = new XMLHttpRequest();
	 
	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
	      console.log(xmlhttp.responseText);
	    
	    else if (xmlhttp.status === 401)
	    	document.getElementById('status').innerHTML = "Übertragung an den Server fehlgeschlagen. Fehler: "+xmlhttp.responseText;
	
	}
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  
	  xmlhttp.send(params);
}


/**
 * Takes username input form login form and sends it to the server for verification by AJAX call.
 * Afterwards it handles the return code from the server.
 * @param: {string} params, url
 * @returns: {void}
 */

function checkUsername(params, url) {
	
	xmlhttp = new XMLHttpRequest();
	 
	  xmlhttp.onreadystatechange = function () {
		  
		/*in case of success you'll geht a response text similar to: {"user":"tscm","policy":1}*/
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    	
	      console.log("Responsetext: "+xmlhttp.responseText);
	      var response = JSON.parse(xmlhttp.responseText);
	      
	      console.log("Username ist okay und Policy lautet: "+response.policy+ ". Public Keys sind auf dem Server verfügbar.");
	      	// check whether there are entries in the indexedDB which can be used as credentials
	  		navigator.authentication.readDB().then(function(credList){
			
	  			console.log(credList); // prints out the content of our indexedDB: An array with (various) objects like [{"type":"FIDO_2_0","id":"4BDCC1AF-3169-45CD-A97A-5EDAD7BCCFD2"}]
			
				// check whether there are keys in it which we can use for later assertions
				if (response.policy !== 0 && (typeof credList == 'undefined' || credList.length < 1)) {
					console.log('IndexedCheck DONE');
					document.getElementById('status').innerHTML = "In der IndexDB wurden keine Key-ID's gefunden. Es muss neues Material erstellt werden.<br>"+
		    			"<div id='makeCredButton'><br>"+
		    			'<button id="makeCredButtonID" onclick="makeCredentials(\'' + response.user + '\' , \'' + response.userId + '\')">Make Credentials</button></div>'; 
					
					//Hier muss noch ein Return rein, weil wenn jemand mit Policy 2 sich an einer Station ohne Indexed DB anmeldet kommt zwar kurz die Meldung oben,
					//aber da die Funktion weiter abgearbeitet wird, würde die Weiterleitung zu getAssertion gleich Aktiv werden.
					return;
				}
				
				else
					console.log("Es gibt Einträge in der Indexed DB!");
				
				console.log("User hat Policy: "+response.policy);
			
				//handle the policy: 0/1 requires the submission of the users password, 2 redirects directly to the page where you'll get an assertion
					if(response.policy == 0 || response.policy == 1)
						window.location = "../PHP/login.php";
					else
						{
						Console.log("REDIRECTION TO GET ASSERTION");
						//window.location = "../PHP/getAssertion.php";
						}
						
	  		});
	 }
	    
	    //if the usercheck has failed you'll get the error code 401 back
	    else if (xmlhttp.readyState === 4 && xmlhttp.status === 401) {
	    	document.getElementById('status').innerHTML = "wrong username"; 
		    }
	    
	    //if the usercheck has been successful but there are no keys on the server, you'll get code 202 back
	    else if (xmlhttp.readyState === 4 && xmlhttp.status === 202) {
	    	
		    var response = JSON.parse(xmlhttp.responseText);
		    
		    document.getElementById('status').innerHTML = "Für den Benutzer: "+response.user + " mit userId: " + response.userId + " wurde die Policy 1 oder 2 aktiviert, aber es sind noch keine Public Keys auf dem Server vorhanden." +
		    "<br>"+
	    			"<div id='makeCredButton'><br>"+
	    			'<button id="makeCredButtonID" onclick="makeCredentials(\'' + response.user + '\' , \'' + response.userId + '\')">Make Credentials</button> ' +
	    			"<br><br><p id='helloState'></p></div>";	    	
	    }
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  
	  xmlhttp.send(params);
}


/**
 * Takes users input from the password field and submits it to the server in order to check.
 * Afterwards it handles the return code from the server.
 * @param: {void}
 * @returns: {void}
 */

function checkPW(){

	var pw = document.getElementById("pwInput").value;
	var params = "password="+pw;
	var url = "../PHP/pwCheck.php";
		
	xmlhttp = new XMLHttpRequest();
	 
	  xmlhttp.onreadystatechange = function () {
		  
		//if password check has been successfull you'll be redirected to the appropriate page (Webflow or assertion page)  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    
	     eval(xmlhttp.responseText);
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


/**
 * Sends the assertion as a return from getAssertion() to the server.
 * Afterwards it handles its return code.
 * @param: {void}
 * @returns: {void}
 */

function sendAssertion(assertion){ 
	
	console.log("übermitteln von: "+params);
	var params = "assertion="+assertion;
	var url = "../PHP/handleAssertion.php";
		
	xmlhttp = new XMLHttpRequest();

	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    	console.log("User erfolgreich authentisiert (Validation OK) mit Status: "+xmlhttp.status+ "\n" + "Responsetext: "+xmlhttp.responseText);
	    	document.getElementById('assertionState').innerHTML = "ASSERTION ERFOLGREICH VALIDIERT";
	    }
	    
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 400) {
	    	console.log("Status: "+xmlhttp.readyState + " Validierung der Assertion fehlgeschlagen.  Responsetext: "+ xmlhttp.responseText);
	    	 document.getElementById('assertionState').innerHTML = "Validierung fehlgeschlagen."; 
	    }
	    
	    else {
	    	console.log("Change in State:"+xmlhttp.readyState);
		    }  
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  xmlhttp.send(params);

}









