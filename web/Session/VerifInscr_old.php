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
					
					<?php
						if(!empty($_GET['statut'])) 
						{
				 
							switch($_GET['statut']) 
							{
								case 'wrong_captcha':
									echo'le captcha est incorrect';
									break;
								
								case 'code_missing':
									echo'Le champ code n\'a pas été rempli';
									break;
									
								case 'incorrect_code';
									echo'Le code entré est incorrect, essayez de copier coller celui-ci à partir du mail';
									break;

								default:
									echo'Erreur Inconnue';
							}
						}
					?>
					
					<form method="post" action="verif_account.php">
						<label for="code">Code envoyé par mail :</label>
						</br>
						<input type="text" name="code" id="code" required autofocus/>
						</br>
						</br>
						<?php 	if(isset($_SESSION['verifTry']) && $_SESSION['verifTry'] == true)
								{
									echo '
										<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
										</br>
										<input type="text" name="captcha_code" size="10" maxlength="6" />
										<a href="#" onclick="document.getElementById(\'captcha\').src = \'/securimage/securimage_show.php?\' + Math.random(); return false">[ Different Image ]</a>
										</br>
										</br>';
								}		
						?>
						<input type="submit" value="Valider le compte"/>
					<form/>
					<br/><br/><br/><br/>
					<p>Vous n'avez pas reçu le mail?<p>
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