<?php session_start();?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title>Albatros Sensei: connexion</title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="connexion">
				<div class="connexion">
					<?php
						if(!empty($_GET['statut'])) 
						{
				 
							switch($_GET['statut']) 
							{
								case 'wrong_captcha':
									echo'le captcha est incorrect';
									break;
									
								case 'wrong_pseudo':
									echo'Le pseudonyme ets incorrect ou n\'existe pas';
									break;
								
								case 'wrong_password':
									echo'Il manque le champ mot de passe';
									break;
									
								default:
									echo'Erreur Inconnue';
							}
						}
					?>
				
					<form method="post" action="connec_status.php">
						<h3>
							Connexion à votre compte:
						</h3>
						<br/>
						<label for="pseudo">Votre adresse mail ou nom de compte :</label>
						</br>
						<input type="text" name="pseudo" id="pseudo" autofocus required/>
						</br>
						</br>
						<label for="passmot">Votre mot de passe :</label>
						</br>
						<input type="password" name="password" id="password" required/>
						</br>
						</br>
						
						<input type="submit" value="Connexion"/>
					<form/>
					
					<a href="forgottenPass.php"> Mot de passe oublié?</a>
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>