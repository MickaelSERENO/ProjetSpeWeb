<?php session_start();
//SUPERCLAPIER à retirer
$_SESSION["mailProf"] = "prof@scolaire.fr";?>

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
switch($idPrompt)
{
	//Asking list packs
	case 0:
		$prompter = new PSQLDatabase();
		$packs = $prompter->getListPackGame1($_SESSION["mailProf"]);
		if(!$packs)
			echo -1;
		else
			echo $serializer->serialize($packs, 'json');
		break;

	//Asking for the sentences
	case 1:
		$idPack   = $_POST["idPack"];
		$idSent   = $_POST["idSent"];
		$prompter = new PSQLDatabase();
		$sent     = $prompter->getFromPackSentences($idPack, $idSent);

		if(!$sent)
			echo -1;
		else
			echo $serializer->serialize($sent, 'json');
		break;

	//Give results from idPack, idSent and ask next pair of sentences
	case 2:
		$idPack = $_POST["idPack"];
		$currentIDSent = $_POST["idSent"];
		$results = json_decode($_POST["results"]);

		$prompter = new PSQLDatabase();
		$prompter->commitGame1ResultsCookies($idPack, $currentIDSent, $results);

		$sent = $prompter->getFromPackSentences($idPack, $currentIDSent);
		if(!$sent)
		{
			//TODO Get the user ID (replace User by the user ID
			$prompter->createHistoricGame1("User", $idPack);
			echo $currentIDSent+1;
		}
		else
			echo $serializer->serialize($sent, 'json');
		break;
}
?>
