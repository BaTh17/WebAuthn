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
		
		document.getElementById('status').innerHTML = "Key material was created. Please try to log in with your username."
	    
	    
	}).catch(function(reason) {
		console.log('catch Function called');
        // Windows Hello isn't setup, show dialog explaining how to set it up
        console.log(reason.message);
		if (reason.message === 'NotSupportedError') {
            console.log('Windows Hello failed (' + reason.message + ').');
            document.getElementById('status').innerHTML =  
            	'Windows Hello is not yet established. Please activate it:<br><br><button id="helloSetupOK" class="rounded button" onclick="makeCredentials(\'' + username + '\' , \'' + userId + '\')">Done</button>';
   
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
function getAssertion(challenge, successUrl, userName) {
	navigator.authentication.getAssertion(challenge).then(function(assertion) {
		console.log('Assertion created');
		/*function call for AJAX - direct call with anonymous function didn't work*/
		
		/*Manipulation der Assertion um sicherzustellen, dass die Validierung korrekt ist:*/
		/*
		console.log(Object.values(assertion));
		var rogueAssertion = JSON.stringify(assertion);
		rogueAssertion = JSON.parse(rogueAssertion);
		console.log("manipulating...");
		rogueAssertion.clientData="gibberish";
		console.log("Vergleich Original und manipulierte Assertion: "+JSON.stringify(assertion) + "  "+ JSON.stringify(rogueAssertion));
		*/
		sendAssertion(JSON.stringify(assertion),successUrl, userName);
		console.log(JSON.stringify(assertion));
	}).catch(function(reason) {

		if (reason.message === 'NotSupportedError') {
            //showSetupWindowsHelloDialog(true);
            console.log('Windows Hello failed (' + reason.message + ').');
            document.write('<body><p><b>Windows Hello has not been configured so far</b><br><p>Please follow these steps in order to use <b>Windows Hello<b> authentication in this web application.<br><br>'+
            		'<link rel="stylesheet" href="../CSS/default.css" type="text/css">' +
            		'<div><ol type="1">' + 
            		'<li>Access Settings</li>' +
            		'<li>Select Accounts</li>' +
            		'<li>Select Sign-in options</li>' +
            		'<li>Set Up Windows Hello by clicking on its button or create a PIN when Hello is not available on your device.</li></ol><div></body>'+
            		'<button class="button rounded" onClick="javascript:window.location.href=\'welcome.php\'">OK & Sign-In</button>'
            
            );
   
		}
        else {
        	console.log('other problems: '+reason.message);
        }
        
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
	    	document.getElementById('status').innerHTML = "Transmission to the server failed. error: "+xmlhttp.responseText;
	
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
					document.getElementById('status').innerHTML = "No key-IDs were found in the IndexedDB. New key material has to be created.<br>"+
		    			"<div id='makeCredButton'><br>"+
		    			'<button id="makeCredButtonID" class="rounded button" onclick="makeCredentials(\'' + response.user + '\' , \'' + response.userId + '\')">Make Credentials</button></div>'; 
					
					//Hier muss noch ein Return rein, weil wenn jemand mit Policy 2 sich an einer Station ohne Indexed DB anmeldet kommt zwar kurz die Meldung oben,
					//aber da die Funktion weiter abgearbeitet wird, würde die Weiterleitung zu getAssertion gleich Aktiv werden.
					return;
				}
				
				else
					console.log("Es gibt Einträge in der Indexed DB!");
				
				console.log("User hat Policy: "+response.policy);
			
				//handle the policy: 0/1 requires the submission of the users password, 2 redirects directly to the page where you'll get an assertion
					if(response.policy === 0 || response.policy === 1)
						window.location = "../PHP/login.php";
					
					else {console.log("Hallo"); window.location = "../PHP/getAssertion.php";}
						
					
						
	  		});
	 }
	    
	    //if the usercheck has failed you'll get the error code 401 back
	    else if (xmlhttp.readyState === 4 && xmlhttp.status === 401) {
	    	document.getElementById('status').innerHTML = "username not found"; 
		    }
	    
	    //if the usercheck has been successful but there are no keys on the server, you'll get code 202 back
	    else if (xmlhttp.readyState === 4 && xmlhttp.status === 202) {
	    	
		    var response = JSON.parse(xmlhttp.responseText);
		    
		    document.getElementById('status').innerHTML = "The user: "+response.user + " with userId: " + response.userId + "" +
		    		" has policy 1 or 2 active, but no public keys were found on the server. Please create Credentials. " +

		    "<br>"+
	    			"<div id='makeCredButton'><br>"+
	    			'<button id="makeCredButtonID" class="rounded button" onclick="makeCredentials(\'' + response.user + '\' , \'' + response.userId + '\')">Make Credentials</button> ' +
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
function checkPW(urlRedirection,policy=0){
	var pw = document.getElementById("pwInput").value;
	var params = "password="+pw;
	var url = "../PHP/pwCheck.php";
	if (typeof urlRedirection === "undefined" || urlRedirection === null) {
		//fallback
		urlRedirection = "../PHP/originalWebflowStartPage.php"; 
	  }
	xmlhttp = new XMLHttpRequest();
	 
	  xmlhttp.onreadystatechange = function () {

		//if password check has been successfull you'll be redirected to the appropriate page (Webflow or assertion page)  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    	// no need to post  "+xmlhttp.responseText; 
	     	document.getElementById('pwState').innerHTML = "passwort was correct";
	     	//execute the responseText, that is the next action you have to  do via javascript
	     	eval(xmlhttp.responseText);
	    }else if (xmlhttp.status === 401) {
	    	document.getElementById('pwState').innerHTML = "wrong password"; 
		} else{
			//not an expected answer
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
function sendAssertion(assertion,successUrl,userName){ 
	console.log("übermitteln von: "+params);
	var params = "assertion="+assertion;
	var url = "../PHP/handleAssertion.php";

	xmlhttp = new XMLHttpRequest();

	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	    	console.log("User erfolgreich authentisiert (Validation OK) mit Status: "+xmlhttp.status+ "\n" + "Responsetext: "+xmlhttp.responseText + "");
	    	document.getElementById('assertionState').innerHTML = "Assertion was successfully validated.";

	    	//redirect to utility to build the session, otherwise use window.location = "../PHP/originalWebflowStartPage.php";
	    	var successUrlclean = successUrl.replace(/\//g, "|");
	    	window.location = "../PHP/utility.php?successUrl=" + successUrlclean + "&userName=" + userName + "";
	    	
	    }else if (xmlhttp.readyState === 4 && xmlhttp.status === 400) {
	    	console.log("Status: "+xmlhttp.readyState + " Validierung der Assertion fehlgeschlagen.  Responsetext: "+ xmlhttp.responseText + "");
	    	document.getElementById('assertionState').innerHTML = "validation failed: " + xmlhttp.responseText; 
	    } else {
	    	console.log("Change in State :"+xmlhttp.readyState);
	    	console.log("Responsetext: "+ xmlhttp.responseText);
	    	 
		}  
	}
	
	  xmlhttp.open("POST", url, true);
	  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xmlhttp.setRequestHeader("Content-length", params.length);
	  xmlhttp.setRequestHeader("Connection", "close");
	  xmlhttp.send(params);

}
