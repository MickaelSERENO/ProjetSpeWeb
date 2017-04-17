<?php session_start();?>
<?php
	
	//Load symfony
	require_once __DIR__.'/../../vendor/autoload.php';
	require_once __DIR__.'/ClientQuery/PSQLDatabase.php';
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
	$code = htmlspecialchars($_GET['code']);
	
	
	
	if(isset($GET['mail']) && isset($GET['code']))
	{
		$mail = htmlspecialchars($_GET['mail']);
		$code = htmlspecialchars($_GET['code']);
		
		$prompter = new PSQLDatabase();
		
		if(!(prompter->existMail($mail)))
		{
			header('location: VerifInscr.php?statut=wrong_mail');
			exit;
		}
		else if(!(prompter->compare_code($mail, $code)))
		{
			header('location: VerifInscr.php?statut=wrong code');
			exit;
		}
		else
		{
			$prompter->verifyTeacherClass($mail);
			header('location: InscriptionReussie.php');
		}
		
	}
?>