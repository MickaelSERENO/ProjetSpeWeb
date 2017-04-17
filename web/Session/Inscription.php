<?php session_start();?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title>Albatros Sensei: Inscription</title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="connexion">
				<div class="connexion">
					</br>
					
					<?php
					if(isset($_SESSION['clapier']))
								echo "Live : ".$_SESSION['clapier'];
							else
								echo "DIE DIE DIE";
					echo"\r\n";
					echo"\r\n";
					echo"\r\n";
					
					if(!empty($_GET['statut'])) 
					{
						switch($_GET['statut']) 
						{
							case 'wrong_captcha':
								echo'le captcha est incorrect';
								break;
							
							case 'mail_missing':
								echo'Il manque le champ mot de passe';
								break;
								
							case 'pseudo_missing':
								echo'Il manque le champ pseudo';
								break;
							
							case 'password_missing':
								echo'Il manque le champ mot de passe';
								break;
							/*	
							case 'birth_missing':
								echo 'Votre date de naissance a un format incorrext où n\'a pas été rentrée';
								break;
							*/
							case 'mail_different':
								echo 'les deux adresses mail rentrées sont différentes';
								break;
								
							case 'password_different':
								echo'Les mots de passe sont différents';
								break;
								
							case 'not_secured_length':
								echo'Le mot de passe ne respecte pas les consignes demandées (longueur non réglementaire): ';
								break;
								
							case 'not_secured':
								echo'Le mot de passe ne respecte pas les consignes demandées: Il ne contient pas de majuscule ou de chiffre';
								break;
								
							case 'existing_mail':
								echo 'Le mail entré est déja celui d\'un utilisateur existant';
								break;
							
							case 'user_existing':
								echo 'Utilisateur Existant: Veuillez choisir un autre pseudo';
								break;
								
							case 'mail_existing':
								echo 'Ce mail est déja enregistré pour un autre utilisateur';
								break;
							default:
								echo'Erreur Inconnue';
						}
					}
					?>
					
					</br>
					<form method="post" action="inscr_form.php">
						<h3>
							Inscrivez vous en remplissant les champs suivants. Sans autre précision, le champ est obligatoire:
						</h3>
						</br>
						
						<?php 	if(isset($_SESSION['mail']))
									$mailEx = $_SESSION['mail'];
								else
									$mailEx = "example@domain.com";
						?>
						<label for="mail">Votre adresse mail :</label>
						</br>
						<input type="email" name="mail" value="<?php echo $mailEx ?>" id="mailInscr" autofocus required/>
						</br>
						</br>
						
						<label for="mailVer">Confirmez votre adresse mail :</label>
						</br>
						<input type="email" name="mailVer" id="mailInscr" required/>
						</br>
						</br>
						
						<?php 	if(isset($_SESSION['pseudo']))
									$pseudEx = $_SESSION['pseudo'];
								else
									$pseudEx = "";
						?>
						<label for="pseudo">Pseudonyme (nom avec lequel vous apparaitrez sur le site) :</label>
						</br>
						<input type="text" name="pseudo" value="<?php echo $pseudEx ?>" id="pseudo" autofocus required/>
						</br>
						</br>
						<!--
						<?php 	/*
								if(isset($_SESSION['tel']))
									$telEx = $_SESSION['tel'];
								else
									$telEx = "";
								*/
						?>
						<label for="tel">Numéro de téléphone (facultatif) :</label>
						</br>
						<input type="tel" name="tel" value="</*?php echo $telEx ?*/>" id="tel"/>
						</br>
						</br>
						-->
						<label for="password">Votre mot de passe. Il doit contenir une majuscule, un chiffre, être long d'au moins 8 charactères et inférieur à 32 charactères: </label>
						</br>
						<input type="password" name="password" id="password" required/>
						</br>
						</br>
						
						<label for="passwordVer">Confirmez le mot de passe :</label>
						</br>
						<input type="password" name="passwordVer" id="password" required/>
						</br>
						</br>
						<!--
						<?php 	
								/*if(isset($_SESSION['birthDate']))
									$birthEx = $_SESSION['birthDate'];
								else
									$birthEx = "";
								*/
						?>
						<label for="birthDate">Entrer votre date de naissance :</label>
						</br>
						<input type="date" name="birthDate" value="</*?php echo $birthEx ?*/>" id="birthDate" required/>
						</br>
						</br>
						-->
						
						<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
						</br>
						<input type="text" name="captcha_code" size="10" maxlength="6" />
						<a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
						</br>
						</br>
						
						<input type="submit" value="Inscription"/>
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