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
	
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<h1 class="acc"> <?php echo ("$txt_acc[phrase_acc]"); ?> </h1>
			<section class="presentationAcc">
				<h3>
					<?php  ?>
				</h3>
				<p>
					Ce site web a pour vocation d'améliorer la compréhension de certaines langues en proposant des jeux basés sur la compréhension du langage.
				<p>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>
