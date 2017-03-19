<?php
namespace ClientQuery;

//Load symfony
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/PSQLDatabase.php';
require_once __DIR__.'/../Datas/Sentence.php';

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use PSQLDatabase;

header("Content-Type:text/plain.txt");
$encoders    = array(new JsonEncoder());
$normalizers = array(new ObjectNormalizer());
$serializer  = new Serializer($normalizers, $encoders);


//Select the action following the idPrompt
$idPrompt    = (isset($_POST["idPrompt"])) ? $_POST["idPrompt"] : NULL;
switch($idPrompt)
{
	//Asking for the sentences
	case 1:
		$idPack   = $_POST["idPack"];
		$idSent   = $_POST["idSent"];
		$prompter = new PSQLDatabase();
		$sent     = $prompter->getFromPackSentences($idPack, $idSent);

		if($sent == -1)
			echo -1;
		else
			echo $serializer->serialize($sent, 'json');
		break;
}
?>
