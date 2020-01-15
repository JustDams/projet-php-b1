<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Tout les utilisateurs</title>
<?php 
require "assets/php/header.php";
if ( !isset($_SESSION["connected"]) || $_SESSION["connected"] == false) {
	header("location: index.php");
	exit();
}
?>

<main>
	<section>
		<div class="master-events">
<?php 
$all = showAllUsers();
$user = getInfoUser(["id_utilisateur" => $_SESSION["id"]]);
foreach ($all as $key => $value) {
	if ( $value["email_u"] == $user[0]["email_u"]) {
		echo "";
	} else {
		echo "<div style='display: flex;justify-content:center;'>".$value["email_u"]."</div>";
	}
}  
?>		</div>
	</section>
</main>
</body>
</html>
