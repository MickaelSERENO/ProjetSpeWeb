<?php

?>

<!DOCTYPE>
<html>
	<head>
		<script src="bower_components/angular/angular.min.js"></script>
		<script src="bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<link rel="stylesheet" type="text/css" href="stats.css">
		<link rel="stylesheet" type="text/css" href="CSS/Accueil.css">
	</head>

	<header class="headerAcc">
		<?php include('HeaderFooter/Header.inc.php'); ?>
	</header>

	<body ng-app="studCaApp">
<?php
	//The class use to get the XML text from res/lang
	class LangContent
	{
		public $listStats;
		public $listStudents;
		public $historic;
		public $student;
		public $formular;
	}

    function getStudentCara($psql, $langData)
    {
        $studentTxt = $langData->student;
        $studentData = $psql->getStudentCara($_GET['studentID']);

        if($studentData == null)
            return "";

        $result = "<div class=\"studentCara\">
                        <div class=\"studentName\">
                            <div id=\"firstNameStudent\">
                                $studentTxt[firstName] : $studentData->firstName
                            </div>
                            <div>
                                $studentTxt[name] : $studentData->lastName
                            </div>
                        </div>
                        <div class=\"nbGame\">
                            <div>
                                $studentTxt[nbGame1] : $studentData->nbGame1
                            </div>
                            <div>
                                $studentTxt[nbGame2] : $studentData->nbGame2
                            </div>
                        </div>
                   </div>";
        return $result;
    }

	function getHistoricHtml($psql, $langData)
	{
		$historicTxt   = $langData->historic;
		$studentTxt    = $langData->student;
		$historicArray = $psql->getHistoricFromListStudent($_GET['studentID'], "prof@scolaire.fr"); //should be replace by teacher ID
		$result = "
				<div class=\"tableDiv\">
					<table class=\"tableStats\">
						<tr class=\"headerStatsRow\">
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
						{$histo->nbGame1}
					</td>		
					<td>
						{$histo->nbGame2}
					</td>		
				</tr>";
		}

		$result = $result.$rowDataHTML."</table></div>";
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

	$encoders        = array(new XmlEncoder());
	$normalizers     = array(new ObjectNormalizer());
	$serializer      = new Serializer($normalizers, $encoders);

	$listStatsText   = file_get_contents("res/lang/fr/statistic.xml");
	$langData        = $serializer->deserialize($listStatsText, LangContent::class, 'xml');

    $studentDataHtml = getStudentCara($psql, $langData);
	$historicHtml    = getHistoricHtml($psql, $langData);

	echo "
			<br/>
			<div class=\"backgroundBody\">
				$studentDataHtml
				$historicHtml
			</div>
			<br/>";
?>
	</body>

	<footer>
		<?php include('HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>
