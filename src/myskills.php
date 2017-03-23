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

$_SESSION["logged_in_user"] = session_valid(); // check if user accessing this page has logged in = session valid or not -> logout.php
$_SESSION["status"] = ""; // string if and why login would not work
$_SESSION["refresh_page"] = false; // if page should be refreshed or not

$skills_all = config::get('lib_mysqli_commands_instance')->records("skills"); // get the full list of all skills from database

/* try to detect capture event */
if($_SESSION["logged_in_user"])
{
	if(isset($_REQUEST['action']))
	{
		if($_REQUEST['action'] == "add_new_skill")
		{
			add_new_skill($skills_all);
		}
		if($_REQUEST['action'] == "del_skill")
		{
			del_skill($skills_all);
		}
	}
}

/* delete selected skills from the list of skills the user has/can do, if there are no more users on the list, delete the skill entirely
 * $skills_all = a array of all skills in the database
 * */
function del_skill($skills_all = null)
{
	if($skills_all != null)
	{
		foreach($_REQUEST as $post_request_key => $skill_selected) // process list of selected skills
		{
			// detect RandomID...0..1..2...
			$RandomID = substr($post_request_key, 0, 14);
			if($RandomID == "Skill_RandomID")
			{
				// are there more users on the skill list than me?
				$skill = null;
				foreach($skills_all as $array_key => $skill) // process list of selected skills
				{
					if($skill->RandomID == $_REQUEST[$post_request_key])
					{
						break; // skill found
					}
				}

				// more database intensive alternative to using the above foreach:
				// $skill = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `skills` WHERE `RandomID` LIKE '".."' LIMIT 0 , 30;");
				// $skill = GetFirstElementOfArray($skill);

				if($skill)
				{
					$list_of_RandomIDs_string = $skill->UserListRandomID;
	
					// is the user on that list?
					if (strpos($list_of_RandomIDs_string, $_SESSION["logged_in_user"]->RandomID) !== false)
					{
						// yes user is
						$list_of_RandomIDs_string = str_replace($list_of_RandomIDs_string, "", $_SESSION["logged_in_user"]->RandomID);
						$list_of_RandomIDs_string = str_replace($list_of_RandomIDs_string, "", ","); // what is left, if we also remove the semicolons?

						if(empty($list_of_RandomIDs_string)) // no UserRandomID left?
						{
							// Yes -> actually delete the skill from database
							$skill = config::get('lib_mysqli_interface_instance')->query("DELETE FROM `admin_hyperhelp`.`skills` WHERE `RandomID` = '".$skill->RandomID."';");
							$_SESSION["status"] = $_SESSION["status"]." Skill(s) deleted.";
							$_SESSION["refresh_page"] = true;
						}
						else
						{
							// No, there are still users doing this skill -> just remove User_RandomID from list
							// update changes in database
							$skill->UserListRandomID = $list_of_RandomIDs_string;
							config::get('lib_mysqli_commands_instance')->RecordEdit("skills",$skill);
							$_SESSION["status"] = $_SESSION["status"]." Skill(s) deleted from your list of skills.";
							$_SESSION["refresh_page"] = true;
						}
					}
					else
					{
						// no user is not, how the heck did user get here?
					}
				}
			}
		}
	}
}

/* add a new skill to a user */
function add_new_skill($skills_all)
{
	// test if skill already exists
	$skill_existing = null;
	foreach($skills_all as $array_key => $skill) // process list of selected skills
	{
		if($skill->skill == $_REQUEST["new_skill_name"])
		{
			$skill_existing = $skill;
			break; // skill found
		}
	}
	
	// more database intensive alternative to above foreach:
	// $skill_existing = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `skills` WHERE `skill` LIKE '".$_REQUEST["new_skill_name"]."' LIMIT 0 , 30;");
	// $skill_existing = GetFirstElementOfArray($skill_existing);
	
	if($skill_existing)
	{
		// yes
		// is user allready part of the list of users that have/can do that skill?
		if (strpos($skill_existing->UserListRandomID, $_SESSION["logged_in_user"]->RandomID) !== false)
		{
			// yes, do nothing just notify user ;)
			$_SESSION["status"] = $_SESSION["status"]." You are allready in the list of users that have/can do this skill... :) thanks for your commitment.";
		}
		else
		{
			// no, add user to the list of users that have/can do this skill
			$skill_existing->UserListRandomID = $skill_existing->UserListRandomID.",".$_SESSION["logged_in_user"]->RandomID; // add user to UserListRandomID,comma,se,pa,rated list of users that have/can do that skill.
			config::get('lib_mysqli_commands_instance')->RecordEdit("skills",$skill_existing);

			$_SESSION["status"] = $_SESSION["status"]." You were added to the list of users that have/can do this skill. Thanks for your hard work and dedication.";
		}
	}
	else
	{
		// no -> new skill record
		$skill_template = config::get('lib_mysqli_commands_instance')->newRecord("skills");
		$skill_template->RandomID = salt();
		$skill_template->UserListRandomID = $_SESSION["logged_in_user"]->RandomID;
		$skill_template->skill = $_REQUEST["new_skill_name"];
		$skill_template->skill_description = $_REQUEST["new_skill_description"];
		$skill_new = config::get('lib_mysqli_commands_instance')->RecordAdd("skills",$skill_template); // returns the record-object from database, containing a new, database generated id, that is important for editing/deleting the record later

		$_SESSION["status"] = $_SESSION["status"]." New skill added :) with RandomID ".$skill_template->RandomID." Thanks for your hard work and dedication.";
	}
	
	$_SESSION["refresh_page"] = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php
include('text/head.php');
// if a record was created reload the page
if($_SESSION["refresh_page"])
{
	echo '<meta http-equiv="refresh" content="5;URL=myskills.php"/>';
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
							<h2><div style="background-color: white; color: red;"><?php echo $_SESSION["status"]; ?></div></h2>
							<?php include("text/menu.php"); ?>
						</div>
						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
										<img class="profilepicture" src="<?php if($_SESSION["logged_in_user"] && isset($_SESSION["logged_in_user"]->profilepicture)){ echo $_SESSION["logged_in_user"]->profilepicture; } ?>" alt="ProfilePicture - logged in user">
									</div>
								</div>
							</div>

							<h1>
							<?php
								if($_SESSION["logged_in_user"])
								{
									echo $_SESSION["logged_in_user"]->username;
								}
							?> can:
							</h1>

							<form action="myskills.php">
								<div class="table">
									<div class="column100">
										<div class="line">
											<div class="prop">
												<b>Skill:</b>
											</div>
											<div class="value">
												<b>Delete this skill?</b>
											</div>
											<?php
											if($_SESSION["logged_in_user"])
											{
												// show all skills of logged in user
												$skills_user = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `skills` WHERE `UserListRandomID` LIKE '%".$_SESSION["logged_in_user"]->RandomID."%';");
												$counter = 0;
												foreach($skills_user as $key => $skill) {
													echo '
															<div class="line">
																<div class="prop">
																	<a href="https://startpage.com/?='.$skill->skill.'">
																		<div class="element_select">'.$skill->skill.'</div>
																	</a>
																		'.$skill->skill_description.'
																</div>
																<div class="value">
																	<input type="checkbox" name="Skill_RandomID'.$counter++.'" value="'.$skill->RandomID.'">
																</div>
																<div class="value">
																	<br>
																	Number of Minutes spend on this Skill: '.$skill->MinutesSpend.'
																</div>
															</div>
														';
												}
											}
											?>
										</div>
									</div>
								</div>
								<input type="submit" name="del" value="del" class="button" style="width: 100%; height: 40px;"/>
								<input name="action" value="del_skill" hidden>
							</form>

							<h2 title="Add a Skill to your repository of Skills">+Add New Skill:</h2>

							<div class="table">
								<div class="column100">
									<div class="line">
										<form action="myskills.php">
											<p>Name:</p>
											<input name="new_skill_name" placeholder="I can do this :)" value="<?php remember_value("new_skill_name");?>" type="text" style="width: 100%;"/>
											<p>Description:</p>
											<input name="new_skill_description" placeholder="more details if you wish to add... " value="<?php remember_value("new_skill_description");?>" type="text" style="width: 100%;"/>
											<input name="action" value="add_new_skill" hidden>
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
											if($_SESSION["logged_in_user"])
											{
												// show all skills of all users
												// $skills_all = config::get('lib_mysqli_interface_instance')->query("SELECT * FROM `".config::get("db_name")."`.`skills`;");
												foreach($skills_all as $key => $skill) {
													echo '<a href="myskills.php?new_skill_name='.$skill->skill.'&new_skill_description='.$skill->skill_description.'"><div class="element_select">'.$skill->skill.'</div></a>'; 
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