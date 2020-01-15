<?php
session_start();
require 'assets/php/fun.php';
deleteInvitation();
?>
<style type="text/css">
	@import url(assets/css/header.css);
	@import url(assets/css/style.css);
</style>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Catamaran&display=swap" rel="stylesheet">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src=""></script>
<script type="text/javascript" src="assets/js/jquery-anim.js" async></script>
</head>
<body>
	<header>
		<nav>
			<div class="nav-bar-hamburger"><span></span><span></span><span></span><span></span></div>
			<ul>
				<li <?php if (!isset($_SESSION["connected"]) || $_SESSION["connected"] == false):?>
						style="background:white"
						<?php endif;?>>
						<a href="index.php">Accueil</a></li>
				<?php if ( !isset($_SESSION["connected"]) || $_SESSION["connected"] == false ): ?>
					<li><a class="headerlink" href="connexion.php">connexion</a></li>
					<li><a class="headerlink" href="inscription.php">inscription</a></li>
					<?php elseif ( isset($_SESSION["connected"]) && $_SESSION["connected"] == true ): ?>
					<div class="dropdown-container">
						<li><a>événement</a></li>
						<div class="dropdown-items">
							<li><a class="headerlink" href="nvevenement.php">Créer</a></li>
							<li><a class="headerlink" href="joinevenement.php">Rejoindre</a></li>
						</div>
					</div>
					<li class="headerlink" style="flex:2;"><a href="utilisateurs.php">tout les utilisateurs</a></li>
					<li class="headerlink"><a href="profil.php">profil</a></li>
					<li class="headerlink"><a href="deconnexion.php">deconnexion</a></li>
					<?php endif; ?>
				</ul>
			</nav>
		</header>