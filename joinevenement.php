<!DOCTYPE html>
<html>
<head>	
	<meta charset="utf-8">
	<title>Rejoindre événement</title>
<?php 
require 'assets/php/header.php'; 
if (!isset($_SESSION["connected"]) || $_SESSION["connected"] == false) {
	header("location: index.php");
}
$eventjoin = showAllJoinableEvent(["id_utilisateur" => $_SESSION["id"]]);
$invited = showAllInvitedEvent(["id_utilisateur" => $_SESSION["id"]]);
?>

<main>
	<section>
		<div class="master-content">
<!--------------------Publique-------------------->
			<h1 >Evénement(s) publique(s):</h1>
<?php 
$array = [];
foreach ($eventjoin as $testdeadlinek => $testdeadlinev) {
 	if (strtotime($testdeadlinev["deadline_e"]) <= time()) {
 		array_push($array,$testdeadlinev["deadline_e"]);
 	}
}
 	if (count($array) == count($eventjoin)):
?>
		<div><p>Aucun événement publique</p></div>
<?php else : ?>
		<div class="master-events">
<?php 
foreach ($eventjoin as $key => $value) :
	if (strtotime($value["deadline_e"]) > time()):
		$t = explode('-',$value['deadline_e']);		
		$infoUser = getInfoUser(["id_utilisateur" => $value['id_utilisateur']])[0];
		if ($infoUser["nom_u"] && $infoUser["prenom_u"]) {
			$createur = $infoUser["nom_u"]." ".$infoUser["prenom_u"];
		} else {
			$createur = $infoUser["email_u"];
		}
?>
			<div>
				<ul>
					<h1><?='titre: '.$value['titre_e']?></h1>
					<li><?="Créateur: ".$createur?></li>
					<li><?="description: ".$value['desc_e']?></li>
					<h2>Date possible de l'événement:</h2>
					<ul>
						<form method="post">
							<input style="display: none;" type="hidden" name="join" value="<?=$value['id_evenement']?>" readonly>
<?php
$dateDispo = getDatePossible([ "id_evenement" => $value['id_evenement'] ]);
foreach ($dateDispo as $index => $valeur): 
	$date = explode('-',$valeur['date_dp']);

?>
							<li><?=$date[2].'/'.$date[1].'/'.$date[0]?><input type="checkbox" name="date<?=$index?>" value="<?=$valeur['id_datepossible']?>"></li>
<?php endforeach; ?>			
							<input class="cross-link-index" style="width: 29px;height: 29px; border: 0;" type="submit" value="+">
						</form>
					</ul>
					<li><?="Fin d'incription: ".$t[2].'/'.$t[1].'/'.$t[0]?></li>
<?php if ($value['public_e'] == 0): ?>
					<li><?="Evénement privé"?></li>
<?php else : ?>
					<li><?="Evénement public"?></li>
<?php endif; ?> 
				</ul>
			</div>
<?php endif; endforeach;?>
		</div>
<?php 
endif;
if (isset($_POST["join"])) {
	foreach ($dateDispo as $dpk => $dpv) {
		if (isset($_POST["date".$dpk]) && $_POST["date".$dpk] == $dpv["id_datepossible"]) {
			foreach ($eventjoin as $evkey => $evvalue) {
				if ($_POST["join"] == $evvalue['id_evenement']) {
					$dateChoose = $_POST["date".$dpk];
					addDateChoose([
						"id_datepossible" => $dateChoose,
						"id_utilisateur" => $_SESSION["id"],
						"id_evenement" => $evvalue["id_evenement"]
					]);
					$allDates = getDateChoose(["id_utilisateur" => $_SESSION["id"],"id_evenement" => $evvalue["id_evenement"]]);
				}
			}
		}
	}
	if (isset($allDates) && count($allDates)>0) {
		foreach ($eventjoin as $key3 => $value3) {
			if ($_POST["join"] == $value3['id_evenement']) {
				$array = [
					"id_evenement" => $_POST["join"],
					"id_utilisateur" => $_SESSION["id"]
				];
				joinEvent($array);
				echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
			}
		}
	}
}

$allInvitedEvent = showAllInvitedEvent(["id_utilisateur" => $_SESSION["id"]]);
?>
<!--------------------Invitation-------------------->
			<h1 >invitation(s):</h1>
<?php if (count($allInvitedEvent )< 1) : ?>
		<div><p>Aucune invitation</p></div>
<?php else : ?>
			<div class="master-events">
<?php
foreach ($allInvitedEvent as $k => $v):
	$invitedEvent = getEvent(["id_evenement" => $v["id_evenement"]]);
	foreach ($invitedEvent as $key => $value):
		$t = explode('-',$value['deadline_e']);
		$infoUser = getInfoUser(["id_utilisateur" => $value['id_utilisateur']])[0];
		if ($infoUser["nom_u"] && $infoUser["prenom_u"]) {
			$createur = $infoUser["nom_u"]." ".$infoUser["prenom_u"];
		} else {
			$createur = $infoUser["email_u"];
		}
?>
			<div>
				<ul>
					<h1><?='titre: '.$value['titre_e']?></h1>
					<li><?="Créateur: ".$createur?></li>
					<li><?="description: ".$value['desc_e']?></li>
					<h2>Date possible de l'événement:</h2>
					<ul>
						<form method="post">
							<input style="display: none;" type="hidden" name="accept" value="<?=$v['id_inv']?>" readonly>
<?php
$dateDispo = getDatePossible([ "id_evenement" => $value['id_evenement'] ]);
foreach ($dateDispo as $index => $valeur): 
	$date = explode('-',$valeur['date_dp']);	

?>
							<li><?=$date[2].'/'.$date[1].'/'.$date[0]?><input type="checkbox" name="date<?=$index?>" value="<?=$valeur['id_datepossible']?>"></li>
<?php endforeach; ?>			
							<input class="cross-link-index" style="width: 29px;height: 29px; border: 0;" type="submit" value="+">
						</form>
					</ul>
					<li><?="Fin d'incription: ".$t[2].'/'.$t[1].'/'.$t[0]?></li>
<?php if ($value['public_e'] == 0): ?>
					<li><?="Evénement privé"?></li>
<?php else : ?>
					<li><?="Evénement public"?></li>
<?php endif; ?> 
				</ul>
			</div>
<?php endforeach;endforeach; ?>
		</div>
<?php 
endif;
?>
		</div>
	</section>
</main>
</body>
</html>
<?php
if (isset($_POST["accept"])) {
	foreach ($dateDispo as $dpk => $dpv) {
		if (isset($_POST["date".$dpk]) && $_POST["date".$dpk] == $dpv["id_datepossible"]) {
			foreach ($allInvitedEvent as $key => $value) {
				if ($_POST["accept"] == $value['id_inv']) {
					$dateChoose = $_POST["date".$dpk];
					addDateChoose([
						"id_datepossible" => $dateChoose,
						"id_utilisateur" => $_SESSION["id"],
						"id_evenement" => $value["id_evenement"]
					]);
				}
			}
		}
	}
	foreach ($allInvitedEvent as $allk => $allv) {
		if ($_POST["accept"] == $allv["id_inv"]) {
			joinInvitedEvent(array(
				"id_inv" => $allv["id_inv"],
				"id_evenement" => $allv["id_evenement"],
				"id_utilisateur" => $_SESSION["id"]
			));
			echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
		}
	}
}
?>