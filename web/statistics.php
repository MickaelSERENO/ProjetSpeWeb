<?php
?>
<!DOCTYPE>
<html>
	<head>
		<script src="bower_components/angular/angular.min.js"></script>
		<script src="bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<script src="script/stats.js"></script>
	</head>

	<body ng-app="statsApp">
<?php
		class LangContent
		{
			public $listStats;
		}

		//Load symfony
		require_once __DIR__.'/../vendor/autoload.php';

		//Get serializer XML
		use Symfony\Component\Serializer\Serializer;
		use Symfony\Component\Serializer\Encoder\XmlEncoder;
		use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

		$encoders = array(new XmlEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);

		$listStatsText = file_get_contents("../res/lang/fr/statistic.xml");
		$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
		$listStats     = $langData->listStats;

		echo("
			<div ng-controller='listStatsCtrl'>
				<my-statsAccordion>
					<my-statsExpander title='$listStats[listStutends]'>Coucou1</my-statsExpander>
					<my-statsExpander title='$listStats[classement]'>Coucou2</my-statsExpander>
					<my-statsExpander title='$listStats[history]'>Coucou3</my-statsExpander>
				</my-statsAccordion>
			</div>
		");
?>
	</body>
</html>
