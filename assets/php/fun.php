<?php
	function getPdo() {
	    $pdo = new PDO("mysql:host=localhost;dbname=projetphp", "root", "");
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    return $pdo;
	}
	//Inscription de l'utilisateur
	function inscription($array) {
		$pdo = getPdo();
		if (count(emailExist([
				'email' => $array['email'] 
			]))>0) {
			echo "<span class='bad-issues'>Email déjà utilisé</span>";
		} else {
			$stmt = $pdo->prepare("INSERT INTO utilisateur VALUES(NULL, :email, NULL, NULL, :mdp, 'images/user.jpg')");
			$stmt->execute($array);
			echo "<span class='good-issues'>Inscription effectué</span>";
		}
	}
	//Verification si l'email existe ou pas
	function emailExist($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email_u = :email");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Connexion
	function connexion($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT mdp_u FROM utilisateur WHERE email_u = :email");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Création d'un nouvel événement
	function nvevent($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("INSERT INTO evenement VALUES(NULL, :titre, :description, :deadline, :public, :id_utilisateur)");
		$stmt->execute($array);
		//return permettant l'ajout d'une date possible dans nvevenement.php:58
		return getCreatedEvent(["id_utilisateur" => $array["id_utilisateur"]]);
	}
	//Ajout de date durant lesquelles l'événement ce déroulera
	function addDatePossible($array) {
		$pdo = getPdo();
		$date = $array["date_possible"];
		foreach ($date as $key => $value) {
			$date_possible = $value;
			$addate = array(
				"date_possible" => $date_possible,
				"id_evenement" => $array["id_evenement"]
			);
			$stmt = $pdo->prepare("INSERT INTO date_possible VALUES(NULL, :date_possible , :id_evenement)");
			$stmt->execute($addate);
		}
	}
	//Retourne les dates durant lesquelles l'événement ce déroulera
	function getDatePossible($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM date_possible WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Suppréssion d'une date durant laquelle l'événement se déroulait
	function deleteDate($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("DELETE FROM date_possible WHERE id_datepossible = :id_datepossible AND id_evenement = :id_evenement");
		$stmt->execute($array);
	}
	//Retourne tout les événements crée par un utilisateur 
	function getCreatedEvent($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM evenement WHERE id_utilisateur = :id_utilisateur order by deadline_e");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne les événements rejoins par un utilisateur
	function getJoinedEvent($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM evenement,participe WHERE participe."."id_utilisateur = :id_utilisateur AND evenement."."id_evenement = participe."."id_evenement order by deadline_e");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne l'id d'un utilisateur
	function getIdUser($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email_u = :email");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne toutes les informations d'un utilisateur
	function getInfoUser($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Changement de nom + prénom d'un utilisateur
	function updateUser($array) {
		$pdo = getPdo();
		$nom = array( 
			"nom_u" => $array["nom_u"], 
			"id_utilisateur" => $array["id_utilisateur"]
		);
		$stmt = $pdo->prepare("UPDATE utilisateur SET nom_u = :nom_u where id_utilisateur = :id_utilisateur");
		$stmt->execute($nom);
		$prenom = array( 
			"prenom_u" => $array["prenom_u"], 
			"id_utilisateur" => $array["id_utilisateur"]
		);
		$stmt = $pdo->prepare("UPDATE utilisateur SET prenom_u = :prenom_u where id_utilisateur = :id_utilisateur");
		$stmt->execute($prenom);
	}
	//Changement d'image d'un utilisateur
	function updateImageUser($array) {
		$pdo = getPdo();
		$image_name = "images/".$array["image_u"][1].".png";
		$image = array( 
			"image_u" => $image_name,
			"id_utilisateur" => $array["id_utilisateur"]
		);
		$stmt = $pdo->prepare("UPDATE utilisateur SET image_u = :image_u where id_utilisateur = :id_utilisateur");
		$stmt->execute($image);
		move_uploaded_file($array["image_u"][0],$image_name);
	}
	//Suppréssion d'un événement crée ainsi que les participants, les dates et les invitation en cours
	function deleteEvent($array) {		
		$pdo = getPdo();
		$stmt = $pdo->prepare("DELETE FROM participe WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$stmt = $pdo->prepare("DELETE FROM evenement WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$stmt = $pdo->prepare("DELETE FROM date_possible WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$stmt = $pdo->prepare("DELETE FROM date_participe WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
	}
	//Quitter un événement rejoins
	function leaveEvent($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("DELETE FROM participe WHERE id_evenement = :id_evenement and id_utilisateur = :id_utilisateur");
		$stmt->execute($array);
		$stmt = $pdo->prepare("DELETE FROM date_participe WHERE id_evenement = :id_evenement and id_utilisateur = :id_utilisateur");
		$stmt->execute($array);
	}
	//Rejoindre un événement
	function joinEvent($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("INSERT INTO participe VALUES(:id_utilisateur ,:id_evenement)");
		$stmt->execute($array);
	}
	//Ajoute les dates sélectionné
	function addDateChoose($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("INSERT INTO date_participe VALUES(NULL, :id_utilisateur ,:id_datepossible, :id_evenement)");
		$stmt->execute($array);
	}
	//Retourne les dates choisis par l'utilisateur
	function getDateChoose($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM date_possible,date_participe WHERE date_participe."."id_evenement = :id_evenement AND id_utilisateur = :id_utilisateur AND date_possible."."id_datepossible = date_participe."."id_datepossible order by date_dp");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne l'ensemble des choix de date pour un événement
	function getAllDateChoose($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT date_dp FROM date_possible,date_participe WHERE date_participe."."id_evenement = :id_evenement AND date_possible."."id_datepossible = date_participe."."id_datepossible order by date_dp");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Rejoindre un événement suite à une invitation
	function joinInvitedEvent($array) {
		$pdo = getPdo();
		joinEvent(array(
			"id_evenement" => $array["id_evenement"],
			"id_utilisateur" => $array["id_utilisateur"],
		));
		$stmt = $pdo->prepare("DELETE FROM invitation WHERE id_inv = :id_inv");
		$stmt->execute(["id_inv" => $array["id_inv"]]);
	}
	//Retourne tout les événements public pouvant être rejoins
	function showAllJoinableEvent($id) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM evenement WHERE id_utilisateur != :id_utilisateur AND id_evenement NOT IN( SELECT e."."id_evenement FROM evenement e, participe p WHERE p."."id_evenement = e."."id_evenement AND p."."id_utilisateur = :id_utilisateur) and public_e = 1");
		$stmt->execute($id);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne tout les utilisateurs
	function showAllUsers() {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT email_u FROM utilisateur");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Inviter des personnes aux événement privé.
	function invite($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("INSERT INTO invitation VALUES(NULL, :id_evenement, :id_utilisateur)");
		$stmt->execute($array);
	}
	//Retourne un array correspondant à aux informations d'une personne invité sinon retourne un array vide 
	function checkInvited($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM invitation WHERE id_evenement = :id_evenement and id_utilisateur = :id_utilisateur");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}	
	//Retourne les personnes invités qui n'ont pas encore accépté l'invitation 
	function getInvitedPeople($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM invitation WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne les personnes qui ont rejoins l'événement correspondant à l'id
	function getJoinedPeople($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM participe WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne tout les événements dans lesquels l'utilisateur a été invité
	function showAllInvitedEvent($array){
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM invitation WHERE id_utilisateur = :id_utilisateur");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne un evenement
	function getEvent($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM evenement WHERE id_evenement = :id_evenement");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Retourne toutes les invitations
	function getAllInvitation() {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM invitation");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//Supprime toutes les invitations des événements dont la deadline est passé
	function deleteInvitation() {
		$pdo = getPdo();
		$a = getAllInvitation();
		foreach ($a as $key => $value) {
			$b = getEvent(["id_evenement" => $value["id_evenement"]]);
			foreach ($b as $k => $v) {
				if (strtotime($v["deadline_e"]) <= time()) {
					$stmt = $pdo->prepare("DELETE FROM invitation WHERE id_evenement = :id_evenement");
					$stmt->execute(["id_evenement" => $value["id_evenement"]]);
				}
			}
		}
	}
	//Retourne une date à partir de son id
	function getDateFromId($array) {
		$pdo = getPdo();
		$stmt = $pdo->prepare("SELECT * FROM date_possible WHERE id_datepossible = :id_datepossible");
		$stmt->execute($array);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
?>