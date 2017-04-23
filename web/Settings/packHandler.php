<?php session_start();
$_SESSION['mail'] = "prof@scolaire.fr";
?>
<?php
	if(!isset($_SESSION['mail']))
	{
		header('location: /Session/Connexion.php');
		exit;
	}
?>
<!DOCTYPE>
<html>
	<head>
		<meta charset="utf-8" />
		<script src="../bower_components/angular/angular.min.js"></script>
		<script src="../bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<link rel="stylesheet" type="text/css" href="/CSS/stats.css">
		<link rel="stylesheet" type="text/css" href="/CSS/Accueil.css">
	</head>

	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>

	<body ng-app="statsApp">
<?php
		class LangContent
		{
		}

		//Load symfony
		require_once __DIR__.'/../../vendor/autoload.php';
		require_once __DIR__.'/../ClientQuery/PSQLDatabase.php';

		//Get serializer XML
		use Symfony\Component\Serializer\Serializer;
		use Symfony\Component\Serializer\Encoder\XmlEncoder;
		use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

		$psql = new PSQLDatabase();

		$encoders = array(new XmlEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);

		$listStatsText = file_get_contents("../res/lang/fr/statistic.xml");
		$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
		$listStats     = $langData->listStats;

		$listStutendsHtml = getListStutendsHtml($psql, $langData);
		$rankingHtml      = getRankingHtml();
		$historicHtml     = getHistoricHtml($psql, $langData);
?>
		<br/>
		<div class="backgroundBody">

	<?php include("settingsMenu.php");?>

			<div class="settingsDiv">
			</div>
		</div>
		<br/>
	</body>

	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>

