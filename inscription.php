<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Inscription</title>
<?php
require 'assets/php/header.php';
if ( isset($_SESSION["connected"]) && $_SESSION["connected"] == true ) {
	header("location: index.php");
}  
?>
	<main>
		<section class="master-content" style="height: calc(100vh - 50px); margin: 0;">
			<form method="post" class="popup-form">
				<div class="popup-form-div" style="align-items: baseline; margin: 0 10px;">
					<div class="w100" style="margin-bottom: 5px;">
						<label for="email">Email</label>
						<input type="email" name="email" required >
					</div>
					<div style="margin-bottom: 5px;">
						<label for="password">Mot de passe</label>
						<input type="password" name="password" required class="w100">
					</div>
					<div style="margin-bottom: 5px;">
						<label for="reppassword">Répeter le mot de passe</label>
						<input type="password" name="reppassword" required class="w100">
					</div>
					<input type="submit" value="s'inscrire" class="button" style="align-self: center;">
				</div>
			</form>
<?php  
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["reppassword"])){
	if ($_POST["password"] == $_POST["reppassword"]) {
		$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
		$user = array(
			"email" => $_POST["email"],
			"mdp" => $password
		);
		inscription($user);
	} else {
		echo "<span class='bad-issues'>Les mots de passe sont différents</span>";
	}
}
?>
		</section>
	</main>
</body>
</html>