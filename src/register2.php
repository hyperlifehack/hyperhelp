<?php
/*=== header start ===*/
session_start(); // some values will have to be remembered
require_once('./lib/php/lib_mysqli_commands.php');	// library needed to access database

// init database
config::set('lib_mysqli_interface_instance',new lib_mysqli_interface()); // create instance from class and store reference to this instance in config for reuse
config::set('lib_mysqli_commands_instance',new lib_mysqli_commands()); // create instance from class and store reference to this instance in config for reuse

require_once('./lib/php/lib_security.php');			// will mysql-real-escape all input
require_once('./lib/php/lib_session.php');			// check if user is allowed to access this page

$redirect = false;

/*=== header ends ===*/

$answer = ""; // feedback for user
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
if(isset($_REQUEST["SetProfilePicture"]))
{
	$user = config::get('lib_mysqli_commands_instance')->GetUserByActivation($_SESSION["activation"]);
	if(isset($user))
	{
		if(($user->id == $_SESSION["UserID"]) && ($user->activation == $_SESSION["activation"]) && ($user->RandomID == $_SESSION["RandomID"]))
		{
			$user->profilepicture = $_REQUEST["SetProfilePicture"];
			$target_file = $_REQUEST["SetProfilePicture"]; // sync

			$user = config::get('lib_mysqli_commands_instance')->UserEdit($user); // update the existing user with profilepicture path
			$answer = 'success! :) <br> pleace check your mail! <br> You will be redirected to <a href="index.php">login!</a>';

			$redirect = true; // redirect to index.php (login)
		}
		else
		{
			$answer = "very sorry something went wrong, you might want to contact your admin: ".config::get('mail_admin');
		}
	}
	else
	{
		$answer = "very sorry something went wrong, you might want to contact your admin: ".config::get('mail_admin');
	}
}

if(isset($_FILES["fileToUpload"]))
{
	$target_dir = "images/profilepictures/";
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
		echo "File already exists, use that as your profile picture?";
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
			echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			$answer = "Awesome ProfilePic! :)";
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

if($redirect)
{
	echo '<meta http-equiv="refresh" content="5;URL=index.php"/>';
}
?>
</head>

<body id="body">
	<div id="parent">
		<div class="centered">
			<div id="content">
				<div class="border">
					<div class="element">
						<div class="title button clickable">Register New User: Upload a picture if you want</div>
						<div class="element_content">
							<div class="column100">
								<p>Please upload a nice profile picture! (does not have to be you :-D)</p>
								<p>It should be not insulting violent or otherwise respect-less.</p>
							</div>
						</div>
					</div>
					<!-- end of element -->

					<div class="element">
						<div class="title button clickable"><a name="capture" href="#capture">ProfilePicture:</a></div>
						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
										<div class="prop">
											<form action="register2.php" method="post" enctype="multipart/form-data">
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
										<img src="<?php if(isset($target_file)) echo $target_file ?>" width="300px">
									</div>
								</div>
								<div class="column100">
									<div class="line">
										<div class="prop">
										feedback:
										</div>
										<div class="value">
												<div class="value">
											    	<div class="status_error"><?php echo $answer; ?></div>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="column100">
									<div class="line">
										<a href="register2.php?SetProfilePicture=<?php if(isset($target_file)) echo $target_file ?>">
											<div class="title button clickable">set this as my ProfilePicture</div>
										</a>
									</div>
								</div>
								<div class="column100">
									<div class="line">
									<div class="line">
										<a href="login.php">
											<div class="title button clickable">skip this process and go straight to login</div>
										</a>
									</div>
								</div>
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