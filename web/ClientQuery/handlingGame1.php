<?php session_start();
//SUPERCLAPIER à retirer
$_SESSION["mailProf"] = "prof@scolaire.fr";
$_SESSION['pseudal'] = "User";
$_SESSION['userID'] = 1;?>
<?php

//Load symfony
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/PSQLDatabase.php';
require_once __DIR__.'/../Datas/Sentence.php';

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

header("Content-Type:text/plain.txt");
$encoders    = array(new JsonEncoder());
$normalizers = array(new ObjectNormalizer());
$serializer  = new Serializer($normalizers, $encoders);


//Select the action following the idPrompt
$idPrompt    = (isset($_POST["idPrompt"])) ? $_POST["idPrompt"] : NULL;
error_log($idPrompt);
switch($idPrompt)
{
	//Asking list packs
	case 0:
		$_SESSION['idSent'] = 0;
		$_SESSION['result'] = array();
		$_SESSION['finishG1'] = false;

		$prompter = new PSQLDatabase();
		$packs = $prompter->getListPackGame1($_SESSION["mailProf"]);
		if(!$packs)
			echo -1;
		else
			echo $serializer->serialize($packs, 'json');
		break;

	//Asking for the sentences
	case 1:
		$_SESSION['idSent'] = 0;
		$_SESSION['idPack'] = $_POST["idPack"];
		$idPack   = $_POST["idPack"];
		$prompter = new PSQLDatabase();
		$sent     = $prompter->getFromPackSentences($idPack, $_SESSION['idSent']);

		if(!$sent)
			echo $serializer->serialize(new ReturnNextSent(null, null), 'json');
		else
			echo $serializer->serialize(new ReturnNextSent($sent, null), 'json');

		break;

	//Give results from idPack, idSent and ask next pair of sentences while getting the results of the current one.
	case 2:
		$idPack = $_SESSION["idPack"];
		error_log($_POST['results']);
		$results = json_decode($_POST["results"]);

		//Get result
		array_push($_SESSION['result'], $results);

		$prompter = new PSQLDatabase();
		$_SESSION['idSent']+=1;

		$sent = $prompter->getFromPackSentences($idPack, $_SESSION['idSent']);
		if(!$sent && !$_SESSION['finishG1'])
		{
			$_SESSION['finishG1'] = true;
			$prompter->createHistoricGame1($_SESSION['result'], $_SESSION['userID'], $idPack);
			error_log("finish");
			echo $serializer->serialize(new ReturnNextSent(null, $prompter->getResultFromPackSentences($idPack, $_SESSION['idSent']-1)), 'json');
		}
		else
			echo $serializer->serialize(new ReturnNextSent($sent, $prompter->getResultFromPackSentences($idPack, $_SESSION['idSent']-1)), 'json');
		break;
}
?>
