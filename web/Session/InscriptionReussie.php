<?php session_start();?>
<?php
	class LangContent
	{
		public $head;
		public $body;
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
	
	$listStatsText = file_get_contents("../res/lang/fr/InscriptionReussie_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
	
	$head = $langData->head;
	$body = $langData->body

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title><?php echo ("$head[title]"); ?></title>
	</head>
										
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<section class="connexion">
				<div class="presentationAcc">
					<h2><?php echo ("$body[terminated]"); ?></h2>
					<br/>
					<h3>
						<?php echo ("$body[welcome]"); ?>
						<br/>
						<?php echo ("$body[access]"); ?>
					</h3>
					<br/>
					
				</div>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>