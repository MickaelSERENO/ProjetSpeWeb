<?php
	class LangContent_Footer
	{
		public $txt_Footer;
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
	
	$listStatsText = file_get_contents("../res/lang/fr/Footer_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent_Footer::class, 'xml');
	$txt_Footer = $langData->txt_Footer;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="../../res/Img/IcoBal.ico">
		<title>Footer!</title>
	</head>
	
	<div class="footerClass">
		<div><a href="../FooterLinks/FAQ.php"> <?php echo ("$txt_Footer[faq_title]");?></a></div>
		<div><a href="mailto:askalbatrossensei@gmail.com"><?php echo "$txt_Footer[contact_txt]";?></a></div>
		<div><a href="../FooterLinks/Infos.php"><?php echo "$txt_Footer[Infos_title]";?></a></div>
	</div>

</html>