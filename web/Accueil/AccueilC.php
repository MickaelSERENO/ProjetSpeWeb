<?php session_start();?>
<?php
	class LangContent
	{
		public $txt_acc;
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
	
	$listStatsText = file_get_contents("../res/lang/fr/Accueil_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
	
	
	$txt_acc = $langData->txt_acc;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="/res/Img/IcoBal.ico">
		<title><?php echo ("$txt_acc[title]"); ?></title>
	</head>
	<br/>
	<header class="headerAcc">
		<div class="logoAndHead">
			<br/><br/><br/><br/><br/><br/>
		</div>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
			<!--<section class="presentationAcc">
				<h3>
					<br/>
					<br/>
					<br/>
					<br/>
					
				</h3>
				<p>
					<br/>
					<br/>
					<br/>
					<br/>
				<p>
			</section> -->
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>
