<?php
?>
<!DOCTYPE>
<html>
	<head>
		<meta charset="utf-8" />
		<script src="bower_components/angular/angular.min.js"></script>
		<script src="bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<script src="script/stats.js"></script>
		<link rel="stylesheet" type="text/css" href="stats.css">
		<link rel="stylesheet" type="text/css" href="CSS/Accueil.css">
	</head>

	<header class="headerAcc">
		<?php include('HeaderFooter/Header.inc.php'); ?>
	</header>

	<body ng-app="statsApp">
<?php
		class LangContent
		{
			public $listStats;
			public $listStudents;
			public $historic;
			public $student;
			public $formular;
		}

		function getListStutendsHtml($psql, $langData)
		{
			$listStudentsTxt = $langData->listStudents;
			$studentTxt      = $langData->student;
			$formularTxt     = $langData->formular;
			$listStudents    = $psql->getAllFromListStudents("prof@scolaire.fr"); /*should be replace by the prof ID*/
			$result = 
				"<form>
					<input type=\"submit\">
					<input type=\"text\" value=\"search\">
				</form>

				<div class=\"tableDiv\">
					<table class=\"tableStats\">
						<tr class=\"headerStatsRow\">
							<th>
								$studentTxt[firstName]
							</th>
							<th>
								$studentTxt[name]
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
				$rowDataHTML = $rowDataHTML.
						"<tr class=\"statsRow\" ng-value=\"$stud->id\" ng-click=\"onRowStudentClick('$stud->id', \$event)\">
							<td>
								{$stud->firstName}
							</td>		

							<td>
								{$stud->lastName}
							</td>		

							<td>
								{$stud->nbGame1}
							</td>		

							<td>
								{$stud->nbGame2}
							</td>		
						</tr>";
			}

			$result = $result.$rowDataHTML.
					"</table>

				</div>

				<form class=\"addStudent\">
					<input type=\"submit\" value=\"$formularTxt[addUser]\">
				</form>";
			return $result;
		}

		function getRankingHtml()
		{
			return "";
		}

		function getHistoricHtml($psql, $langData)
		{
			$historicTxt   = $langData->historic;
			$studentTxt    = $langData->student;
			$historicArray = $psql->getHistoricFromListStudent("prof@scolaire.fr"); //should be replace by teacher ID
			$result = "
					<table class=\"tableStats\">
						<tr class=\"headerStatsRow\">
							<th>
								$studentTxt[firstName]
							</th>
							<th>
								$studentTxt[name]
							</th>
							<th>
								$historicTxt[idGame]
							</th>
							<th>
								$historicTxt[date]
							</th>
						</tr>";
			$rowDataHTML = "";
			foreach($historicArray as $histo)
			{
				$rowDataHTML = $rowDataHTML.
					"<tr class=\"statsRow\" ng-value=\"$histo->id\" ng-click=\"onRowHistoricClick($histo->id, \$event)\">
						<td>
							{$histo->firstName}
						</td>		
						<td>
							{$histo->lastName}
						</td>		
						<td>
							{$histo->nbGame1}
						</td>		
						<td>
							{$histo->nbGame2}
						</td>		
					</tr>";
			}

			$result = $result.$rowDataHTML."</table>";
			return $result;
		}

		//Load symfony
		require_once __DIR__.'/../vendor/autoload.php';
		require_once __DIR__.'/ClientQuery/PSQLDatabase.php';

		//Get serializer XML
		use Symfony\Component\Serializer\Serializer;
		use Symfony\Component\Serializer\Encoder\XmlEncoder;
		use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

		$psql = new PSQLDatabase();

		$encoders = array(new XmlEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);

		$listStatsText = file_get_contents("res/lang/fr/statistic.xml");
		$langData      = $serializer->deserialize($listStatsText, LangContent::class, 'xml');
		$listStats     = $langData->listStats;

		$listStutendsHtml = getListStutendsHtml($psql, $langData);
		$rankingHtml      = getRankingHtml();
		$historicHtml     = getHistoricHtml($psql, $langData);

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

	<footer>
		<?php include('HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>
