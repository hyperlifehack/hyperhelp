<?php
session_start(); // some values will have to be remembered
/*=== header start ===*/
require_once('./lib/php/lib_mysqli_commands.php');	// library needed to access database

// init database
config::set('lib_mysqli_interface_instance',new lib_mysqli_interface()); // create instance from class and store reference to this instance in config for reuse
config::set('lib_mysqli_commands_instance',new lib_mysqli_commands()); // create instance from class and store reference to this instance in config for reuse

$user = ""; // currently loggedin user
require_once('./lib/php/lib_security.php');			// will mysql-real-escape all input
require_once('./lib/php/lib_session.php');			// check if user is allowed to access this page
/*=== header ends ===*/

$logged_in_user = session_valid(); // check if user accessing this page has logged in = session valid or not -> logout.php
$status_capture = ""; // string if and why login would not work
$refresh_page = false; // if page should be refreshed or not

/* try to detect capture event */
if($logged_in_user)
{
	if(isset($_REQUEST['action']))
	{
		if($_REQUEST['action'] == "add_new_skill")
		{
			$output = capture($users);
			$status_capture = $status_capture." ".$output["status_capture"];
			$refresh_page = $output["refresh_page"];
		}
	}
}

/* add a new skill to a user */
function add_new_skill()
{
	// test if skill already exists
	// yes -> add user to UserListRandomID, comma, separated
	// no -> new skill record
	$skill_template = config::get('lib_mysqli_commands_instance')->newRecord("skills");
	$skill_template->RandomID = salt();
	$skill_template->RandomID = salt();
	$logged_in_user
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
	echo '<meta http-equiv="refresh" content="3;URL=myskills.php"/>';
}
?>
	<!-- custom site specific style -->
	<style>
	.column50 {
		border: 1px solid #5bc0de;
	}
	</style>

</head>

<body id="body">
	<div id="parent">
		<div class="centered">
			<div id="content">
				<div class="border">
					<div class="element">
						<div id="headline" class="boxShadows" style="margin-top: 0px;" title="for security: works without Javascript :-D">
							<h1>
								<div id="headline_text">My Skills</div>
							</h1>
							<h2><div style="background-color: white; color: red;"><?php echo $status_capture; ?></div></h2>
							<?php include("text/menu.php"); ?>
						</div>
						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
										<img class="profilepicture" src="<?php if($logged_in_user && isset($logged_in_user->profilepicture)){ echo $logged_in_user->profilepicture; } ?>" alt="ProfilePicture - logged in user">
									</div>
								</div>
							</div>

							<h1>
							<?php
								if($logged_in_user)
								{
									echo $logged_in_user->username;
								}
							?> can:
							</h1>

							<form action="myskills.php">
								<div class="table">
									<div class="column50">
										<div class="line">
											<div class="prop">
												Skill:
											</div>
											<div class="value">
												Delete this skill?
											</div>
											<div class="line">
											
												<?php
												if($logged_in_user)
												{
													// show all skills of logged in user
													$skills_user = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `".config::get("db_name")."`.`skills` WHERE `RandomID` LIKE '%".$logged_in_user->RandomID."%';");
													
													foreach($skills_user as $key => $skill) {
														echo '
															<div class="prop">
																<a href="myskills.php?delete_skill='.$skill->skill.'&RandomID='.$skill->RandomID.'">
																	<div class="element_select">'.$skill->skill.'</div>
																</a>
															</div>
															<div class="value">
																<input type="checkbox" name="DeleteSkill" value="'.$skill->RandomID.'">
															</div>
															';
													}
												}
												?>
											</div>
										</div>
									</div>
									<div class="column50">
										<div class="line">
											<div class="prop">
												Number of Minutes spend on this Skill:
											</div>
											<div class="value">
												1232
											</div>
										</div>
									</div>
								</div>
								<input type="submit" name="del" value="del" class="button" style="width: 100%; height: 40px;"/>
							</form>

							<h2 title="Add a Skill to your repository of Skills">Add Brand New Skill:</h2>

							<div class="table">
								<div class="column100">
									<div class="line">
										<form action="myskills.php">
											<p>Name:</p>
											<input name="new_skill_name" placeholder="I can do this :)" value="<?php remember_value("new_skill_name");?>" type="text" style="width: 100%;"/>
											<p>Description:</p>
											<input name="new_skill_description" placeholder="more details if you wish to add... " value="<?php remember_value("new_skill_description");?>" type="text" style="width: 100%;"/>
											<input name="skill" value="add_new_skill" hidden>
											<input type="submit" name="add" value="add" class="button" style="width: 100%; height: 40px;"/>
										</form>
									</div>
								</div>
							</div>
							
							<h2 title="You can check out what skills are there in your community and copy them.">All skills of all users in your community:</h2>
							<p>(you can click on one to add them to your repository of skills)</p>

							<div class="table">
								<div class="column100">
									<div class="line">
										<div id="list_of_all_skills_of_all_users" class="column100" style="text-align: center;">
											<?php
											if($logged_in_user)
											{
												// show all skills of all users
												$skills_all = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `".config::get("db_name")."`.`skills`;");
												foreach($skills_all as $key => $skill) {
													echo '<a href="myskills.php?what_select='.$skill->keyword.'&what_id='.$skill->id.'"><div class="element_select">'.$skill->keyword.'</div></a>'; 
												}
											}
											?>
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