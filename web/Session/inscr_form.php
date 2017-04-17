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
	
	/*Deleting any code that could be inserted in those fields*/
	$_POST['pseudo'] = htmlspecialchars($_POST['pseudo']);
	$_POST['mail'] = htmlspecialchars($_POST['mail']);
	$_POST['mailVer'] = htmlspecialchars($_POST['mailVer']);
	$_POST['password'] = htmlspecialchars($_POST['password']);
	$_POST['passwordVer'] = htmlspecialchars($_POST['passwordVer']);
	/*$_POST['tel'] = htmlspecialchars($_POST['tel']);*/
	
	/*Saving some of the fields so the user doesn't have to retype everything if he made a mistake*/
	$_SESSION['mail'] = $_POST['mail'];
	$_SESSION['pseudo'] = $_POST['pseudo'];
	/*if(isset($_POST['tel']))
		$_SESSION['tel'] = $_POST['tel'];
	$_SESSION['birthDate'] = $_POST['birthDate'];*/
	
	/*Managing wrong captcha: http://www.phpcaptcha.org/documentation/quickstart-guide/ */
	include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
	$securimage = new Securimage();

	if ($securimage->check($_POST['captcha_code']) == false) 
	{
	  // the captcha code was incorrect
	  header('location: Inscription.php?statut=wrong_captcha');
	  exit;
	}

	if ((empty($_POST['mail']) || (empty($_POST['mailVer']))))
	{
		header('location: Inscription.php?statut=mail_missing');
		exit;
	}
	else if (empty($_POST['pseudo']))
	{
		header('location: Inscription.php?statut=pseudo_missing');
		exit;
	}
	else if ((empty($_POST['password']) || (empty($_POST['passwordVer']))))
	{
		header('location: Inscription.php?statut=password_missing');
		exit;
	}
	/*else if((empty($_POST['birthDate'])))
	{
		header('location: Inscription.php?statut=birth_missing');
		exit;
	}*/
	else if (($_POST['mail']) != ($_POST['mailVer']))
	{
		header('location: Inscription.php?statut=mail_different');
		exit;
	}
	else if (($_POST['password']) != ($_POST['passwordVer']))
	{
		header('location: Inscription.php?statut=password_different');
		exit;
	}
	else
	{
		if(strlen($_POST['password'])<8 || strlen($_POST['password']>32))
		{
			header('location: Inscription.php?statut=not_secured_length');
			exit;
		}
		$arrPass = str_split($_POST['password']);
		$maj = false;
		$number=false;
		foreach($arrPass as $char)
		{
			if(ord($char)>64 && ord($char)<91)
				$maj=true;
			else if(ord($char)>47 && ord($char)<58)
				$number=true;
		}
		if(!$maj || !$number)
		{
			header('location: Inscription.php?statut=not_secured');
			exit;
		}
		
		$passHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
		
		$ExistentUserMail = false;
		$ExistentUserPseudal = false;
		
		$prompter = new PSQLDatabase();
	
		if ($prompter->existPseudo($_POST['pseudo']))
		{
			$ExistentUserPseudal = true;
		}
		if($prompter->existMail($_POST['mail']))
		{
			$ExistentUserMail = true;
		}	

		if($ExistentUserMail)
			header('location: Inscription.php?statut=mail_existing');
		else if($ExistentUserPseudal)
			header('location: Inscription.php?statut=user_existing');
		else
		{	
			//SUPERCLAPIER: Inutile?
			$Recordusers = file_get_contents('Secured/pseudaList.txt');
			$addNewUser = $Recordusers.$_POST['pseudo'].':'.$_POST['mail'].':'.$_POST['passHash']."\n";
			$Recordusers = file_put_contents('Secured/pseudalist.txt', $addNewUser);
			
			$code=substr(md5(mt_rand()),0,15);
			
			$prompter->registerTeacherClass($_POST['mail'], $_POST['pseudo'], $passHash, false, $code);
			
			//Envoi du code d'activation par mail
			//SUPERCLAPIER: modifier l'adresse de la page si on a un nom de domaine
			$to=$_POST['mail'];
			$subject="Code d'activationpour AlbatrosSensei.fr";
			$from = 'noreplyAlbatrossensei@gmail.com';
			$body='Votre code d\'activation de compte pour le site Albatros Sensei est '.$code.'\n
			Merci de cliquer sur le lien ci dessous pour activer votre compte:\n
			<a href="localhost/Session/verif_account.php">verify.php?mail='.$_POST['mail'].'&code='.$code.'<a> pour activer votre compte.\n
			Merci pour votre confiance.\n 
			L\'équipe d\'administration du site\n';
			$headers = "From:".$from;
			mail($to,$subject,$body,$headers);
			
			
			header('location: VerifInscr.php');
		}
		
	}
?>