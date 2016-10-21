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
			console.log("Enter InitDB()");
            /*
			 * to remove database, use
			 * window.indexedDB.deleteDatabase('_webauthn');
			 */
			
			return new Promise(function(resolve,reject) {
				// Hier steht was die Funktion machen soll, die das Promise
				// zurückgibt
				var req = indexedDB.open(WEBAUTHN_DB_NAME,WEBAUTHN_DB_VERSION); // =Anlagen
																				// der
																				// Datenbank
																				// mit
																				// Name
																				// und
																				// Versionsnummer
				req.onupgradeneeded = function() { // wird auf das Request
													// Objekt das upgradeneeded
													// Event gefeuert gibt man
													// hier an was gemacht
													// werden soll
					// new database - set up store
					db = req.result; // this.result oder hie req.result ist
										// die eigentliche DB
					var store = db.createObjectStore(WEBAUTHN_ID_TABLE, { keyPath: "id"}); // Es
																							// wird
																							// ein
																							// Object
																							// Store
																							// (=eine
																							// Tabelle)
																							// in
																							// der
																							// DB
																							// angelegt.
																							// Die
																							// Einträge
																							// werden
																							// mit
																							// einer
																							// ID
																							// versehen
																							// und
																							// inkrementiert
				};
				req.onsuccess = function() {
					db = req.result; // db der Variable db zugewiesen
					resolve();
				};
				req.onerror = function(e) {
					reject(e);
				};
			});
		} // END OF INIT DB

		
		
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
		
	} // ENDE VON const webauthnDB = (function() { - dann müsste von hier an
		// das webauthnDB Objekt stehen?

	() // Wozu ist das? => Siehe unten: "Funktion ausführen"
	); // Das ')' ist die Schlussklammer von (function() { - abgekürzt sähe das
		// so aus: const webauthnDB = (function() {doStuff} );
	
	// webauthnDB.store("4", "jdvijijijij");
	
	
	
	
	
	
	// Ebene: navigator.authentication || (function () - d.h. makeCredential ist
	// oberste Ebene in der "mainfunction"
    function makeCredential(accountInfo, cryptoParams, attestChallenge, options) {
      	
		var acct = 	{rpDisplayName: accountInfo.rpDisplayName,userDisplayName: accountInfo.displayName};
		var params = [];
		var i;	
		
		if (accountInfo.name) { acct.accountName = accountInfo.name; }
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
		
        return msCredentials.makeCredential(acct, params).then(function (cred) {
			if (cred.type === "FIDO_2_0") {
				var result = Object.freeze({
					credential: {type: "ScopedCred", id: cred.id},
					publicKey: JSON.parse(cred.publicKey),
					attestation: cred.attestation
				});
				return webauthnDB.store(result.credential.id,accountInfo).then(function() 
						{ return result; });
			} else {
				return cred;
			}
		});
    } // END of makeCredential
    
    
    

    function getCredList(allowlist) {
		var credList = [];
    	if(allowlist) {
    		return new Promise(function(resolve,reject) {
    			allowlist.forEach(function(item) {
					if (item.type === 'ScopedCred' ) {
						credList.push({ type: 'FIDO_2_0', id: item.id });
					} else {
						credList.push(item);
					}
    			});
    			resolve(credList);
			});
    	} else {
    		return webauthnDB.getAll().then(function(list) {
    			list.forEach(item => credList.push({ type: 'FIDO_2_0', id: item.id })); 
    			return credList;
    		});
    	}
    }

    function getAssertion(challenge, options) {
        var allowlist = options ? options.allowList : undefined;
		return getCredList(allowlist).then(function(credList) {
			var filter = { accept: credList }; 
			var sigParams = undefined;
			if (options && options.extensions && options.extensions["webauthn_txAuthSimple"]) { sigParams = { userPrompt: options.extensions["webauthn_txAuthSimple"] }; }

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