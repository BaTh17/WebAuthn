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
		
		
		var idToServer = JSON.stringify(result.credential.id);
		var keyToServer = JSON.stringify(result.publicKey.n);
		
		var credToServer = idToServer + keyToServer;
		
		console.log("Daten an Server: "+credToServer);
		console.log("Credentials wurden erstellt und in der Indexed DB gespeichert.")
		console.log(JSON.stringify(result));
		
		
		//ID und Public Key extrahieren und an Server schicken.
		
		console.log("<br>ID und Public Key an Server schicken. ID: "+id + ", UND KEY: "+JSON.stringify(publicKey));
		
		navigator.authentication.readDB().then(function(credList){
			console.log(credList);
		}
		)		;
//		var credList = [];
//		
//		//webauthnDB is undefined und wenn ich navigator.authentication. vornedran hänge ist es nicht initialisiert
//		navigator.authentication.webauthnDB.getAll().then(function(list) {
//			list.forEach(item => credList.push({ type: 'FIDO_2_0', id: item.id })); 
//		});
//	    console.log(credList);

	    
	    
	}).catch(function (err) {
	    // No acceptable authenticator or user refused consent. Handle appropriately.
	    alert(err);
	});


}


/*
 * Funktionen für die Welcome Page
 */

function postAjaxCall(params, url) {
	
	xmlhttp = new XMLHttpRequest();
	 
	  //Events für Response
	  xmlhttp.onreadystatechange = function () {
		  
	    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	      console.log(xmlhttp.responseText);
	      console.log("Username ist okay, und falls Policy 1/2 existiert sind angeblich auch Public Keys auf dem Server." +
	      		"jetzt wird geprüft, ob die Indexed-DB Einträge enthält!");
	      
	  	navigator.authentication.readDB().then(function(credList){
			console.log(credList);
		}
		);
	  	
	  	
	      
	      //forward to login.php mit bestehendem Session Cookie wo ich mitgeben und Server dadurch weiss, dase es user gibt
	      //window.location = '../PHP/login.php';
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