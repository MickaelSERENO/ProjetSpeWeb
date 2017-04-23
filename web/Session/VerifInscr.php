<?php session_start();?>
<?php
	class LangContent
	{
		public $head;
		public $status_list;
		public $formular;
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
	
	$listStatsText = file_get_contents("../res/lang/fr/VerifInscr_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
	$head = $langData->head;
	$status_list = $langData->status_list;
	$formular = $langData->formular;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title><?php echo ("$head[validate]"); ?></title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="connexion">
				<div class="presentationAcc">
					<h2><?php echo ("$head[titlePage]"); ?></h2>
					<br/>
					
					<div class="statusList">
					<?php
						if(!empty($_GET['statut'])) 
						{
							switch($_GET['statut']) 
							{
								case 'wrong_mail':
									echo "$status_list[wrong_mail]";
									break;
									
								case 'wrong_code':
									echo "$status_list[wrong_code]";
									break;
									
								case 'wrong_captcha':
									echo "$statusList[wrong_captcha]";
									break;
									
								default:
									echo "$statusList[default]";
							}
						}
					?>
					</div>
					<p><?php echo "$formular[not_received]"?></p>
					<form method="post" action="sendCodeAgain.php">
						<label for="code"><?php echo "$formular[mail]";?></label>
						</br>
						<input type="mail" name="mailV" id="mail" required autofocus/>
						</br>
						</br>
						
						<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
						</br>
						<label for="captcha_code"><?php echo "$formular[inscr_captcha]";?></label>
						<input type="text" name="captcha_code" size="10" maxlength="6" />
						<a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
						</br>
						</br>
						<input type="submit" value=<?php echo "\"$formular[resend_button_value]\"";?>/>
					<form/>
					<br/><br/><br/><br/>
					
					<h5><?php echo"$formular[impossible]";?></h5>
					<a href="mailto:askalbatrossensei@gmail.com"><?php echo "$formular[contact]"; ?></a>
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>