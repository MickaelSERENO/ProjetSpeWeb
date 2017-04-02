<?php

?>

<!DOCTYPE>
<html>
	<head>
		<script src="bower_components/angular/angular.min.js"></script>
		<script src="bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
	</head>

	<header class="headerAcc">
		<?php include('HeaderFooter/Header.inc.php'); ?>
	</header>

	<body ng-app="studChaApp">
<?php
	//The class use to get the XML text from res/lang
	class XMLText
	{
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

	$listStatsText = file_get_contents("../res/lang/fr/statistic.xml");
?>
	</body>

	<footer>
		<?php include('HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>
