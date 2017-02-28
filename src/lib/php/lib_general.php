<?php
require_once('lib_array_and_objects.php');
require_once('lib_convert.php');

/* search and include the missing lib - test if relative or absolute paths are necessary to include the lib */
/*
function include_missing_lib($lib_name)
{
	$current_working_directory = getcwd();
	
	if(file_exists ($lib_name))
	{
		require_once ($lib_name);
	}
	else
	{
		if (file_exists ('../../'.$lib_name))
		{
			require_once ('../../'.$lib_name);
		}
		else
		{
			if (file_exists ('./lib/php/'.$lib_name))
			{
				require_once ('./lib/php/'.$lib_name);
			}
			else
			{
				trigger_error ( basename ( __FILE__, '.php' ) . "-> could not include library ".$lib_name.", it should be on top of every file.php", E_USER_ERROR );
			}
		}
	} // include lib_general.php
}
*/

/* just very general functions, is included via config.php
 * so that it is available to all files */

/* when a group of checkboxes is transmitted via form
 * name="checkbox_group_root"
 * name="checkbox_group_users"
 * ... one wants to extract all this group info into one array/object
 */
function GetREQUESTSstarting($with)
{
	$result = array();
	$count = strlen($with);
	foreach ($_REQUEST as $key => $value)
	{
		$substring = substr($key, 0, $count);
		if($substring == $with)
		{
			$result[$key] = $value;
		}
	}
	
	return $result;
}

/* generate password-hash by applying sha256 multiple times
 * 
 * http://stackoverflow.com/questions/21711890/how-to-implement-sha-512-md5-and-salt-encryption-all-for-one-password
 * */
function password_encrypt($password)
{
		$salt = salt();

		// how many times the string will be hashed
		$rounds = 1000;

		// pass in the password, the number of rounds, and the salt
		// $5$ specifies SHA256-CRYPT, use $6$ if you really want SHA512
		$password_encrypted = crypt($password, sprintf('$5$rounds=%d$%s$', $rounds, $salt));
	
		return $password_encrypted;
}

/* Compare an existing password
 * 
 * takes in $password and $hash and test if $password->encryption->$hash, if yes, return true, if not return false
 * 
 * creditz: http://stackoverflow.com/questions/21711890/how-to-implement-sha-512-md5-and-salt-encryption-all-for-one-password */

function password_compare($password,$hash)
{
	// extract the hashing method, number of rounds, and salt from the stored hash
	// and hash the password string accordingly
	$parts = explode('$', $hash);

	$test_hash = crypt($password, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3]));
	
	if($hash == $test_hash)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/* generate as random as possible 16-character string */
function salt()
{
	// generate a 16-character salt string
	$salt = substr(str_replace('+','_',base64_encode(md5(mt_rand(), true))),0,16);
	$salt = str_replace('/','_',$salt);
	$salt = str_replace('\\','_',$salt);
	return $salt;
}

/* for security reasons, one tries to be as random as possible here */
function generate_activation_key()
{
	# sha512 encode the password, only works with PHP Version 5.3+X, for older versions use: hash('sha512', $user->password);
	$activation_key = salt();

	return $activation_key;
}

/* generate a password and md5 hash it */
function generatePassword($length = 8) {
	
	$result = "";

    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    
    return $result;
}

/* outputs a warning and if config::get('log_errors') == true, outputs to error.log
 * 
 * $message = string that will be output to the client
 * $type = can be	fatal (default)	= error message will be print and program will be interrupted, no further processing 
 * 					warning			= error message will be print and program will continue
 * */
function error($message,$type = "fatal")
{
	$message = "error_type: ".$type.", message: ".$message;
	
	if(config::get("db_errors_output") == "json")
	{
		answer(null,"register","error","error",$message);
	}
	else
	{
		trigger_error($message);
	}

	$log_errors = config::get('log_errors');
	if(!empty($log_errors))
	{
		log2file(config::get('log_errors'),$message);
	}

	if($type == "fatal")
	{
		exit; // exit program, end of processing
	}
	
	return $message;
}

/* outputs a warning and if config::get('log_errors') == true, outputs to error.log */
function operation($operation)
{
	$log_operations = config::get('log_operations');
	if(!empty($log_operations))
	{
		log2file(config::get('log_operations'),$operation);
	}
}

/* write the error to a log file */
function log2file($file,$this)
{
	$line = time().": ".$this."\n";
	$cwd = getcwd();
	file_put_contents($file, $line, FILE_APPEND);
}

/* remember what was entered in the form, as session */
function remember_value($value)
{
	if (isset ( $_REQUEST [$value] ))
	{
		$_SESSION[$value] = $_REQUEST[$value];
		echo $_REQUEST[$value];
	}
	else
	{
		// if value was entered previously
		if(isset($_SESSION[$value])) echo $_SESSION[$value];
	}
}
