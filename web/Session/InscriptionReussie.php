<?php
session_start();
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title>Albatros Sensei: Valider le compte</title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="connexion">
				<div class="connexion">
					<h2>Votre inscription est presque terminée!</h2>
					<br/>
					<h3>Un email vous a été envoyé à l'adresse utilisée pour valider votre compte.</h3>
					<br/>
					<form method="post" action="verif_account.php">
						<label for="code">Code envoyé par mail :</label>
						</br>
						<input type="text" name="code" id="code" required autofocus/>
						</br>
						</br>
						
						<input type="submit" value="Valider le compte"/>
					<form/>
					
					<p>Vous n'avez pas reçu le mail?<p>
					<br/>
					<a href="sendCodeAgain.php">Renvoyer un mail</a>
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>