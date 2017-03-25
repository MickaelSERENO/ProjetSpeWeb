<?php
?>
<!DOCTYPE>
<html>
	<head>
		<script src="bower_components/angular/angular.min.js"></script>
		<script src="bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<script src="script/stats.js"></script>
		<link rel="stylesheet" type="text/css" href="stats.css">
	</head>

	<body ng-app="statsApp">
<?php
		class LangContent
		{
			public $listStats;
			public $historic;
		}

		function getListStutendsHtml($langData)
		{
			$historic = $langData->historic;
			return "<form>
						<input type=\"submit\">
						<input type=\"text\" value=\"search\">
					</form>
					<table>
						<tr>
							<th>
								$historic[name]
							</th>
							<th>
								$historic[grade]
							</th>
							<th>
								$historic[nbGame1]
							</th>
							<th>
								$historic[nbGame2]
							</th>
						</tr>
					</table>";
		}

		function getRankingHtml()
		{
			return "";
		}

		function getHistoricHtml()
		{
			return "";
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

		$listStutendsHtml = getListStutendsHtml($langData);
		$rankingHtml      = getRankingHtml();
		$historicHtml     = getHistoricHtml();

		echo("
			<div ng-controller='listStatsCtrl'>
				<my-statsAccordion>
					<my-statTabItem title=\"$listStats[listStutends]\"></my-statTabItem>
					<my-statTabItem title=\"$listStats[ranking]\"></my-statTabItem>
					<my-statTabItem title=\"$listStats[historic]\"></my-statTabItem>

					<my-statTabContent>$listStutendsHtml</my-statTabContent>
					<my-statTabContent>$rankingHtml</my-statTabContent>
					<my-statTabContent>$historicHtml</my-statTabContent>
				</my-statsAccordion>
			</div>
		");
?>
	</body>
</html>
