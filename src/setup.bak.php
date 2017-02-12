<?php
/*=== header start ===*/
include('config.php');
session_start(); // some values will have to be remembered
/*=== header ends ===*/

global $answer;
$answer = ""; // feedback for user

if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "delete"))
{
	unlink("setup.php");
	$answer = 'this file you are seeing just got deleted. Please proceed to <a href="register1.php">register your first user :)</a> ';
}
/*
 * write details to config.php
 */
if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "setup"))
{
	// test if all properties required are there
	testIfExists($_REQUEST,'platform_name');
	testIfExists($_REQUEST,'platform_url');
	testIfExists($_REQUEST,'force_ssl');
	testIfExists($_REQUEST,'db_srv_address');
	testIfExists($_REQUEST,'db_name');
	testIfExists($_REQUEST,'db_user');
	testIfExists($_REQUEST,'db_pass');
	testIfExists($_REQUEST,'mail_admin');
	testIfExists($_REQUEST,'login_session_timeout');
	testIfExists($_SESSION,'platform_logo');

	if(empty($answer))
	{
		$fileName = 'config.php';
		file_put_contents($fileName, ""); // empty/truncate config.php
		
		$fh = fopen($fileName, 'w');
		
		if(!$fh)
		{
			$answer = "can't open ".$fileName." file";
		}
		else
		{
$config_text = '<?php
/* project wide settings */
/* this file stores all project-wide settings in an getter-setter way.
	you can access those settings by include "config.php"; and config::get("platform_name"); */

error_reporting(E_ALL); // turn the reporting of php errors on
date_default_timezone_set("UTC");

class config {

	function __construct()
	{
		// nothing here
	}

	private static $config = array();

	public static function set( $key, $value ) {
		self::$config[$key] = $value;
	}

	public static function get( $key ) {
		if( config::isKeySet( $key ) ) {
			return isset( self::$config[$key] ) ? self::$config[$key] : null;
		}
		else
		{
			trigger_error ( "key: ".$key." does not exist in config.php");
		}
	}

	public static function setAll( array $array ) {
		self::$config = $array;
	}

	public static function isKeySet( $key ) {
		return isset( self::$config[ $key ] );
	}
}

// set valuable values

/* ======================= ABOUT THE PLATFORM */
config::set("platform_name"		, "'.$_REQUEST['platform_name'].'");		# name of the platform (may appear in title="" tag
config::set("platform_logo"		, "'.$_SESSION['platform_logo'].'");	# logo of platform
config::set("platform_url"		, "'.$_REQUEST['platform_url'].'"); # base-url of platform
config::set("log_errors"		, "");	# put empty string here if you do not want errors to get logged to file
config::set("log_operations"	, "");					# leave empty string here if you do not want database operations to be logged, per default only errors are logged. you could put log.operations.txt here
config::set("force_ssl"			, '.$_REQUEST['force_ssl'].');				# if users coming via http should be redirected to https (SSL is recommended with or without TOR)

/* ======================= DEVELOPMENT */
config::set("debug_mode",		true);					# if you want additional info about whats going on. will also perserve xdebug ?Session parameters.

/* ======================= DATABASE */
config::set("db_srv_address","'.$_REQUEST['db_srv_address'].'");				# address of database server
config::set("db_datasource","mysql");					# right now can only be "mysql", could be postgress (not implemented) sqlite (not implemented)
config::set("db_name","'.$_REQUEST['db_name'].'");					# the database one will deal with, for conveniance same name as platform
config::set("db_charset","utf8");						# if you want special chars to be properly displayed in the database/phpmyadmin etc.
config::set("db_user","'.$_REQUEST['db_user'].'");							# what database user to use for accessing the database
config::set("db_pass","'.$_REQUEST['db_pass'].'");							# what database password to use for accessing the database
config::set("db_auth_table","passwd"); 					# name of table where platform"s usernames & passwords (md5 hashed) are stored (passwd)
config::set("db_groups_table","groups");				# what the table is called, where the groups are stored (groups)

// will be reset to defaults before every query of database
config::set("db_result",null);							# -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
config::set("db_output",null);							# -> data extracted from RAW mysql result, "the result" ready for further processing, can be any type
config::set("db_log_errors", "./log/errors_db.log");	# put empty string here if you do not want database query errors to be logged
config::set("db_errors_output",true);					# true = output errors as html to browser to screen, false = return them as function values for further processing (json encode -> client -> let client display that stuff)
config::set("db_worked",false);							# -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
config::set("db_last_id","");							# -> if there was an insert, return the auto-generated id of the record inserted.

config::set("answer","");								# if there is any error, output it to the user

/* will hold the link to mysql, as soon as it is initialized like this:
 * config::set("database")["name"] = "MyDataBaseName"; // overwrite settings from config.php
 * $lib_mysqli_commands_instance = new lib_mysqli_commands(); # create instance from class
*/
config::set("lib_mysqli_interface_instance",null);	// actually only handles the buildup of connection and contains only one command: public static function query($query)
config::set("lib_mysqli_commands_instance",null);	// extends interface

/* ======================= WHO IS THE ADMIN? WHO IS RESPONSIBLE? */
config::set("mail_admin", "'.$_REQUEST['mail_admin'].'");		// where notification go
config::set("login_session_timeout", ('.$_REQUEST['login_session_timeout'].')); // 86400*3sec = 3 days, 3600sec = 1h, 1800sec = 30min, 0 = no timeout, amounts of seconds that login-cookies are valid, after login (time until user has to re-login)

/*
 $url = $_SERVER["PHP_SELF"]; // php way to determine the current filename (fails sometimes?)
$filename_and_ending = explode("/", $url);
$filename_and_ending = $filename_and_ending[count($filename_and_ending) - 1];
$filename_and_ending = explode(".", $filename_and_ending);
config::set("current_filename", $filename_and_ending[0]); // automatically load filename.js
*/
?>';

fwrite($fh, $config_text);
fclose($fh);
$answer = 'all settings saves successfully! :) should setup.php now be <a href="config.php?action=delete">deleted? (recommended)</a>';
		}
	}
}

/* test if all required input values have been send if not, abort and output a message */
function testIfExists($array,$property)
{
	global $answer;

	if(isset($array[$property]))
	{
		if(empty($array[$property]))
		{
			$answer = $property." can not be empty.";
		}
	}
	else
	{
		$answer = $property." is missing.";
	}
}

/*
 * register.php
* register new users
*
* requirements for ProfileImage Upload: ensure that "php.ini" is configured to allow file uploads -> file_uploads = On
* PHP script explained:

$target_dir = "uploads/" - specifies the directory where the file is going to be placed
$target_file specifies the path of the file to be uploaded
$uploadOk=1 is not used yet (will be used later)
$imageFileType holds the file extension of the file
Next, check if the image file is an actual image or a fake image (filetype))
*/
if(isset($_FILES["fileToUpload"]))
{
	$target_dir = "images/logo/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"]))
	{
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false)
		{
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		}
		else
		{
			echo "File is not an image."; // will not allow gif
			$uploadOk = 0;
		}
	}
	// Check if file already exists
	if (file_exists($target_file))
	{
		echo "File already exists and will be reused.";
		$uploadOk = 1;
	}
	// Check file size
	$maximum_upload_filesize = getMaximumFileUploadSize();
	if ($_FILES["fileToUpload"]["size"] > $maximum_upload_filesize)
	{
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}

	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
	{
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0)
	{
		echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
	}
	else
	{
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
		{
			$answer = "Awesome Logo! :)";
			$_SESSION['platform_logo'] = $target_file; // save temporarily for later saving into config.php
		}
		else
		{
			echo "Sorry, there was an error uploading your file.";
		}
	}
}

//This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
function convertPHPSizeToBytes($sSize)
{
	if ( is_numeric( $sSize) ) {
		return $sSize;
	}
	$sSuffix = substr($sSize, -1);
	$iValue = substr($sSize, 0, -1);
	switch(strtoupper($sSuffix)){
		case 'P':
			$iValue *= 1024;
		case 'T':
			$iValue *= 1024;
		case 'G':
			$iValue *= 1024;
		case 'M':
			$iValue *= 1024;
		case 'K':
			$iValue *= 1024;
			break;
	}
	return $iValue;
}

function getMaximumFileUploadSize()
{
	return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php
include('text/head.php');
?>
</head>

<body id="body">
	<div id="parent">
		<div class="centered">
			<div id="content">
				<div class="border">
					<div class="element">
						<div class="title button">Setup of <a href="http://HyperHelp.org">HyperHelp.org</a></div>
						<div class="element_content">
							<div class="column100">
								<div class="line">
									<div class="status_error">
										<?php echo $answer; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- end of element -->

					<div class="element">
						<div class="title button">config.php</div>
						<div class="element_content">
		
								<div class="table">

									<!-- form instance logo upload -->
										<div class="column100">
											<div class="line">
												<div class="prop">
													<form action="setup.php" method="post" enctype="multipart/form-data">
														<div class="prop">
															<label for="fileToUpload">
														    	<input name="fileToUpload" id="fileToUpload" type="file">
															</label>
														</div>
												</div>
												<div class="value">
														<div class="value">
													    	<input type="submit" value="Upload Image" name="submit">
														</div>
													</form>
												</div>
											</div>
										</div>
										
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>logo of platform:</label>
													<p>Should be in square format and not larger than 512x512 pixels</p>
												</div>
												<div class="value">
													<?php 
													if(isset($target_file))
													{
														// file was uploaded, display
														$platform_logo = $target_file;
													}
													if(isset($_SESSION['platform_logo']))
													{
														// file was uploaded, display
														$platform_logo = $_SESSION['platform_logo'];
													}
													?>
													<img src="<?php echo $platform_logo; ?>" width="300px">
													<input id="platform_logo" name="platform_logo" type="text" value="<?php echo $platform_logo; ?>" required>
												</div>
											</div>
										</div>

									<!-- form config details -->
									<form action="setup.php">
										<!-- platform_logo -->
										<input name="action" value="setup" hidden>
									
										<div class="column100">
											<div class="line">
												<div class="prop">
													<!-- username -->
													<label>platform_name:</label>
													<p># name of the platform (may appear in title='' tag)</p>
												</div>
												<div class="value">
													<input id="platform_name" name="platform_name" type="text" value="hyperhelp.org" required>
												</div>
											</div>
										</div>
										
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>platform_url</label>
													<p>base-url of platform / domainname.com/path/of/installation </p>
												</div>
												<div class="value">
													<input id="platform_url" name="platform_url" type="text" value="http://hyperhelp.org" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>force_ssl:</label>
													<p>if users coming via http should be redirected to https (<a href="https://www.torproject.org/projects/torbrowser.html.en" target="_blank">TorBundle</a> is recommended to use this platform anonymously, it includes Addons: <a href="https://noscript.net/" target="_blank">NoScript</a> (this platform completely operates without JavaScript, so it will work just fine) and <a href="https://www.eff.org/https-everywhere" target="_blank">SSLAnywhere</a> which will enforce SSL usage. if this is set to false, https is optional (some people do not trust a self-signed certificate)).</p>
												</div>
												<div class="value">
													<input id="force_ssl" name="force_ssl" type="text" value="false" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>db_srv_address:</label>
													<p>address of database server</p>
												</div>
												<div class="value">
													<input id="db_srv_address" name="db_srv_address" type="text" value="localhost" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>db_name:</label>
													<p># the database one will deal with, for conveniance same name as platform</p>
												</div>
												<div class="value">
													<input id="db_name" name="db_name" type="text" value="admin_hyperhelp" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>db_user:</label>
													<p># what database user to use for accessing the database (should be a user that has ONLY access to this single database that this instance needs, SHOULD NOT be root :-D)</p>
												</div>
												<div class="value">
													<input id="db_user" name="db_user" type="text" value="DatabaseUsername" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>db_pass:</label>
													<p># what database password to use for accessing the database</p>
												</div>
												<div class="value">
													<input id="db_pass" name="db_pass" type="text" value="HighlyComplexPassword" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>mail_admin:</label>
													<p># WHO IS THE ADMIN? WHO IS RESPONSIBLE for hosting/running this instance? Also "new-user-has-registered" mails will be send to this Mail-Address.</p>
												</div>
												<div class="value">
													<input id="mail_admin" name="mail_admin" type="text" value="admin@server.org" required>
												</div>
											</div>
										</div>
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>login_session_timeout:</label>
													<p>How long users stay logged in, validity of cookie in seconds. 3600 = 30min, 86400*3sec = 3 days, 36</p>
												</div>
												<div class="value">
													<input id="login_session_timeout" name="login_session_timeout" type="text" value="3600" required>
												</div>
											</div>
										</div>

										<!-- controls -->
										<div class="error_div_register"></div>
										<input class="button" type="submit" value="save config">
										<?php
										
										if(isset($user))
										{
											if(isset($user->id))
											{
												echo '
												<a href="setup.php?action=tidyup">
													<div class="title button clickable">delete this file and go to register a new user</div>
												</a>
												';
											}
										}
										?>
									</form>
								</div>
						</div>
					</div>
					<!-- end of element -->
				</div>
			</div>
		</div>
	</div>
</body>
</html>