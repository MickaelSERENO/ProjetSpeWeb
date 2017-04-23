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
	
	$listStatsText = file_get_contents("../res/lang/fr/Connexion_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
	
	$head = $langData->head;
	$status_list = $langData->status_list;
	$formular = $langData->formular;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="/res/Img/IcoBal.ico">
		<title><?php echo ("$head[title]"); ?></title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="presentationAcc">
				<div class="connexion">
					<div class="statusList">
					<?php
						if(!empty($_GET['statut'])) 
						{
				 
							switch($_GET['statut']) 
							{
								case 'wrong_captcha':
									echo "$status_list[wrong_captcha]";
									break;
									
								case 'wrong_pseudo':
									echo "$status_list[wrong_pseudo]";
									break;
								
								case 'wrong_password':
									echo "$status_list[wrong_password]";
									break;
									
								default:
									echo "$status_list[default]";
							}
						}
					?>
					</div>
					<form method="post" action="connec_status.php">
						<h2>
							<?php echo ("$formular[connect_title]"); ?>
						</h2>
						<br/>
						<label for="pseudo"><?php echo ("$formular[connect_pseudo]"); ?></label>
						</br>
						<input type="text" name="pseudo" id="pseudo" autofocus required/>
						</br>
						</br>
						<label for="passmot"><?php echo ("$formular[connect_pass]"); ?> :</label>
						</br>
						<input type="password" name="password" id="password" required/>
						</br>
						</br>
						
						<input type="submit" value=<?php echo ("\"$formular[connect_value_submit_button]\""); ?>/>
					<form/>
					
					<a href="forgottenPass.php"> <?php echo ("$formular[forgot_pass]"); ?></a>
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>