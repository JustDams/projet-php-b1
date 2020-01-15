<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Accueil</title>
<?php 
require "assets/php/header.php";
if (isset($_GET["delete"])){
	$id_evenement = ["id_evenement" => $_GET["delete"]];
	deleteEvent($id_evenement);
}
if (isset($_GET["leave"])){
	$array = [
		"id_evenement" => $_GET["leave"],
	 	"id_utilisateur" => $_SESSION["id"]
	];
	leaveEvent($array);
}
if (isset($_GET["dddp"]) && isset($_GET["ddid"])){
	foreach (getCreatedEvent(["id_utilisateur" => $_SESSION["id"]]) as $key => $value) {
		if($value["id_evenement"] ==  $_GET["ddid"] ) {
			foreach (getDatePossible(["id_evenement" => $_GET["ddid"]]) as $k => $v) {
				if ($v["id_datepossible"] == $_GET["dddp"]) {
					$array = [
						"id_datepossible" => $_GET["dddp"],
						"id_evenement" => $_GET["ddid"],
					];
					deleteDate($array);
				}
				echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
			}
		}
		echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
	}
}
?>
	<main>
		<section>
			<div class="master-content">

<?php if (!isset($_SESSION['connected']) || $_SESSION['connected'] == false ) :?>
<!------------ Affichage si la personne n'est pas connecté ---------->
				<h1>Site de création et de partage d'événement</h1>
				<div><p>Rejoins nous afin de créer des événements et les partager avec tes amis ou bien tout les autres utilisateurs !</p></div>

<?php 
elseif( isset($_SESSION['connected']) && $_SESSION['connected'] == true) : 
	$id_utilisateur = ["id_utilisateur" => $_SESSION['id']];
	$eventlist = getCreatedEvent($id_utilisateur);
	$eventjoin = getJoinedEvent($id_utilisateur);
?>
<!------------ Affichage si la personne est connecté ------------>
	<!------------ événement(s) rejoins ------------>
				<h1>Vos événement(s) rejoins:</h1>

<?php if (count($eventjoin)<1) : ?>
		<!------------ si aucun événement rejoins ------------>
				<div><p>Aucun événement rejoins</p></div>

<?php else : ?>
		<!------------ sinon ------------>
				<div class="master-events">
<?php 
foreach ($eventjoin as $key => $value) :
	if ($_SESSION["id"] == $eventjoin[$key]['id_utilisateur']):
		$allDateChoose = getAllDateChoose([ "id_evenement" => $value['id_evenement']]);
		$dateChoose = getDateChoose([ "id_evenement" => $value['id_evenement'], "id_utilisateur" => $_SESSION["id"] ]);
		$datePossible = getDatePossible([ "id_evenement" => $value['id_evenement'] ]);
		$t = explode('-',$value['deadline_e']);
		$getCreator = getInfoUser(["id_utilisateur" => getEvent(["id_evenement" => $value['id_evenement']])[0]["id_utilisateur"]])[0];
		if ($getCreator["nom_u"] && $getCreator["prenom_u"]) {
			$createur = $getCreator["nom_u"]." ".$getCreator["prenom_u"];
		} else {
			$createur = $getCreator["email_u"];
		}
?>
			<!------------ Affichage des informations sur l'événement rejoins ------------>
					<div>
						<ul>
							<h1>
								<?='titre: '.$value['titre_e']?>
<?php  if (strtotime($value["deadline_e"]) >= time()):?>
								<a class="cross-link-index" href="?leave=<?=$value['id_evenement']?>">x</a>
<?php endif;?>
							</h1>
							<li><?="Créateur: ".$createur?></li>
							<li><?="Description: ".$value['desc_e']?></li>
							<li><?="Fin d'incription: ".$t[2].'/'.$t[1].'/'.$t[0]?></li>
<?php if(strtotime($value["deadline_e"]) >= time()): ?>
							<h2>Date possible de l'événement: </h2>
							<ul>
<?php
foreach ($datePossible as $dpk => $dpv):
	$here = "";
	foreach ($dateChoose as $index => $valeur) {
		$total = 0;
		if (strtotime($dpv["date_dp"]) == strtotime($valeur['date_dp'])) {
			$here = "<span style='color:#ff6666;'>*</span>";
			$date = explode('-',$valeur['date_dp']);		
			foreach ($allDateChoose as $adck => $adcv) {
				if (strtotime($adcv["date_dp"]) == strtotime($valeur['date_dp'])){
					$total += 1; 
				}
			}
		} else {
			$date = explode('-',$dpv['date_dp']);
			foreach ($allDateChoose as $adck => $adcv) {
				if (strtotime($adcv["date_dp"]) == strtotime($dpv['date_dp'])){
					$total += 1; 
				}
			}
		}
	}
?>
								<li><?=$date[2].'/'.$date[1].'/'.$date[0]." (".$total.")".$here?></li>
<?php endforeach; ?>
							</ul>
<?php else: ?>				
							<h2>Date de l'événement: </h2>
							<ul>
<?php
$finalDateEvent = [];
foreach ($datePossible as $dpk => $dpv) {
	foreach ($dateChoose as $index => $valeur) {
		$total = 0;
		if (strtotime($dpv["date_dp"]) == strtotime($valeur['date_dp'])) {
			foreach ($allDateChoose as $adck => $adcv) {
				if (strtotime($adcv["date_dp"]) == strtotime($valeur['date_dp'])){
					$total += 1; 
				}
			}
			array_push($finalDateEvent,["total" => $total, "id_datepossible" => $valeur["id_datepossible"]]);
		} else {
			foreach ($allDateChoose as $adck => $adcv) {
				if (strtotime($adcv["date_dp"]) == strtotime($dpv['date_dp'])){
					$total += 1; 
				}
			}
			array_push($finalDateEvent,["total" => $total, "id_datepossible" => $dpv["id_datepossible"]]);
		}
	}
}
$findDate = [];
$total = [];
foreach ($finalDateEvent as $fdek => $fdev) {
	array_push($total, intval($fdev["total"]));
	array_push($findDate, [$fdev["id_datepossible"],intval($fdev["total"])]);
	if (count($total)>1) {
		if ($total[0] < $total[1]) {
			array_shift($total);
			array_shift($findDate);
		} elseif ($total[0] > $total[1]) {
			array_pop($total);
			array_pop($findDate);
		} elseif ($total[0] = $total[1]) {
			array_shift($total);
		}
		if(isset($findDate[0])) {
			if ($total[0] > $findDate[0][1]) {
				$findDate = [];
				array_push($findDate, [$fdev["id_datepossible"],intval($fdev["total"])]);
			}
		}
	}
}
foreach ($findDate as $fdk => $fdv):
	$allDates = getDateFromId(["id_datepossible" => $fdv[1]]);
	foreach ($allDates as $adk => $adv):
		$date = explode('-',$adv["date_dp"]);
		$total = $fdv[1];
?>
								<li><strong style="color: #006200;"><?=$date[2].'/'.$date[1].'/'.$date[0]." (".$total.")"?></strong></li>
<?php endforeach; endforeach; ?>
							</ul>
<?php endif; if ($value['public_e'] == 0): ?>							
							
							<li><?="Evénement privé"?></li>
<?php else : ?>
							<li><?="Evénement public"?></li>
<?php endif; if (strtotime($value["deadline_e"]) >= time()):?> 
							<span style='color:#ff6666;'>* votre participation</span>
<?php endif; ?>
						</ul>
					</div>
<?php endif; endforeach; ?>
				</div>
<?php endif; ?>
	<!------------ événement(s) crée(s) ------------>
				<h1 style="margin-top: 30px;">Vos événement(s) crée(s):</h1>
<?php if (count($eventlist)<1) : ?>
		<!------------ si aucun événement crée ------------>
				<div ><p>Aucun événement(s) crée(s)</p></div>
		<!------------ sinon ------------>
<?php else : ?>
					<div class="master-events">
<?php 
foreach ($eventlist as $key => $value) :
	if ($_SESSION["id"] == $eventlist[$key]['id_utilisateur']):
		$allDateChoose = getAllDateChoose([ "id_evenement" => $value['id_evenement']]);
		$datePossible = getDatePossible([ "id_evenement" => $value['id_evenement'] ]);
		$t = explode('-',$value['deadline_e']);
		$infoUser = getInfoUser(["id_utilisateur" => $value['id_utilisateur']])[0];
		if ($infoUser["nom_u"] && $infoUser["prenom_u"]) {
			$createur = $infoUser["nom_u"]." ".$infoUser["prenom_u"];
		} else {
			$createur = $infoUser["email_u"];
		}
?>
		<!------------ Affichage des informations sur l'événement crée ------------>
						<div>
							<ul>
								<h1>
									<?='titre: '.$value['titre_e']?>
<?php  if (strtotime($value["deadline_e"]) >= time()):?>
									<a class="cross-link-index" title="supprimer l'événement" href="?delete=<?=$value['id_evenement']?>">x</a>
<?php endif;?>
								</h1>
								<li><?="Créateur: ".$createur?></li>
								<li><?="description: ".$value['desc_e']?></li>
								<li><?="Fin d'incription: ".$t[2].'/'.$t[1].'/'.$t[0]?></li>
<?php  if (strtotime($value["deadline_e"]) >= time()):?>
			<!------------ Si la deadline n'est pas passé ------------>								
							<h2>Date Possible de l'événement: </h2>
							<ul>
<?php 
foreach ($datePossible as $index => $valeur):
	$total = 0;
	foreach ($allDateChoose as $adck => $adcv) {
		if (strtotime($adcv["date_dp"]) == strtotime($valeur['date_dp'])){
			$total += 1; 
		}
	}
	if (strtotime($value["deadline_e"]) >= time()) {
		//
	}

	$date = explode('-',$valeur['date_dp']);
?>
								<li><?=$date[2].'/'.$date[1].'/'.$date[0]." (".$total.")"?></li>
<?php endforeach;?>
							</ul>
<?php else: ?>
			<!------------ Si la deadline est passé ------------>
							<h2>Date de l'événement: </h2>
							<ul>
<?php
$finalDateEvent = [];
foreach ($datePossible as $dpk => $dpv) {
	$total = 0;
	foreach ($allDateChoose as $adck => $adcv) {
		if (strtotime($adcv["date_dp"]) == strtotime($dpv['date_dp'])){
			$total += 1; 
		}
	}
	array_push($finalDateEvent,["total" => $total, "id_datepossible" => $dpv["id_datepossible"]]);
}
$findDate = [];
$total = [];
foreach ($finalDateEvent as $fdek => $fdev) {
	array_push($total, intval($fdev["total"]));
	array_push($findDate, [$fdev["id_datepossible"],intval($fdev["total"])]);
	if (count($total)>1) {
		if ($total[0] < $total[1]) {
			array_shift($total);
			array_shift($findDate);
		} elseif ($total[0] > $total[1]) {
			array_pop($total);
			array_pop($findDate);
		} elseif ($total[0] = $total[1]) {
			array_shift($total);
		}
		if(isset($findDate[0])) {
			if ($total[0] > $findDate[0][1]) {
				$findDate = [];
				array_push($findDate, [$fdev["id_datepossible"],intval($fdev["total"])]);
			}
		}
	}
}
foreach ($findDate as $fdk => $fdv):
	$allDates = getDateFromId(["id_datepossible" => $fdv[1]]);
	foreach ($allDates as $adk => $adv):
		$date = explode('-',$adv["date_dp"]);
		$total = $fdv[1];
?>
								<li><?=$date[2].'/'.$date[1].'/'.$date[0]." (".$total.")"?></li>
<?php endforeach; endforeach; ?>
							</ul>
<?php
endif; 
$joined = getJoinedPeople(["id_evenement" => $value['id_evenement']]);
$invited = getInvitedPeople(["id_evenement" => $value['id_evenement']]);
if (count($invited) > 0 && $eventlist[$key]["public_e"] == 0):
?>
							<h2>Liste des invités:</h2>
							<ul>
<?php foreach($invited as $ik => $iv): ?>
								<li>
<?php
$userinvite = getInfoUser(["id_utilisateur" => $iv["id_utilisateur"]])[0];
if (strlen($userinvite["nom_u"]) != 0 && strlen($userinvite["prenom_u"]) != 0){
	echo $userinvite["nom_u"]." ".$userinvite["prenom_u"]."</li>";
} else {
	echo $userinvite["email_u"]."</li>";
}
endforeach;?>
							</ul>
<?php 
endif;
if (count($joined) > 0):
?>
							<h2>Liste des participants:</h2>
							<ul>
<?php foreach($joined as $jk => $jv): ?>
								<li>
<?php
$userjoin = getInfoUser(["id_utilisateur" => $jv["id_utilisateur"]])[0];
if (strlen($userjoin["nom_u"]) != 0 && strlen($userjoin["prenom_u"]) != 0){
	echo $userjoin["nom_u"]." ".$userjoin["prenom_u"];
} else {
	echo $userjoin["email_u"];
}
?>								
								</li>					
<?php endforeach; endif; if (strtotime($value["deadline_e"]) >= time() && $eventlist[$key]['public_e'] == 0): ?>			
								<li class="relative"><span>inviter </span><a class="more-link-index" href="?ide=<?=$value['id_evenement']?>">+</a></li>
<?php endif; if (count($joined) > 0): ?>
							</ul>
<?php endif; endif;?>
							<li>
<?php if ($value['public_e'] == 0): ?>
						<?="Evénement privé"?>
<?php else : ?>
						<?="Evénement public"?>
<?php endif;?>
							</li>
						</ul>
					</div>
<?php endforeach; ?>
				</div>	
<?php endif; ?>
			</div>
<?php endif; ?>
		</section>
	</main>
</body>
</html>
<?php  
if (isset($_GET["ide"])):
	foreach ($eventlist as $key => $value):
		if ($_GET["ide"] == $value["id_evenement"] && $value["public_e"] == 0):
?>
<!------------ Popup pour ajouter une personne à l'événement ------------>
<div class="master-popup">
	<div class="content-popup">
		<div class="cross-item-content"><a href="index.php">x</a></div>
		<span class="popup-title-item">
			Inviter une personne pour: <?=$value["titre_e"]?>
		</span>
		<form method="post" class="popup-form">
			<div class="popup-form-div">
				<label for="email_u">Email de la personne:</label>
				<input type="email" name="email_u" required>
				<input type="submit" value="ajouter" class="button popup-form-submit">
		
<?php
if (isset($_POST["email_u"]) && strlen($_POST["email_u"]) != 0) {
	$user = emailExist(["email" => $_POST["email_u"]]);
	if(count($user) > 0) {
		$array = array(
			"id_evenement" => $_GET["ide"],
			"id_utilisateur" => $user[0]["id_utilisateur"]
		);
		if (count(checkInvited($array)) > 0) {
			echo "<span class='bad-issues'>Cette personne a déjà été invité</span>";
		} else if($user[0]["id_utilisateur"] == $_SESSION["id"]){
			echo "<span class='bad-issues'>Vous ne pouvez pas vous inviter</span>";
		} else {
			invite($array);
			echo "<span class='good-issues'>Invitation éffectué</span>";
		}
	} else {
		echo "<span class='bad-issues''>Cet email n'est pas valide</span>";
	}
}
?>			
			</div>
		</form>
	</div>
</div>
<?php
endif;
endforeach;
endif;
?>