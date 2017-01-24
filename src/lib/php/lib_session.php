<?php
/* check if a user is allowed to access this page (cookie-hash matches database and has not expired)
 * This can now be included at the top of any file that needs protecting (before any other HTML/PHP content)
 */
// 1. check for valid session
/* will check if a cookie is set and
 * if yes, if the session is still valid,
 * if yes, return currently logged in user object,
 * else return null */
function session_valid()
{
	// 1. check for valid session
	if(isset($_COOKIE["hyperhelp"]))
	{
		$user = config::get('lib_mysqli_commands_instance')->GetUserBySession($_COOKIE["hyperhelp"]);
	
		$valid_until = $user->loginexpires;
	
		$now = time();
	
		if($now < $valid_until)
		{
			return $user;
		}
		else
		{
			return null;
			logout();
		}
	}
	else
	{
		return null;
		logout();
	}
}

/* logout a user and redirect to logout page */
function logout()
{
	// log em out
	$_COOKIE["hyperhelp"] = ""; // not using sessions but well...

	setcookie("hyperhelp", "", 1); // delete cookie by overwriting it

	header("Location: logout.php");
}

?>