<?php
session_start(); // some values will have to be remembered
/*=== header start ===*/
require_once('./lib/php/lib_mysqli_commands.php');	// library needed to access database

// init database
config::set('lib_mysqli_interface_instance',new lib_mysqli_interface()); // create instance from class and store reference to this instance in config for reuse
config::set('lib_mysqli_commands_instance',new lib_mysqli_commands()); // create instance from class and store reference to this instance in config for reuse

require_once('./lib/php/lib_security.php');			// will mysql-real-escape all input
require_once('./lib/php/lib_session.php');			// check if user is allowed to access this page
/*=== header ends ===*/

$logged_in_user = session_valid(); // currently logged in user
$status = ""; // string if and why login would not work
$refresh_page = false; // if page should be refreshed or not

if($logged_in_user)
{
	if(isset($_REQUEST['action']))
	{
		/* neuen eintrag erfassen */
		if($_REQUEST['action'] == "capture")
		{
			$NewRecord = config::get('lib_mysqli_commands_instance')->newRecord("records");
			
			// RecordAdd - add a arbitrary record to a arbitrary table
			$NewRecord->username = $_REQUEST["username"];
	
			$date_and_time = $_REQUEST["when_date"]." ".$_REQUEST["when_time"];
			
			$when = parse_date2timestamp($date_and_time);
			$NewRecord->when = $when;
	
			$NewRecord->howmany_minutes = $_REQUEST["howmany_minutes"];
			$NewRecord->to_whom = $_REQUEST["to_whom"];
			$NewRecord->what = $_REQUEST["what"];
			$NewRecord = config::get('lib_mysqli_commands_instance')->RecordAdd("records",$NewRecord); // returns the record-object from database, containing a new, database generated id, that is important for editing/deleting the record later
			$test = config::get('lib_mysqli_interface_instance')->get('last_id'); // get id of last inserted record // DOES THIS REALLY WORK?
			$NewRecord->id = config::get('lib_mysqli_interface_instance')->get('last_id'); // get id of last inserted record
			
			// store what as template
			if(isset($_REQUEST['store_what_as_template']))
			{
				// test if template exists
				$NewAction = config::get('lib_mysqli_commands_instance')->newRecord("actions");
				$NewAction->keyword = $_REQUEST['what'];
				
				$ActionExists = config::get('lib_mysqli_commands_instance')->records("actions",$NewAction,"keyword");
				if($ActionExists)
				{
					// check if user is not allready associated with this action
					if (strpos($ActionExists->users, $logged_in_user->username) !== false)
					{
						// do nothing, user is allready associated with this action
					}
					else
					{
						// add user to the list of users that use this action
						$ActionExists->users = $ActionExists->users.$logged_in_user->username.",";
						$ActionExists_id = config::get('lib_mysqli_commands_instance')->RecordEdit("actions",$ActionExists);
					}
				}
				else
				{
					// add action to database
					$NewAction->description = $_REQUEST['what'];
					$NewAction->users = $logged_in_user->username.",";
					$NewAction = config::get('lib_mysqli_commands_instance')->RecordAdd("actions",$NewAction); // returns the record-object from database, containing a new, database generated id, that is important for editing/deleting the record later
				}
			}

			if(lib_mysqli_commands::get('worked',true))
			{
				$status = "thank you for your hard work and dedication! :) your working-time have been recorded with the recordID: ".$NewRecord->id."<br> Page will refresh in 3 sec...";
				$refresh_page = true;
			}
		}
	}
}

/* wandelt ein datum wie 30.12.2016 in eine TimeStamp um (ms since 1970) */
function parse_date2timestamp($datum)
{
	$parsed = date_parse_from_format("Y.m.d H:i", $datum);
	
	// $date = new DateTime($parsed['year']."-".$parsed['month']."-".$parsed['day']);
			
	/* not accurate ? */
	$TimeStamp = mktime(
			$parsed['minute'],
			$parsed['hour'],
			0, // $parsed['second'],
			$parsed['month'],
			$parsed['day'],
			$parsed['year']
	);
	
	$test = date('d.m.Y H:i', $TimeStamp); // just for checking
	
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
// if a record was created reload the page
if($refresh_page)
{
	echo '<meta http-equiv="refresh" content="3;URL=capture.php"/>';
}
?>
</head>

<body id="body">
	<div id="parent">
		<div class="centered">
			<?php include('text/headline.php'); ?>
			<div id="content">
				<div class="border">
				<!--  
					<div class="element">
						<div class="title button clickable">ErklärVideo</div>
						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
										<img src="images/youtube.jpg" style="max-width: 100%;"/>
										<iframe width="100%" height="400px" src="https://www.youtube.com/embed/lJy_tAm9IJQ" frameborder="0" allowfullscreen></iframe>
									</div>
								</div>
							</div>
						</div>
					</div>
				-->
					<!-- end of element -->

					<div class="element">
						<div class="title button clickable"><a name="capture" href="#capture">capture action</a></div>
						<div class="element_content">
							<form class="form-signin" action="capture.php#capture">
								<input name="action" value="capture" hidden>
								<h2>Who? (You)</h2>
								<a href="logout.php"><h2>(logout)</h2></a>
								<h1>
								<?php
									if($logged_in_user)
									{
										echo $logged_in_user->username;
									}
								?>
								</h1>
								<div class="table">
									<div class="column100">
										<div class="line">
											<img class="profilepicture" src="<?php if($logged_in_user && isset($logged_in_user->profilepicture)){ echo $logged_in_user->profilepicture; } ?>" alt="profile Picture">
										</div>
										<div class="line">
											<div class="prop">
												usr:
											</div>
											<div class="value">
												<input id="who_username" name="username" type="username" placeholder="username" value="<?php if($logged_in_user) echo $logged_in_user->username; ?>" readonly>
											</div>
										</div>
									</div>
								</div>

								<h2>howmuch?</h2>

								<div class="table">
									<div class="column100">
										<div class="line">
											<div class="prop">
												<div>
												Minutes:
												</div>
												<a href="capture.php?howmany_minutes=10#capture"><div class="element_select">10</div></a>
												<a href="capture.php?howmany_minutes=20#capture"><div class="element_select">20</div></a>
												<a href="capture.php?howmany_minutes=30#capture"><div class="element_select">30</div></a>
												<a href="capture.php?howmany_minutes=45#capture"><div class="element_select">45</div></a>
												<a href="capture.php?howmany_minutes=60#capture"><div class="element_select">60</div></a>
												<a href="capture.php?howmany_minutes=120#capture"><div class="element_select">120</div></a>
											</div>
											<div class="value">
												<div id="minuten">
													<input name="howmany_minutes" id="howmany_minutes" value="<?php remember_value("howmany_minutes") ?>" type="text" placeholder="10">
												</div>
											</div>
										</div>
									</div>
								</div>

								<h2 title="previously often helped users">to whom?</h2>

								<div class="table">
									<div class="column100">
										<div class="line">
											<div id="list_of_usernames" class="column100" style="text-align: center;">
												<?php
												$users = config::get('lib_mysqli_commands_instance')->users(); // get all users
												
												foreach($users as $key => $user) {
													echo '<a href="capture.php?to_whom_select='.$user->username.'#capture"><div class="element_select">'.$user->username.'</div></a>'; 
												}
												
												?>
											</div>
										<div class="line">
											<input name="to_whom" id="to_whom" placeholder="username" value="<?php remember_value("to_whom_select") ?>" type="text" style="width: 100%;"/>
										</div>
									</div>
								</div>
								
								<h2 title="What task did you help your neighbour / the otheruser with?">what?</h2>

								<div class="table">
									<div class="column100">
										<div class="line">
											<input name="what" id="what" placeholder="what?" value="<?php remember_value("what_select") ?>" type="text" style="width: 100%;"/>
											<input type="checkbox" name="store_what_as_template" value="true">store as template?<br>
											<div id="status"><?php
												if(isset($ActionExists_id))
												{
													echo "Action (id:".$ActionExists_id.") has been added to your Templates"; 
												}
												else if(isset($NewAction->id))
												{
													echo "The Action-Template has been saved under the id:".$NewAction->id; 
												}
											?></div>
										</div>
										<div class="line">
											<div id="list_of_actions" class="column100" style="text-align: center;">
												<?php
												if($logged_in_user)
												{
													// show all templates from all users
													$Actions_of_that_User = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `".config::get("db_name")."`.`actions`;");
													// uncomment this to only see the templates associated with the current logged in user
													// $Actions_of_that_User = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `".config::get("db_name")."`.`actions` WHERE `users` LIKE '%".$logged_in_user->username."%';");
													foreach($Actions_of_that_User as $key => $action) {
														echo '<a href="capture.php?what_select='.$action->keyword.'#capture"><div class="element_select">'.$action->keyword.'</div></a>'; 
													}
												}
												?>
											</div>
										</div>
									</div>
								</div>

								<h2 title="When did help your neighbour / the otheruser?">When?</h2>

								<div class="table">
										<div class="line">
											<div class="prop">
												Am:
											</div>
											<div class="value">
											<?php 
											// today
											$current_date = date("Y.m.d");	// 20010310
											$current_time = date("H:i");	// 17:16
											?>
												<input id="when_date" name="when_date" type="text" placeholder="dd.mm.yyyy" value="<?php echo $current_date; ?>"/>
												
												<input id="when_time" name="when_time" type="text" placeholder="hh:mm" value="<?php echo $current_time; ?>"/>
											</div>
										</div>
								</div>
								<div class="column100">
									<input type="submit" name="erfassen" value="Erfassen" class="button" style="width: 100%; height: 40px;"/>
									<div id="status"><?php echo $status; ?></div>
								</div>
							</form>
						</div>
					</div>
					<!-- end of element -->

					<div class="element">
						<div class="title button clickable">Sharing is Caring :)</div>
						<div class="element_content">
							<div class="shariff shariff-main"
								data-services="facebook%7Ctwitter%7Cgoogleplus"
								data-url="http%3A%2F%2Fhyperhelp.org%2F"
								data-timestamp="1475015147"
								data-backendurl="http://hyperhelp.org/wp-json/shariff/v1/share_counts?">
								<ul
									class="shariff-buttons theme-default orientation-horizontal buttonsize-medium">
									<li class="shariff-button mailto"
										style="background-color: #a8a8a8"><a
										href="mailto:?body=http%3A%2F%2Fhyperhelp.org%2F&amp;subject=hyperhelp.org"
										title="Send by email" aria-label="Send by email"
										role="button" rel="noopener nofollow" class="shariff-link"
										style="background-color: #999; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
													<path
														d="M32 12.7v14.2q0 1.2-0.8 2t-2 0.9h-26.3q-1.2 0-2-0.9t-0.8-2v-14.2q0.8 0.9 1.8 1.6 6.5 4.4 8.9 6.1 1 0.8 1.6 1.2t1.7 0.9 2 0.4h0.1q0.9 0 2-0.4t1.7-0.9 1.6-1.2q3-2.2 8.9-6.1 1-0.7 1.8-1.6zM32 7.4q0 1.4-0.9 2.7t-2.2 2.2q-6.7 4.7-8.4 5.8-0.2 0.1-0.7 0.5t-1 0.7-0.9 0.6-1.1 0.5-0.9 0.2h-0.1q-0.4 0-0.9-0.2t-1.1-0.5-0.9-0.6-1-0.7-0.7-0.5q-1.6-1.1-4.7-3.2t-3.6-2.6q-1.1-0.7-2.1-2t-1-2.5q0-1.4 0.7-2.3t2.1-0.9h26.3q1.2 0 2 0.8t0.9 2z"></path></svg></span><span
											class="shariff-text">e-mail</span>&nbsp;</a></li>
									<li class="shariff-button diaspora"
										style="background-color: #b3b3b3"><a
										href="https://share.diasporafoundation.org/?url=http%3A%2F%2Fhyperhelp.org%2F&amp;title=hyperhelp.org"
										title="Share on Diaspora" aria-label="Share on Diaspora"
										role="button" rel="noopener nofollow" class="shariff-link"
										target="_blank" style="background-color: #999; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 33 32">
													<path
														d="M20.6 28.2c-0.8-1.2-2.1-2.9-2.9-4-0.8-1.1-1.4-1.9-1.4-1.9s-1.2 1.6-2.8 3.8c-1.5 2.1-2.8 3.8-2.8 3.8 0 0-5.5-3.9-5.5-3.9 0 0 1.2-1.8 2.8-4s2.8-4 2.8-4.1c0-0.1-0.5-0.2-4.4-1.5-2.4-0.8-4.4-1.5-4.4-1.5 0 0 0.2-0.8 1-3.2 0.6-1.8 1-3.2 1.1-3.3s2.1 0.6 4.6 1.5c2.5 0.8 4.6 1.5 4.6 1.5s0.1 0 0.1-0.1c0 0 0-2.2 0-4.8s0-4.7 0.1-4.7c0 0 0.7 0 3.3 0 1.8 0 3.3 0 3.4 0 0 0 0.1 1.4 0.2 4.6 0.1 5.2 0.1 5.3 0.2 5.3 0 0 2-0.7 4.5-1.5s4.4-1.5 4.4-1.5c0 0.1 2 6.5 2 6.5 0 0-2 0.7-4.5 1.5-3.4 1.1-4.5 1.5-4.5 1.6 0 0 1.2 1.8 2.6 3.9 1.5 2.1 2.6 3.9 2.6 3.9 0 0-5.4 4-5.5 4 0 0-0.7-0.9-1.5-2.1z"></path></svg></span><span
											class="shariff-text">share</span>&nbsp;</a></li>
									<li class="shariff-button facebook"
										style="background-color: #4273c8"><a
										href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fhyperhelp.org%2F"
										title="Share on Facebook" aria-label="Share on Facebook"
										role="button" rel="noopener nofollow" class="shariff-link"
										target="_blank"
										style="background-color: #3b5998; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 32">
													<path
														d="M17.1 0.2v4.7h-2.8q-1.5 0-2.1 0.6t-0.5 1.9v3.4h5.2l-0.7 5.3h-4.5v13.6h-5.5v-13.6h-4.5v-5.3h4.5v-3.9q0-3.3 1.9-5.2t5-1.8q2.6 0 4.1 0.2z"></path></svg></span><span
											class="shariff-text">share</span>&nbsp;</a></li>
									<li class="shariff-button twitter"
										style="background-color: #32bbf5"><a
										href="https://twitter.com/share?url=http%3A%2F%2Fhyperhelp.org%2F&amp;text=hyperhelp.org"
										title="Share on Twitter" aria-label="Share on Twitter"
										role="button" rel="noopener nofollow" class="shariff-link"
										target="_blank"
										style="background-color: #55acee; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 32">
													<path
														d="M29.7 6.8q-1.2 1.8-3 3.1 0 0.3 0 0.8 0 2.5-0.7 4.9t-2.2 4.7-3.5 4-4.9 2.8-6.1 1q-5.1 0-9.3-2.7 0.6 0.1 1.5 0.1 4.3 0 7.6-2.6-2-0.1-3.5-1.2t-2.2-3q0.6 0.1 1.1 0.1 0.8 0 1.6-0.2-2.1-0.4-3.5-2.1t-1.4-3.9v-0.1q1.3 0.7 2.8 0.8-1.2-0.8-2-2.2t-0.7-2.9q0-1.7 0.8-3.1 2.3 2.8 5.5 4.5t7 1.9q-0.2-0.7-0.2-1.4 0-2.5 1.8-4.3t4.3-1.8q2.7 0 4.5 1.9 2.1-0.4 3.9-1.5-0.7 2.2-2.7 3.4 1.8-0.2 3.5-0.9z"></path></svg></span><span
											class="shariff-text">tweet</span>&nbsp;</a></li>
									<li class="shariff-button googleplus"
										style="background-color: #f75b44"><a
										href="https://plus.google.com/share?url=http%3A%2F%2Fhyperhelp.org%2F"
										title="Share on Google+" aria-label="Share on Google+"
										role="button" rel="noopener nofollow" class="shariff-link"
										target="_blank"
										style="background-color: #d34836; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
													<path
														d="M31.6 14.7h-3.3v-3.3h-2.6v3.3h-3.3v2.6h3.3v3.3h2.6v-3.3h3.3zM10.8 14v4.1h5.7c-0.4 2.4-2.6 4.2-5.7 4.2-3.4 0-6.2-2.9-6.2-6.3s2.8-6.3 6.2-6.3c1.5 0 2.9 0.5 4 1.6v0l2.9-2.9c-1.8-1.7-4.2-2.7-7-2.7-5.8 0-10.4 4.7-10.4 10.4s4.7 10.4 10.4 10.4c6 0 10-4.2 10-10.2 0-0.8-0.1-1.5-0.2-2.2 0 0-9.8 0-9.8 0z"></path></svg></span><span
											class="shariff-text">share</span>&nbsp;</a></li>
									<li class="shariff-button whatsapp shariff-mobile"
										style="background-color: #5cbe4a"><a
										href="whatsapp://send?text=hyperhelp.org%20http%3A%2F%2Fhyperhelp.org%2F"
										title="Share on WhatsApp" aria-label="Share on WhatsApp"
										role="button" rel="nofollow" class="shariff-link"
										target="_blank"
										style="background-color: #34af23; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
													<path
														d="M17.6 17.4q0.2 0 1.7 0.8t1.6 0.9q0 0.1 0 0.3 0 0.6-0.3 1.4-0.3 0.7-1.3 1.2t-1.8 0.5q-1 0-3.4-1.1-1.7-0.8-3-2.1t-2.6-3.3q-1.3-1.9-1.3-3.5v-0.1q0.1-1.6 1.3-2.8 0.4-0.4 0.9-0.4 0.1 0 0.3 0t0.3 0q0.3 0 0.5 0.1t0.3 0.5q0.1 0.4 0.6 1.6t0.4 1.3q0 0.4-0.6 1t-0.6 0.8q0 0.1 0.1 0.3 0.6 1.3 1.8 2.4 1 0.9 2.7 1.8 0.2 0.1 0.4 0.1 0.3 0 1-0.9t0.9-0.9zM14 26.9q2.3 0 4.3-0.9t3.6-2.4 2.4-3.6 0.9-4.3-0.9-4.3-2.4-3.6-3.6-2.4-4.3-0.9-4.3 0.9-3.6 2.4-2.4 3.6-0.9 4.3q0 3.6 2.1 6.6l-1.4 4.2 4.3-1.4q2.8 1.9 6.2 1.9zM14 2.2q2.7 0 5.2 1.1t4.3 2.9 2.9 4.3 1.1 5.2-1.1 5.2-2.9 4.3-4.3 2.9-5.2 1.1q-3.5 0-6.5-1.7l-7.4 2.4 2.4-7.2q-1.9-3.2-1.9-6.9 0-2.7 1.1-5.2t2.9-4.3 4.3-2.9 5.2-1.1z"></path></svg></span><span
											class="shariff-text">share</span>&nbsp;</a></li>
									<li class="shariff-button threema shariff-mobile"
										style="background-color: #4fbc24"><a
										href="threema://compose?text=hyperhelp.org%20http%3A%2F%2Fhyperhelp.org%2F"
										title="Share on Threema" aria-label="Share on Threema"
										role="button" rel="nofollow" class="shariff-link"
										target="_blank"
										style="background-color: #1f1f1f; color: #fff"><span
										class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
													<path
														d="M30.8 10.9c-0.3-1.4-0.9-2.6-1.8-3.8-2-2.6-5.5-4.5-9.4-5.2-1.3-0.2-1.9-0.3-3.5-0.3s-2.2 0-3.5 0.3c-4 0.7-7.4 2.6-9.4 5.2-0.9 1.2-1.5 2.4-1.8 3.8-0.1 0.5-0.2 1.2-0.2 1.6 0 0.4 0.1 1.1 0.2 1.6 0.4 1.9 1.3 3.4 2.9 5 0.8 0.8 0.8 0.8 0.7 1.3 0 0.6-0.5 1.6-1.7 3.6-0.3 0.5-0.5 0.9-0.5 0.9 0 0.1 0.1 0.1 0.5 0 0.8-0.2 2.3-0.6 5.6-1.6 1.1-0.3 1.3-0.4 2.3-0.4 0.8 0 1.1 0 2.3 0.2 1.5 0.2 3.5 0.2 4.9 0 5.1-0.6 9.3-2.9 11.4-6.3 0.5-0.9 0.9-1.8 1.1-2.8 0.1-0.5 0.2-1.1 0.2-1.6 0-0.7-0.1-1.1-0.2-1.6-0.3-1.4 0.1 0.5 0 0zM20.6 17.3c0 0.4-0.4 0.8-0.8 0.8h-7.7c-0.4 0-0.8-0.4-0.8-0.8v-4.6c0-0.4 0.4-0.8 0.8-0.8h0.2l0-1.6c0-0.9 0-1.8 0.1-2 0.1-0.6 0.6-1.2 1.1-1.7s1.1-0.7 1.9-0.8c1.8-0.3 3.7 0.7 4.2 2.2 0.1 0.3 0.1 0.7 0.1 2.1v0 1.7h0.1c0.4 0 0.8 0.4 0.8 0.8v4.6zM15.6 7.3c-0.5 0.1-0.8 0.3-1.2 0.6s-0.6 0.8-0.7 1.3c0 0.2 0 0.8 0 1.5l0 1.2h4.6v-1.3c0-1 0-1.4-0.1-1.6-0.3-1.1-1.5-1.9-2.6-1.7zM25.8 28.2c0 1.2-1 2.2-2.1 2.2s-2.1-1-2.1-2.1c0-1.2 1-2.1 2.2-2.1s2.2 1 2.2 2.2zM18.1 28.2c0 1.2-1 2.2-2.1 2.2s-2.1-1-2.1-2.1c0-1.2 1-2.1 2.2-2.1s2.2 1 2.2 2.2zM10.4 28.2c0 1.2-1 2.2-2.1 2.2s-2.1-1-2.1-2.1c0-1.2 1-2.1 2.2-2.1s2.2 1 2.2 2.2z"></path></svg></span><span
											class="shariff-text">share</span>&nbsp;</a></li>
									<li class="shariff-button rss"
										style="background-color: #ff8c00"><a
										href="http://hyperhelp.org/feed/" title="rss feed"
										aria-label="rss feed" role="button" rel="noopener nofollow"
										class="shariff-link" target="_blank"
										style="background-color: #fe9312; color: #fff"><span
											class="shariff-icon"><svg width="32px" height="20px"
													xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
													<path
														d="M4.3 23.5c-2.3 0-4.3 1.9-4.3 4.3 0 2.3 1.9 4.2 4.3 4.2 2.4 0 4.3-1.9 4.3-4.2 0-2.3-1.9-4.3-4.3-4.3zM0 10.9v6.1c4 0 7.7 1.6 10.6 4.4 2.8 2.8 4.4 6.6 4.4 10.6h6.2c0-11.7-9.5-21.1-21.1-21.1zM0 0v6.1c14.2 0 25.8 11.6 25.8 25.9h6.2c0-17.6-14.4-32-32-32z"></path></svg></span><span
											class="shariff-text">rss feed</span>&nbsp;</a></li>
								</ul>
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