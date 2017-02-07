

<?php
/*=== header start ===*/
session_start(); // some values will have to be remembered
require_once('./lib/php/lib_mysqli_commands.php');	// library needed to access database

// init database
config::set('lib_mysqli_interface_instance',new lib_mysqli_interface()); // create instance from class and store reference to this instance in config for reuse
config::set('lib_mysqli_commands_instance',new lib_mysqli_commands()); // create instance from class and store reference to this instance in config for reuse

require_once('./lib/php/lib_security.php');			// will mysql-real-escape all input
require_once('./lib/php/lib_session.php');			// check if user is allowed to access this page
/*=== header ends ===*/

$answer = ""; // feedback for user

/* activate a user's account by verifying user's mail address */
if(isset($_REQUEST["code"]))
{
	$user = config::get('lib_mysqli_commands_instance')->GetUserByActivation($_SESSION["activation"]);
	if(isset($user))
	{
		$user->status = "mail_verified";
		$user = config::get('lib_mysqli_commands_instance')->UserEdit($user); // update the existing user with profilepicture path
		$answer = 'successfully verified your mail! :) Thank you!</a>';
	}
	else
	{
		$answer = "very sorry no user found with this activation code, you might want to contact your admin: ".config::get('mail_admin');
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
						<div class="title button clickable">Activation of your account</div>
						<div class="element_content">
							<div class="column100">
								<p>Thanks for registering and activating.</p>
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
										</div>
										<div class="value">
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