<?php session_start();?>
<?php
	
	//Load symfony
	require_once __DIR__.'/../../vendor/autoload.php';
	require_once __DIR__.'/../ClientQuery/PSQLDatabase.php';
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
	$mail = htmlspecialchars($_POST['mailV']);
	
	$prompter = new PSQLDatabase();
	if (empty($mail) || !($prompter->existMail($mail)))
	{
		header('location: mailResent.php');
		exit;
	}
	else
	{
		$code=substr(md5(mt_rand()),0,15);
		$prompter->updateVerifCode($mail, $code);
		
		//Envoi du code d'activation par mail
		//SUPERCLAPIER: modifier l'adresse de la page si on a un nom de domaine
		$to=$mail;
		$subject="Code d'activationpour AlbatrosSensei.fr";
		$from = 'noreplyAlbasensei@gmail.com';
		$body='Votre code d\'activation de compte pour le site Albatros Sensei est '.$code.'\n
		Merci de cliquer sur le lien ci dessous pour activer votre compte:\n
		<a href="spamhost/Session/verif_account.php">verify.php?mail='.$mail.'&code='.$code.'</a> pour activer votre compte.\n
		Merci pour votre confiance.\n 
		L\'équipe d\'administration du site\n';
		$headers = "From:".$from;
		mail($to,$subject,$body,$headers);
		
		header('location: mailResent.php');
		exit;
		
	}
?>