<?php
$session_valid = false;
setcookie("hyperhelp", "", time()-3600); // delete cookie = log em out
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
include_once 'config.php';
include('text/head.php');
?>
</head>
</head>
<body id="body">
	<div id="parent">
		<div class="centered">
			<div id="content">
				<div class="border">
					<div class="element">
						<div class="title button clickable">Thank you for your contributing to your community!</div>
						<div class="element_content">
							<div class="table">
								<div class="column100">
									<div class="line">
											<?php
											$images = glob('images/fun/*');
											$random_image = $images[rand(0, count($images) - 1)];
											echo '<img src="'.$random_image.'"/>';
											?>
											<p>You are a valuable human being!<br>
											Capable of so many (hopefully positive for everybody) things if you put your mind and love to it.</p>
											<h2>YOU HAVE BEEN LOGGED OUT!
											<br>
											:)
											<br>
											to relogin go to: <a href="index.php">LOGIN</a></h2>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- end of element -->

					<?php include('text/SharingButtons.php'); ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>