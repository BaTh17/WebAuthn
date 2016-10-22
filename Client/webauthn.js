"use strict";

// Editor's draft of spec at https://w3c.github.io/webauthn/#api

// Falls es das Objekt schon gibt, wird dieses dem navigator.authentication
// variable links zugewiesen
// und sonst wird mit der Funktion eines gebaut.

navigator.authentication = navigator.authentication || (function () { // geht
																		// bis
																		// zu
																		// unterst
	console.log("webauthn.js aufgerufen");
	/*
	 * die webauthnDB als Konstante ist eigentlich eine Funktion, die bis und
	 * mit Z.109 alles ausführt, weil sie instantiert wird und damit auch die
	 * Funktion die ihr zugewiesen wurde ausführt
	 */
	

	const webauthnDB = (function() { // Weil er bei einer Konstante ev. einen
										// String erwartet
		
		console.log("Funktion der webauthnDB Variable aufgerufen");
		
		const WEBAUTHN_DB_VERSION = 1;
		const WEBAUTHN_DB_NAME = "_webauthn";
		const WEBAUTHN_ID_TABLE = "identities";

		var db = null;
		var initPromise = null;

		/*
		 * Die Funktion initDB() wird nicht jedes Mal aufgerufen. die Funktion
		 * Oben der webauthnDB ruft die einzelnen Funktionen wie initDB() und
		 * store() nicht einfach automatisch auf
		 */
		
			function initDB() {
	            /* to remove database, use window.indexedDB.deleteDatabase('_webauthn'); */
				return new Promise(function(resolve,reject) {
					var req = indexedDB.open(WEBAUTHN_DB_NAME,WEBAUTHN_DB_VERSION);
					// wird auf das Request Objekt das upgradeneeded Event gefeuert gibt man hier an was gemacht werden soll
					req.onupgradeneeded = function() {
						// new database - set up store
						db = req.result; // this.result oder hier req.result ist die eigentliche DB
						
						/* Es wird ein ObjectStore (~eine Tabelle) in der DB angelegt. Die Einträge werden mit einer ID versehen */
						var store = db.createObjectStore(WEBAUTHN_ID_TABLE, { keyPath: "id"});
					};
					req.onsuccess = function() {
						db = req.result;// db der Variable db zugewiesen
						resolve();
					};
					req.onerror = function(e) {
						reject(e);
					};
				});
			}
		
		function store(id,data) {
			console.log("Enter store()");
			if(!initPromise) { initPromise = initDB(); }// initDBPromise wäre
														// wohl der bessere
														// Namen gewesen
			return initPromise.then(function() { doStore(id,data) });
		}

		function doStore(id,data) {
			console.log("doStore aufgerufen, mit ID:  "+id+" und data: "+data)
			if(!db) throw "DB not initialised";
			return new Promise(function(resolve,reject) {
				// Hier steht was die Funktion machen soll, die das Promise
				// zurückgibt
				var tx = db.transaction(WEBAUTHN_ID_TABLE,"readwrite");
				var store = tx.objectStore(WEBAUTHN_ID_TABLE);
				store.put({id:id,data:data});
				tx.oncomplete = function() {
					resolve();
				}
				tx.onerror = function(e) {
					reject(e);
				};
			});
		}

		function getAll() {
			console.log("getAll called");
			if(!initPromise) { initPromise = initDB(); }
			return initPromise.then(doGetAll);
		}

		function doGetAll() {
			if(!db) throw "DB not initialised";
			return new Promise(function(resolve,reject) {
				var tx = db.transaction(WEBAUTHN_ID_TABLE,"readonly");
				var store = tx.objectStore(WEBAUTHN_ID_TABLE);
				var req = store.openCursor();
				var res = [];
				req.onsuccess = function() {
					var cur = req.result;
					if(cur) {
						res.push({id:cur.value.id,data:cur.value.data});
						cur.continue();
					} else {
						resolve(res);
					}
				}
				req.onerror = function(e) {
					reject(e);
				};
			});
		}
		/*
		 * Das return hier wird sicher immer ausgeführt. Zurück kommt ein Objekt
		 * mit den Eigenschaften store und getAll die man dan aufrufen kann über
		 * Objekt.store, wobei das Objekt hier webauthnDB ist. store und getAll
		 * verweisen auf die Funktionen mit den Namen. Warum keine Parameter?
		 */
		console.log("Jetzt kommt das return");
		return {
			key1: "test",
			store: store, 
			getAll: getAll
		};
		Console.log("const intialisieren abgeschlossen");
		
	} // ENDE VON const webauthnDB = (function() { 

	()); // Das ')' ist die Schlussklammer von (function() { - abgekürzt sähe das so aus: const webauthnDB = (function() {doStuff} );

	
	// Ebene: navigator.authentication || (function () - d.h. makeCredential ist
	// oberste Ebene in der "mainfunction"
    function makeCredential(accountInfo, cryptoParams, attestChallenge, options) {
      	
		var acct = 	{rpDisplayName: accountInfo.rpDisplayName,userDisplayName: accountInfo.displayName};
		var params = [];
		var i;	
		
		if (accountInfo.name) { acct.accountName = accountInfo.name; }
		
		console.log("Existiert das überhaupt? "+accountInfo.id);
		if (accountInfo.id) { acct.userId = accountInfo.id; }
		if (accountInfo.imageUri) { acct.accountImageUri = accountInfo.imageUri; }

		for ( i = 0; i < cryptoParams.length; i++ ) {
			//Wenn Scoped Credentials gefunden werden, dann wird das params-Array mit den Einträgen abgefüllt. Ein Eintrag
			//im params-Array ist jeweils ein Objekt (mit {} angezeigt)
			if ( cryptoParams[i].type === 'ScopedCred' ) {
				params[i] = { type: 'FIDO_2_0', algorithm: cryptoParams[i].algorithm };
			//unter dem Strich wird einfach der ScopedCred zu FIDO gewechselt und der Rest aus dem übergebenen Array übernommen
			} else {
				params[i] = cryptoParams[i];
			}
		}
		//die makeCredential Methode im webauthn.js ruft ihrerseits die msCredentials.makeCredential auf und arbeitet mit dem zurückgegebenen
		//Credentialobjekt (cred). Die ID aus dem Credentialobjekt (=>MSAssertion / ScopedCredentialInfo https://developer.microsoft.com/en-us/microsoft-edge/platform/documentation/dev-guide/device/web-authentication/
		//wird zusammen mit den accountInfos in die indexedDB geschrieben. Die storeFunktion des webauthnDB Objektes gibt ein Promise zurück, aber ohne Value,
		//deshalb returnieren wir nach dem call der Storefunktion das gefreezte Objekt.
        return msCredentials.makeCredential(acct, params).then(function (cred) {
			if (cred.type === "FIDO_2_0") {
				var result = Object.freeze({ //mit Object.freeze wird unveränderbar gemacht. Die Methode gibt ein unveränderliches Objekt zurück
					credential: {type: "ScopedCred", id: cred.id},
					publicKey: JSON.parse(cred.publicKey),
					attestation: cred.attestation
				});
				console.log("hier zeige ich das von der msCredentials.makeCredential Methode retournierte Credentialobjekt (MSAssertion / Scoped Crential) an:");
				console.log(result);
				console.log("Algorithmus des Schlüsselpaares ist: "+result.publicKey.alg);
				return webauthnDB.store(result.credential.id,accountInfo).then(function() 
						{ return result; });
			} 
			
			else {
				return cred;
			}
		});
    } // END of makeCredential
    
    
    
    //Funktion, die ein Promise zurückgibt
    function getCredList(allowlist) {
		var credList = [];
    	if(allowlist) { //Wenn was in der allowlist steht (Check damit nicht exception kommt)
    		
    		return new Promise(function(resolve,reject) {
    			allowlist.forEach(function(item) { //durch Liste loopen, das ist wohl der JavaScript Syntac mit dem function(item)
					if (item.type === 'ScopedCred' ) { //die items sind vom Typ MSCredentialSpec und dort ist der Type immer ScopedCred
						credList.push({ type: 'FIDO_2_0', id: item.id });
					} else {
						credList.push(item); //Könnte es ja auch gleich verwerfen?
					}
    			});
    			resolve(credList); //Wird das Promise erfüllt, wird die credList mit den ID's zurückgegeben
			});
    	} 
    	
    	else { //Ist das die Alternative, wenn keine Optionen mitgegeben werden, dass er in die indexDB schauen geht?? 
    		return webauthnDB.getAll().then(function(list) {
    			list.forEach(item => credList.push({ type: 'FIDO_2_0', id: item.id })); 
    			return credList; //Bekommt man dann die credList aufbereitet durch die Einträge in der indexedDB zurück?
    		});
    	}
    }

    function getAssertion(challenge, options) {
        var allowlist = options ? options.allowList : undefined; //wenn options gesetzt sind, wird der Variable allowlist der Wert options.allowList zugewiesen
		
        return getCredList(allowlist).then(function(credList) { //Wird das Promise erfüllt, wurde eine credList aufbereitet mit ID's die dem Client bekannt sind
			var filter = { accept: credList }; 
			var sigParams = undefined;
			//Nun noch was mit signaturparameter - im Moment wohl nicht wichtig:
			if (options && options.extensions && options.extensions["webauthn_txAuthSimple"]) { sigParams = { userPrompt: options.extensions["webauthn_txAuthSimple"] }; }
			
			//Hier nun der Call der MSLibrary: Sollte das nicht gehen, wenn wir für einen User die credentialID's bereits als CredList mitgeben?
	        return msCredentials.getAssertion(challenge, filter, sigParams).then(function (sig) {
				if (sig.type === "FIDO_2_0"){
					return Object.freeze({
						credential: {type: "ScopedCred", id: sig.id},
						clientData: sig.signature.clientData,
						authenticatorData: sig.signature.authnrData,
						signature: sig.signature.signature
					});
				} else {
					return sig;
				}
			});
		});
    }

    return {
    	
        makeCredential: makeCredential, // Simuliert Getter Methode
        getAssertion: getAssertion,
        
    };
}// Closing von Funktionsdefinition der navigator.authentication || (function
	// () { <----
() // ist dazu da die Funktion gleich auszuführen
);