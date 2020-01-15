<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Création évenement</title>
	<?php
		require 'assets/php/header.php';
		if (!isset($_SESSION['connected']) || $_SESSION['connected'] == false) {
			header("location: index.php");
			exit();
		}
		if (!isset($_SESSION["date"])) {
			$_SESSION["date"] = [];
		}
	?>
	<main style="padding: 0;">
		<section>
			<form method="post" class="master-content" style="height: 100vh; margin: 0 5px;">
				<div class="create-event-container">
					<h1>Créer un événement:</h1>
					<div class="relative" style="flex-direction: column; align-items: unset;">
						<label>Date possible de l'événement<a class="more-link-index" style="left: 200px; top: 5px;" title="ajouter une date" href="?adddate=1">+</a></label>
						<ul style="margin: 0;">
<?php 
if(isset($_SESSION["date"]) && count($_SESSION["date"]) > 0):
foreach($_SESSION["date"] as $key => $value): 
	$date = explode('-',$_SESSION["date"][$key]);
?>
							<li class="relative"><?=$date[2].'/'.$date[1].'/'.$date[0]?><a class="cross-add-date" href="?delete=<?=$key?>">x</a></li>
<?php endforeach; endif; ?>
						</ul>
					</div>
					<div>
						<label for="titre">Nom de l'évenement</label>
						<input type="text" name="titre" required class="w100">
					</div>
					<div class="flex-col">
						<label for="desc">Description</label>
						<textarea name="desc" rows="4" required></textarea>
					</div>
					<div>
						<label for="deadline">Date de fin d'inscription</label>
						<input type="date" name="deadline" required class="w100">
					</div>
					<div>
						<label for="public">événement publique</label>
						<input type="radio" name="public" value="1" required>
						<label for="public">événement privée</label>
						<input type="radio" name="public" value="0" required>
					</div>
					<input type="submit" value="Créer">
				</div>
<?php
if (isset($_GET["error"]) && $_GET["error"] == 1) {
	echo "<span class='bad-issues'>Certaines dates n'ont pas pu être ajouté</span>";
}
if (isset($_POST["titre"]) && isset($_POST["desc"]) && isset($_POST["deadline"]) && isset($_POST["public"]) && isset($_SESSION["date"])){
	if( strtotime($_POST["deadline"]) < time() ){
		echo "<span class='bad-issues'>La deadline ne peut pas être une date déjà passé</span>";
	} else {
		$b = [];
		foreach($_SESSION["date"] as $dk => $dv) {
			if(strtotime($dv) <= strtotime($_POST["deadline"])) {
				array_push($b, $dv);
				$date = explode('-',$_SESSION["date"][$key]);
				unset($_SESSION['date'][$dk]);
				echo "<script type='text/javascript'>document.location.replace('nvevenement.php?error=1');</script>";
				return;
			}
		}

		$event = array(
			"titre" => $_POST["titre"],
			"description" => $_POST["desc"],
			"deadline" => $_POST["deadline"],
			"public" => intval($_POST["public"]),
			"id_utilisateur" => $_SESSION["id"],
		);
		if (count($_SESSION["date"]) != 0) {
	
			$_SESSION["idEvenement"] = nvevent($event);
			$a = array();

			foreach ($_SESSION["idEvenement"] as $key => $value) {
				array_push($a, intval($value["id_evenement"]));
				if (count($a)>1) {
					if ($a[0] < $a[1]) {
						array_shift($a);
					} elseif ($a[0] > $a[1]) {
						array_pop($a);
					}
				}
			}
			$_SESSION["idEvenement"] = $a[0];
			foreach (getDatePossible(["id_evenement" => $_SESSION["idEvenement"]]) as $key => $value) {
				foreach ($value as $k => $v) {
					if($v ==  $_POST["dateDispo"] ) {
						echo "<span class='bad-issues'>Cette date à déjà été ajouté</span>";
						return;
					}
				}
			}
			addDatePossible([
				'date_possible' => $_SESSION["date"],
				'id_evenement' => $_SESSION["idEvenement"]
			]);
			unset($_SESSION["date"]);
			$_SESSION["date"] = [];
			echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
		}else {
			echo "<span class='bad-issues'>Merci de choisir des dates pour votre événement</span>";
		}
	}
}
?>			</form>
		</section>
	</main>
</body>
</html>
<?php
if (isset($_GET["delete"])) {
	unset($_SESSION["date"][$_GET["delete"]]);
	echo "<script type='text/javascript'>document.location.replace('nvevenement.php');</script>";
	return;
}
if (isset($_GET["adddate"]) && $_GET["adddate"] == 1):
?>
<div class="master-popup">
	<div class="content-popup">
		<div class="cross-item-content"><a href="?adddate=0">x</a></div>
		<span class="popup-title-item">Ajouter une date pour l'évenement:</span>
		<form method="post" class="popup-form">
			<div class="popup-form-div">
				<label for="date">Ajouter une date:</label>
				<input type="date" name="date" required>
				<input type="submit" value="ajouter" class="button popup-form-submit">

<?php
if (isset($_POST["date"]) && strlen($_POST["date"]) != 0) {
	foreach ($_SESSION["date"] as $key => $value) {
		if ($value == $_POST["date"]) {
			echo "<span class='bad-issues'>Cette date à déjà été ajouté</span>";
			return "";
		}
	}
	if (strtotime($_POST["date"]) <= time()) {
		echo "<span class='bad-issues'>Cette date ne peut pas être ajouté</span>";
		return "";
	}
	array_push($_SESSION["date"], $_POST["date"]);
	echo "<span class='good-issues'>Date ajoutée</span>";
}
?>			
			</div>
		</form>
	</div>
</div>
<?php
endif;
?>
