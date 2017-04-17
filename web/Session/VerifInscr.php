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
					
					<?php
						if(!empty($_GET['statut'])) 
						{
				 
							switch($_GET['statut']) 
							{
								case 'wrong_mail'
									echo'Le lien utilisé pour vérifier le compte est incorrect.\n
											Assurez-vous de bien utiliser celui qui vous a été envoyé.';
									break;
									
								case 'wrong_code';
									echo'Le code de vérification est incorrect, assurez vous de ne pas avoir modifier le lien.';
									break;
									
								case 'wrong_captcha'
									echo'Le captcha entré pour renvoyer le code par mail est incorrect';
									break;
									
								default:
									echo'Erreur Inconnue';
							}
						}
					?>
					<p>Vous n'avez pas reçu le mail? Renvoyer un code<p>
					<form method="post" action="sendCodeAgain.php">
						<label for="code">Mail utilisé lors de l'inscription :</label>
						</br>
						<input type="mail" name="mailV" id="mail" required autofocus/>
						</br>
						</br>
						
						<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
						</br>
						<input type="text" name="captcha_code" size="10" maxlength="6" />
						<a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
						</br>
						</br>
						<input type="submit" value="Renvoyer un mail"/>
					<form/>
					<br/><br/><br/><br/>
					
					<h5>En cas d'impossibilité totale de validation, veuillez envoyez un mail à l'aide du lien ci-dessous contenant votre mail et le code reçu à askalbatrossensei@gmail.com:</h5>
					</br>
					<a href="mailto:askalbatrossensei@gmail.com">Contact</a>
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>