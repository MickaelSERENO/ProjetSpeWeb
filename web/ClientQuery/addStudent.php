<?php session_start();
$_SESSION["mail"] = "prof@scolaire.fr";
?>

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

	$nameStudent = $_POST["nameStudent"];
	$surnameStudent = $_POST["surnameStudent"];
	$password = $_POST["password"];


	if(strlen($password) < 8)
	{
		echo -2;
	}
	else
	{
		$database = new PSQLDatabase();
		$addError = $database->addStudent($_SESSION['mail'], $nameStudent, $surnameStudent, $password);

		echo $addError;
	}
?>
