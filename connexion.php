<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Connexion</title>
<?php
require 'assets/php/header.php';
if ( isset($_SESSION["connected"]) && $_SESSION["connected"] == true ) {
	header("location: index.php");
}
?>
	<main>
		<section class="master-content" style="height: calc(100vh - 50px); margin: 0;">
			<form method="post" class="popup-form">
				<div class="popup-form-div">
					<div class="w100" style="margin-bottom: 5px;">
						<label for="email">Email</label>
						<input type="email" name="email" required>
					</div>
					<div style="margin-bottom: 5px;">
						<label for="password">Mot de passe</label>
						<input type="password" name="password" required>
					</div>
					<input type="submit" value="se connecter" class="button">
				</div>
			</form>
<?php 
if (isset($_POST["email"]) && isset($_POST["password"])) {
	if (strlen($_POST["email"]) != 0 && strlen($_POST["password"]) != 0) {
		$hash = connexion(array(
			"email" => $_POST["email"]
		));
		if (count($hash) > 0) {
			if (password_verify($_POST["password"],$hash[0]["mdp_u"])){
				$_SESSION["connected"] = true;
				$_SESSION["id"] = getIdUser(["email" => $_POST["email"]])[0]["id_utilisateur"];
				echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
			} else {
				$_SESSION["connected"] = false;
				echo "<span class='bad-issues'>Mauvais identifiants</span>";
			}
		} else {
			$_SESSION["connected"] = false;
			echo "<span class='bad-issues'>Mauvais identifiants</span>";
		}
	}
}
?>
		</section>
	</main>
</body>
</html>