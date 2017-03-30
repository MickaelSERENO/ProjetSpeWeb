<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title>Albatros Sensei! Inscription</title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<h1 class="acc"> Bienvenue sur ce site web funtastic! </h1>
			<section class="connexion">
				<div class="connexion">
					<form method="post" action="connec_status.php">
						<h3>
						Inscrivez vous en remplissant les champs suivants:
						</h3>
						<br/>
						<label for="E-mail">Votre adresse mail :</label>
						</br>
						<input type="email" name="E-mail" value="truc@machin.com" id="mailInscr" autofocus required/>
						</br>
						</br>
						
						<label for="pseudo">Pseudo :</label>
						</br>
						<input type="text" name="pseudo" value="Trucmuche" id="pseudo" autofocus required/>
						</br>
						</br>
						
						<label for="tel">Numéro de téléphone (facultatif) :</label>
						</br>
						<input type="tel" name="tel" id="tel"/>
						</br>
						</br>
						
						<label for="passmot">Votre mot de passe :</label>
						</br>
						<input type="password" name="passmot" id="passmot" required/>
						</br>
						</br>
						
						<label for="passmot">Confirmez le mot de passe :</label>
						</br>
						<input type="password" name="passmot2" id="passmot" required/>
						</br>
						</br>
						
						<label for="dateNaissance">Entrer votre date de naissance :</label>
						</br>
						<input type="date" name="dateNaissance" id="dateNaissance" required/>
						</br>
						</br>
						
						<input type="submit" value="Connexion"/>
					<form/>
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>