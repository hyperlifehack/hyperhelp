<?php
/*=== header start ===*/
require_once('./lib/php/lib_mysqli_commands.php');	// library needed to access database

// init database
config::set('lib_mysqli_interface_instance',new lib_mysqli_interface()); // create instance from class and store reference to this instance in config for reuse
config::set('lib_mysqli_commands_instance',new lib_mysqli_commands()); // create instance from class and store reference to this instance in config for reuse

require_once('./lib/php/lib_security.php');			// will mysql-real-escape all input
require_once('./lib/php/lib_session.php');			// check if user is allowed to access this page
/*=== header ends ===*/

$status = ""; // string if and why login would not work
$logged_in_user = null; // currently logged in user
$valid_login = false;

if(isset($_REQUEST['action']))
{
	/* handle login */
	if($_REQUEST['action'] == "login")
	{
		$users = config::get('lib_mysqli_commands_instance')->users(); // get all users
		
		foreach($users as $key => $value) {
			if($value->username == $_REQUEST['username'])
			{
				$logged_in_user = $value;
				break;
			}
		}
		
		if(!empty($users))
		{
			if($logged_in_user)
			{
				// at this point we know the username exists
				// let's compare the submitted password to value of the array key (the right password)
				if(password_compare($_REQUEST['password'],$logged_in_user->password)) // check if username with that password exists
				{
					$valid_login = true;
					// password is correct
					config::get('lib_mysqli_commands_instance')->SetCookie($_REQUEST['username']);
	
					if(config::get('login_session_timeout') > 0)
					{
						$expires = HumanReadableSeconds(config::get('login_session_timeout'));
						$status = "login "."success "."success "."you have now access. live long and prosper! Login expires in ".$expires;
					}
					else
					{
						$status = "login "."failed "."failed "."session expired please login again.";
					}
				}
				else
				{
					$valid_login = false;
					$status = "login "."failed "."failed "."wrong username or password.";
				}
			}
			else
			{
				$valid_login = false;
				$status = "login "."failed "."failed "."wrong username or password.";
			}
		}
		else
		{
			$valid_login = false;
			$status = "login "."failed "."failed "."list of users is empty.";
		}
	}
}

/* wandelt ein datum wie 30.12.2016 in eine TimeStamp um (ms since 1970) */
function parse_date2timestamp($datum)
{
	$parsed = date_parse_from_format("d.m.Y", $datum);
	$TimeStamp = mktime(
			0, // $parsed['hour'],
			0, // $parsed['minute'],
			0, // $parsed['second'],
			$parsed['month'],
			$parsed['day'],
			$parsed['year']
	);
	
	$test = date('d/m/Y', $TimeStamp); // just for checking
	
	return $TimeStamp; //returns: 1483052400
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php
include('text/head.php');

// if logged in, redirect to capture
if($valid_login)
{
	echo '<meta http-equiv="refresh" content="3;URL=capture.php"/>';
}
?>
</head>
<body id="body">
	<div id="parent">
		<div class="centered">
			<div id="headline" class="boxShadows">
				<h1>
					<div id="headline_text"><a href="http<?php if(config::get('force_ssl')) echo "s" ?>://<?php echo config::get('platform_name'); ?>"><?php echo config::get('platform_name'); ?></a></div>
				</h1>
				<p>Thank you - for your Commitment to your Community! :)</p>
				<p><a href="https://github.com/hyperlifehack/hyperhelp.org" target="_blank">(100% Transparent! Download OpenSource here!)</a></p>
			</div>

			<!-- login -->
			<div class="element">
				<div class="title"><h2>login:</h2></div>
				<div class="element_content">
					<form action="index.php#login">
						<input name="action" value="login" hidden>
						<div class="table">
							<div class="column100">
								<div class="line">
									<img class="profilepicture" src="<?php
									if($valid_login)
									{
										if(isset($logged_in_user->profilepicture))
										{
											echo $logged_in_user->profilepicture;
										}
									}
									?>" alt="profile Picture">
								</div>
								<div class="line">
									<div class="prop">
										usr:
									</div>
									<div class="value">
										<input id="wer_username" name="username" type="username" placeholder="username" value="<?php remember_value("username") ?>">
									</div>
								</div>
								<div class="line">
									<div class="prop">
										pwd:
									</div>
									<div class="value">
										<input id="wer_password" name="password" type="password" placeholder="passwort" value="<?php remember_value("password") ?>">
									</div>
								</div>
								<div class="line">
									<div class="prop">
										<div id="status_login" class="status_error">status:</div>
									</div>
									<div class="value">
										<div id="status" class="status"><?php if(empty($status)) { echo "status"; } else { echo $status; } ?></div>
									</div>
								</div>
							</div>
						</div>

						<div class="column100">
							<input type="submit" name="login" value="login" class="button clickable" style="width: 100%; height: 40px;"/>
						</div>
					</form>
					<div class="column100">
						<a href="register1.php">
							<div class="button_div clickable" style="width: 100%;"><p>new here? -> register</p></div>
						</a>
					</div>
				</div>
			</div>
			<!-- end of element -->
					
			<div id="content">
				<div class="border">
					<div class="element">
						<div id="headline" style="margin-top: 0px;" class="boxShadows" title="for security: works without Javascript :-D">
							<h2>
								<div id="headline_text">about:</div>
							</h2>
						</div>
								
						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
										<img src="images/youtube.jpg" style="max-width: 100%;"/>
										<!-- <iframe width="100%" height="400px" src="https://www.youtube.com/embed/lJy_tAm9IJQ" frameborder="0" allowfullscreen></iframe>  -->
										<p><a href="https://<?php echo config::get('platform_name'); ?>"><?php echo config::get('platform_name'); ?></a> wants to offer a platform for cooperation.</p>
										<p>You can do book-keeping of your commitment to your community, mankind, the planet and make it transparent to your community - so it can be valued :)</p>
										<strong><p>German:</p></strong>
										<p>Hier kannst Du dein Engagement erfassen, <br> damit es gewertschätzt und nicht vergessen wird :)</p>
									</div>
								</div>
							</div>
						</div>

						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
										<div id="headline" style="margin-top: 0px;" class="boxShadows" title="for security: works without Javascript :-D">
											<h2>
												<div id="headline_text">For Your Privacy & Security:</div>
											</h2>
										</div>
										<p><a href="https://www.torproject.org/projects/torbrowser.html.en" target="_blank">TorBundle</a> is recommended to use this platform anonymously, it includes Addons: <a href="https://noscript.net/" target="_blank">NoScript</a> (this platform completely operates without JavaScript, so it will work just fine) and <a href="https://www.eff.org/https-everywhere" target="_blank">SSLAnywhere</a> which will enforce SSL usage.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- end of element -->

					<div class="element">
						<div class="title button clickable">Users Toplist:</div>
						<div class="element_content">
							<div class="table">
								<?php 
								// 1. get a list of all users
								// 2. calculate their total captured units
								// 3. user gets a star for every hour donated
								// 3.1. white star for every hour, blue star for every day, red star for every month
								$users = config::get('lib_mysqli_commands_instance')->users(); // get all users as array

								foreach($users as $key => $user)
								{
									$query = "SELECT SUM( `howmany_minutes` ) AS units_total FROM `actions` WHERE username = '".$user->username."';";
									$units = config::get('lib_mysqli_interface_instance')->query($query); // $units usually minutes
									$units = GetFirstElementOfArray($units);
									$units_total = $units->units_total;

									if(isset($units_total))
									{
										$hours = round($units_total / 60);
										$days = round($hours / 24);
										$months = round($days / 30);
	echo '
										<div class="column100">
											<div class="line">
												<div class="prop">
													'.$user->username.'
												</div>
												<div class="value" title="one star for every hour donated">';
													for($i=0; $i<$months; $i++)
													{
														echo '<img src="images/star_red.png" />';
													}
													for($i=0; $i< $days; $i++)
													{
														echo '<img src="images/star_blue.png" />';
													}
													for($i=0; $i<$hours; $i++)
													{
														echo '<img src="images/star_white.png" />';
													}
		echo '
												</div>
											</div>
										</div>
	';
									}
								}
								?>
							</div>
						</div>
					</div>
					<!-- end of element -->

					<?php
					include('text/SharingButtons.php');
					?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>