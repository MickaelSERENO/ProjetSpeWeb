<?php
	class LangContent_Header
	{
		public $txt_Header;
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
	
	$listStatsText = file_get_contents("../res/lang/fr/Header_fr.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContent_Header::class, 'xml');
	$txt_Header = $langData->txt_Header;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" charset="UTF-8">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="SHORTCUT ICON" href="/res/Img/IcoBal.ico">
		<title>Header!</title>
	</head>
	
	<div class="header">
		<header>
			<div class="headerStyle">
				<ul class="menu">
					<li><a class="logoHome" href="/Accueil/Accueil.php">
						<div class="divFade">
							<img class="fade" src="/res/Img/LogoHeaderFade.png" alt="Return to Home" title=<?php echo ("$txt_Header[home]"); ?>/>
							<img class="nofade" src="/res/Img/LogoHeader.png" alt="Return to Home" title=<?php echo ("$txt_Header[home]"); ?>/> 
						</div>
					</a> </li>
					<div class="menuItems">
						<li><a class="linksMenuItems" href="/JeuAccueil/JeuAccueil.php"> <?php echo ("$txt_Header[game_txt]"); ?> </a></li>
						<li><a class="linksMenuItems" href="/Community/HCommunity.html"> <?php echo ("$txt_Header[community_txt]"); ?> </a></li>
					</div>
					<div class="connect">
						<?php
						if(isset($_SESSION['mail']) && isset($_SESSION['verified_user']) && $_SESSION['verified_user'] == 1)
						{
							echo"
							<div class=\"lienConnec\"><li><a class=\"lienConnec\" href=\"/statistics.php\"> $txt_Header[my_account]</a></li></div>
							<div class=\"lienInscr\"><li><a class=\"lienInscr\" href=\"/Session/Disconnect.php\"> $txt_Header[disconnection]</a></li></div>
							";
						}
						else
						{
							echo "
								<div class=\"lienConnec\"><li><a class=\"lienConnec\" href=\"/Session/Connexion.php\"> $txt_Header[connect_txt]</a></li></div>
								<div class=\"lienInscr\"><li><a class=\"lienInscr\" href=\"/Session/Inscription.php\"> $txt_Header[inscr_txt]</a></li></div>
							";
						}
						?>
					</div>
				</ul>
			</div>
		</header>
	</div>
	
</html>
