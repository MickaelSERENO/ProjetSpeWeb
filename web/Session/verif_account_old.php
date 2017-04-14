<?php session_start();?>
<?php
	
	//Load symfony
	require_once __DIR__.'/../../vendor/autoload.php';
	require_once __DIR__.'/PSQLDatabase.php';
	require_once __DIR__.'/../Datas/Sentence.php';

	use Symfony\Component\Serializer\Serializer;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use PSQLDatabase;
	
	/*Managing wrong captcha: http://www.phpcaptcha.org/documentation/quickstart-guide/ */
	include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
	$securimage = new Securimage();

	if ($securimage->check($_POST['captcha_code']) == false) 
	{
	  // the captcha code was incorrect
	  header('location: VerifInscr.php?statut=wrong_captcha');
	  exit;
	}
	
	/*Deleting any code that could be inserted in those fields*/
	$_POST['code'] = htmlspecialchars($_POST['code']);
	
	
	if (empty($_POST['code']))
	{
		header('location: VerifInscr.php?statut=code_missing');
		$_SESSION['verifTry'] = true;
		exit;
	}
	else
	{
		$prompter = new PSQLDatabase();
		if(!(prompter->compare_code($_SESSION['mail'], $input_code)))
		{
			header('location: VerifInscr.php?statut=incorrect_code');
			$_SESSION['verifTry'] = true;
			exit;
		}
		else
		{
			$prompter->verifyTeacherClass($mail);
			
			header('location: InscriptionReussie.php');
		}
		
	}
?>