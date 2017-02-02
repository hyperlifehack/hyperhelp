<?php
/*=== header start ===*/
require_once('./lib/php/lib_mysqli_commands.php');	// library needed to access database

// init database
config::set('lib_mysqli_interface_instance',new lib_mysqli_interface()); // create instance from class and store reference to this instance in config for reuse
config::set('lib_mysqli_commands_instance',new lib_mysqli_commands()); // create instance from class and store reference to this instance in config for reuse

require_once('./lib/php/lib_security.php');			// will mysql-real-escape all input
require_once('./lib/php/lib_session.php');			// check if user is allowed to access this page
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
if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "register"))
{
	$user = config::get('lib_mysqli_commands_instance')->NewUser();
	$user->username = $_REQUEST["username"];
	$user->profilepicture = $_REQUEST["profilepicture"];
	$user->mail = $_REQUEST["mail_address"];
	$user->password = $_REQUEST["password"];

	if(config::get('lib_mysqli_commands_instance')->UserExist($user,"username"))
	{
		$answer = "register"." error"." error"." very sorry the username \"".$user->username."\" is already taken :( please try a different one.";
	}
	else
	{
		$user = config::get('lib_mysqli_commands_instance')->UserAdd($user); // returns the user-object from database, containing a new, database generated id, that is important for editing/deleting the user later
		$answer = "register"." success"." success".' registration success! :) pleace check your mail, then <a href="index.php">login!</a>';
	}
}

if(isset($_REQUEST["check_username_taken"]))
{
	include_once("./lib/php/lib_mysqli_commands.php");
	$user = config::get('lib_mysqli_commands_instance')->NewUser();
	$user->username = $_REQUEST["check_username_taken"];
	if(config::get('lib_mysqli_commands_instance')->UserExist($user,"username"))
	{
		$answer = "check_username_taken"." error"." error"." very sorry the username \"".$user->username."\" is already taken :( please try a different one.";
	}
	else
	{
		$answer = "check_username_taken"." success"." success"." username is available";
	}
}

if(isset($_FILES["fileToUpload"]))
{
	$target_dir = "images/profilepictures/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image."; // will not allow gif
			$uploadOk = 0;
		}
	}
	// Check if file already exists
	if (file_exists($target_file)) {
		echo "Sorry, file already exists.";
		$uploadOk = 0;
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 500000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
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
						<div class="title button clickable">Register New User:</div>
						<div class="element_content">
							<div class="column100">
								<p>To register a valid e-mail is enough.
								So you do not have to give your real name (choose nickname).
								
								But please upload a nice profile picture! (does not have to be you :-D)</p>
								
								<form action="register.php" method="post" enctype="multipart/form-data">
									<div class="prop">
										<label for="fileToUpload">
									    	<input name="fileToUpload" id="fileToUpload" type="file">
										</label>
									</div>
									<div class="value">
								    	<input type="submit" value="Upload Image" name="submit">
									</div>
								</form>
							</div>
						</div>
					</div>
					<!-- end of element -->

					<div class="element">
						<div class="title button clickable"><a name="capture" href="#capture">User Data:</a></div>
						<div class="element_content">
		
								<div class="table">
									<!-- user add/edit form -->
									<form class="form-register" action="register.php">
										<!-- profilepicture -->
										<input name="action" value="register" hidden>
									
										<div class="column100">
											<div class="line">
												<img class="profilepicture" src="<?php if(isset($target_file)){ echo $target_file; } ?>" alt="profile Picture">
												<input id="profilepicture" name="profilepicture" type="text" value="<?php if(isset($target_file)){ echo $target_file; } ?>" hidden>
											</div>
										</div>
										
										<div class="column100">
											<div class="line">
												<div class="prop">
													<!-- username -->
													<label>UserName:</label>
												</div>
												<div class="value">
													<input id="username" name="username" type="text" required>
													<div class="error_div_username"></div>
												</div>
											</div>
										</div>
										
										<div class="column100">
											<div class="line">
												<div class="prop">
													<label>Mail:</label>
												</div>
												<div class="value">
													<input id="mail_address" name="mail_address" type="text" required>
												</div>
											</div>
										</div>

										<div class="column100">
											<div class="line">
												<div class="prop">
													<!-- password -->
													<label>Password:</label>
												</div>
												<div class="value">
													<input id="password" name="password" placeholder="password" title="something wrong here" data-placement="bottom" type="password" required>
												</div>
											</div>
										</div>
										
										<div class="column100">
											<div class="line">
												<div class="prop">
													<!-- password check -->
													<div class="error_div_passwords"></div>
												</div>
												<div class="value">
													<input id="check_password" name="check_password" placeholder="password Again" title="something wrong here" data-placement="bottom" type="password" required>
												</div>
											</div>
										</div>
										<!-- controls -->
										<div class="error_div_register"></div>
										<input class="button" type="submit" value="register">
									</form>
									
									<div class="column100">
										<div class="line">
											<div class="status_error">
												<?php echo $answer; ?>
											</div>
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