<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Profil</title>
<?php 
require "assets/php/header.php";
if (!isset($_SESSION['connected']) || $_SESSION['connected'] == false) {
	header("location: index.php");
	exit();
}
$infoUser = getInfoUser(["id_utilisateur" => $_SESSION["id"]]);
?>

	<main>
		<section class="master-content">
<?php
foreach ($infoUser as $key => $value):
$img = $value['image_u'];
?>
			<div>
				<?php if (strlen($img) > 1): ?>
				<img src="<?=$img?>" style="height: 310px;width: auto;border-radius: 15px;">
				<?php elseif (strlen($img) < 1): ?>
				<img src="images/user.jpg" style="height: 310px;width: auto;border-radius: 15px;">
				<?php endif; ?>
			</div>
			<form method="post">
				<div>
					<span>Email: <?=$value["email_u"]?></span>
					<div>
						<label for="lastname">Nom:</label>
						<input type="" name="lastname" value="<?=$value['nom_u']?>" required>
					</div>
					<div>
						<label for="firstname">Prénom:</label>
						<input type="" name="firstname" value="<?=$value['prenom_u']?>" required>
					</div>
				<?php endforeach; ?>
					<div>
						<input type="submit" value="modifier" name="upload">
					</div>
				</div>
			</form>
			<form method="post" enctype="multipart/form-data">
				<div>
					<div>
						<label for="image">Photo de profil</label>
						<input type="file" name="image" required>
					</div>
					<div>					
						<input type="submit" value="modifier" name="upload">
					</div>
				</div>
			</form>
<?php
if (isset($_POST["lastname"]) && isset($_POST["firstname"])) {

	updateUser([
		"nom_u" => $_POST["lastname"],
		"prenom_u" => $_POST["firstname"],
		"id_utilisateur" => $_SESSION["id"],
	]);
	echo "<script type='text/javascript'>document.location.replace('profil.php');</script>";
}
if (isset($_FILES["image"]) && strlen($_FILES["image"]["name"]) > 1) {
		$image = [$_FILES["image"]["tmp_name"],$_SESSION["id"]];
		if (strlen($img) > 1) {
			unlink($img);
		}
		updateImageUser([
			"image_u" => $image,
			"id_utilisateur" => $_SESSION["id"]
		]);
		echo "<script type='text/javascript'>document.location.replace('profil.php');</script>";
	}
?>
		</section>
	</main>
</body>
</html>
