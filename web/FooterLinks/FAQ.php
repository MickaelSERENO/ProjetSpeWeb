<?php session_start(); ?>
<!DOCTYPE html>
<?php
	class LangContent
	{
		public $txt_Faq;
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
	
	$listStatsText = file_get_contents("../res/lang/fr/FAQ_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
	$txt_Faq = $langData->txt_Faq;

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title><?php echo"$txt_Faq[title]";?></title>
	</head>
	
	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>
	
	<body>
	<br/>
		<div class="backgroundBody">
			<h1 class="acc"> <?php echo"$txt_Faq[faq_pres]";?> </h1>
			<section class="Faq">
				<h3>
					<?php echo"$txt_Faq[goal_quest]";?>
				</h3>
				<p>
					<?php echo"$txt_Faq[goal_answ]";?>
				<p>
				<h3>
					<?php echo"$txt_Faq[inscr_quest]";?>
				</h3>
				<p>
					<?php echo"$txt_Faq[inscr_answ]";?>
				<p>
				<h3>
					<?php echo"$txt_Faq[connect_error]";?>
				</h3>
				<p>
					<?php echo"$txt_Faq[connect_error_ans_01]";?> 
					<a href="mailto:askalbatrossensei@gmail.com"><?php echo"$txt_Faq[connect_error_ans_link]";?></a>.
					<?php echo"$txt_Faq[connect_error_ans_02]";?> 
				<p>
			</section>
		</div>
	<br/>
	</body>
	
	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>