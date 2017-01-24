<?php
/*
* CONCEPT:
* after user login, backend creates cookie with a random session, that only user-browser and server know about.
* unless user visits other sites that read/steal the cookie.
* 
* PREVENT COOKIE STEALING: 
* the attacker (and attacker's website) not only need to steal cookie but also need to change sender-IP.
* the IP is logged (passwd -> ip_login) during login process.
* 
* PASSWORDS:
* all passwords are sha256 hashed on client side and never submitted clear-text.
* md5 is a 'cleartext'-one-way->'5f4dcc3b5aa765d61d8327deb882cf99' encryption
* that was not broken yet.
* except: if you use a simple dictionary based password.
*
* SO ONLY USE AT LEAST 8 CHARS CONSISTING OF ALPHABET, DIGITS AND SPECIAL CHARS LIKE !&()
* OR YOUR ACCOUNT IS NOT SAFE!
* 
* SQL INJECTION:
* per default all incoming arguments are mysql-real-escaped, so this should prevent an sql injection input form.
* 
* SSL:
* SSL should be used with TOR also, so please generate a self-signed-SSL certificate, it will not cost you anything or get one from here: https://letsencrypt.org/
 */

if(config::get('force_ssl'))
{
	// force user to use ssl
	if($_SERVER["HTTPS"] != "on")
	{
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}
}

// escape everything
foreach ($_REQUEST as $key => $value)
{
	$_REQUEST[$key]= config::get('lib_mysqli_commands_instance')->escape($value);
}	
?>