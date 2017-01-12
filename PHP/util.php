<?php

/**
 * Mock Up File to enable coding without fully implementet utility-functions during our project
 * commented parts are left intentionally
 * @param unknown $username
 * @param unknown $keyID
 */
require_once('utility.php');


function getPublicKey($username, $keyID){

	//return "c20ad4d76fe97759aa27a0c99bff6710";
	return _plugin_utility::getPublicKey($username, $keyID);

}


/**
 * check username exists and is active
 * Return: boolean
 */
function checkUsername($username) {

	return _plugin_utility::checkUsername($username);
	//User exists and is activ

	// 	if($username=="schf" || $username=="tscm" || $username =="hello")
	// 		return true;
	// 	else
	// 		return false;

}

/**
 * checkPassword() - check PW of user
 * Return: boolean
 *
 */
function checkPW($username, $pw) {
	return _plugin_utility::checkPW($username, $pw);

	// 	if($username=="schf" && $pw=="123456")
	// 		return true;
	// 	if($username=="tscm" && $pw=="123456")
	// 		return true;

	// 	else
	// 		return false;
}

/**
 * @return: Integer
 * 0 = Password only
 * 1 = 2-FA
 * 2 = Passwordless
 */
function getPolicy($username) {
	return _plugin_utility::getPolicyFromUser($username, false, true);

//			if($username=="schf")
//						return 0;
//					if($username=="tscm")
//								return 1;
//							if($username=="hello")
//										return 2;

}

/**
 * checks if user has keys registred
 * Return: boolean
 *
 */
function hasKeys($username) {

	return _plugin_utility::hasKeys($username);

// 	if($username=="schf")
// 			return true;
// 			if($username=="tscm")
// 					return true;
// 				if($username=="hello")
// 						return true;

}

/**
 * Save credentials of a username.
 * @params {strings}
 * @return boolean
 * 
 */
function saveCredentials($username, $id, $pubKey) {
	return _plugin_utility::saveCredentials($username, $id, $pubKey);
	//return true;
}

function getChallenge() {
	return _plugin_utility::getChallenge();
	//return md5(openssl_random_pseudo_bytes(16));
}

/**
 * Get configuration into SESSION variables
 * @param {int} $configSetting
 * @return {void}
 */
function getConfiguration($configSetting = 1) {
	return _plugin_utility::getConfiguration($configSetting);
}

?>
