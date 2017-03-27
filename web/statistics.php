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
			public $listStudents;
			public $historic;
		}

		function getListStutendsHtml($psql, $langData)
		{
			$listStudentsTxt = $langData->listStudents;
			$listStudents    = $psql->getAllFromListStudents(1); /*1 should be replace by the prof ID*/
			$result = 
				"<form>
					<input type=\"submit\">
					<input type=\"text\" value=\"search\">

					<input type=\"submit\">
					<input type=\"text\" value=\"Add\">
				</form>
				<table class=\"tableStats\">
					<tr class=\"headerStatsRow\">
						<th>
							$listStudentsTxt[name]
						</th>
						<th>
							$listStudentsTxt[nbGame1]
						</th>
						<th>
							$listStudentsTxt[nbGame2]
						</th>
					</tr>";
			$rowDataHTML = "";
			foreach($listStudents as $stud)
			{
				$rowDataHTML += 
					"<tr class=\"statsRow\">
						<td>
							{$stud->id}
						</td>		

						<td>
							{$stud->nbGame1}
						</td>		

						<td>
							{$stud->nbGame2}
						</td>		
					</tr>";
			}

			$result += $rowDataHTML + "</table>";
			return $result;
		}

		function getRankingHtml()
		{
			return "";
		}

		function getHistoricHtml($langData)
		{
			$historic = $langData->historic;
			return "
					<table class=\"tableStats\">
						<tr class=\"headerStatsRow\">
							<th>
								$historic[name]
							</th>
							<th>
								$historic[idGame]
							</th>
							<th>
								$historic[date]
							</th>
						</tr>
					</table>";
			return "";
		}

		//Load symfony
		require_once __DIR__.'/../vendor/autoload.php';
		require_once __DIR__.'/ClientQuery/PSQLDatabase.php';

		//Get serializer XML
		use Symfony\Component\Serializer\Serializer;
		use Symfony\Component\Serializer\Encoder\XmlEncoder;
		use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
		use PSQLDatabase;

		$psql = new PSQLDatabase();

		$encoders = array(new XmlEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);

		$listStatsText = file_get_contents("../res/lang/fr/statistic.xml");
		$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
		$listStats     = $langData->listStats;

		$listStutendsHtml = getListStutendsHtml($psql, $langData);
		$rankingHtml      = getRankingHtml();
		$historicHtml     = getHistoricHtml($langData);

		echo("
			<div ng-controller='listStatsCtrl'>
				<my-statsAccordion>
					<my-statTabItem title=\"$listStats[listStudents]\"></my-statTabItem>
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
