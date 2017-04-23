<?php session_start();?>
<?php
	class LangContent
	{
		public $head;
		public $status_list;
		public $txt_inscr;
	}
	
	//Load symfony
	require_once __DIR__.'/../../vendor/autoload.php';
	require_once __DIR__.'/../ClientQuery/PSQLDatabase.php';

	//Get serializer XML
	use Symfony\Component\Serializer\Serializer;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	
	$encoders = array(new XmlEncoder());
	$normalizers = array(new ObjectNormalizer());
	$serializer = new Serializer($normalizers, $encoders);
	
	$listStatsText = file_get_contents("../res/lang/fr/Inscription_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
	$head = $langData->head;
	$status_list = $langData->status_list;
	$txt_inscr = $langData->txt_inscr;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title><?php echo("$head[title]"); ?></title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="connexion">
				<div class="presentationAcc">
					</br>
					
					<h3>
						<?php echo"$txt_inscr[inscr_title]";?>
					</h3>
					</br>
					
					<div class="statusList">
					<?php
					echo"\r\n";
					echo"\r\n";
					echo"\r\n";
					
					if(!empty($_GET['statut'])) 
					{
						switch($_GET['statut']) 
						{
							case 'wrong_captcha':
								echo"$status_list[wrong_captcha]";
								break;
							
							case 'mail_missing':
								echo"$status_list[mail_missing]";
								break;
								
							case 'pseudo_missing':
								echo"$status_list[pseudo_missing]";
								break;
							
							case 'password_missing':
								echo"$status_list[password_missing]";
								break;
							/*	
							case 'birth_missing':
								echo 'Votre date de naissance a un format incorrext où n\'a pas été rentrée';
								break;
							*/
							case 'mail_different':
								echo "$status_list[mail_different]";
								break;
								
							case 'password_different':
								echo "$status_list[password_different]";
								break;
								
							case 'not_secured_length':
								echo"$status_list[not_secured_length]";
								break;
								
							case 'not_secured':
								echo"$status_list[not_secured]";
								break;
								
							case 'existing_mail':
								echo "$status_list[existing_mail]";
								break;
							
							case 'user_existing':
								echo "$status_list[user_existing]";
								break;
								
							case 'mail_existing':
								echo "$status_list[mail_existing]";
								break;
								
							default:
								echo"$status_list[default]";
						}
					}
					?>
					</div>
					</br></br>
					<form method="post" action="inscr_form.php">
						
						<?php 	if(isset($_SESSION['mail']))
									$mailEx = $_SESSION['mail'];
								else
									$mailEx = "example@domain.com";
						?>
						<label for="mail"><?php echo "$txt_inscr[inscr_mail]"?></label>
						</br>
						<input type="email" name="mail" value="<?php echo $mailEx ?>" id="mailInscr" autofocus required/>
						</br>
						</br>
						
						<label for="mailVer"><?php echo "$txt_inscr[inscr_pseudo]"?></label>
						</br>
						<input type="email" name="mailVer" id="mailInscr" required/>
						</br>
						</br>
						
						<?php 	if(isset($_SESSION['pseudo']))
									$pseudEx = $_SESSION['pseudo'];
								else
									$pseudEx = "";
						?>
						<label for="pseudo"><?php echo "$txt_inscr[inscr_pseudo]"?></label>
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
						<label for="password"><?php echo "$txt_inscr[inscr_pass]"?> </label>
						</br>
						<input type="password" name="password" id="password" required/>
						</br>
						</br>
						
						<label for="passwordVer"><?php echo "$txt_inscr[inscr_repass]"?></label>
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
						<br/>
						<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
						</br>
						<label for="captcha_code"><?php echo "$txt_inscr[inscr_captcha]"?></label>
						
						
						<input type="text" name="captcha_code" size="10" maxlength="6" />
						<a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
						</br>
						</br>
						
						<input type="submit" value=<?php echo ("\"$txt_inscr[inscr_value_submit_button]\""); ?>/>
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