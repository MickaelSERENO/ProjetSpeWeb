<head>
	<link rel="stylesheet" type="text/css" href="/CSS/menuSettings.css">
</head>

<?php 
	class LangContentMenu
	{
		public $menu;
	}

	//Get serializer XML
	use Symfony\Component\Serializer\Serializer;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

	$psql = new PSQLDatabase();

	$encoders = array(new XmlEncoder());
	$normalizers = array(new ObjectNormalizer());
	$serializer = new Serializer($normalizers, $encoders);

	$listStatsText = file_get_contents("../res/lang/fr/menuSettings.xml");
	$langData      = $serializer->deserialize($listStatsText, LangContentMenu::class, 'xml');

	$menuTxt = $langData->menu;
?>

<div id="menuSettings">
	<ul>
		<li><a href="/Settings/statistics.php"><?php echo "$menuTxt[statistics]";?></a></li>
		<li><a href="/Settings/packHandler.php"><?php echo "$menuTxt[packHandler]";?></a></li>
		<li><a href="/Settings/personalSettings.php"><?php echo "$menuTxt[settings]";?></a></li>
	</ul>
</div>
