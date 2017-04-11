<?php session_start();?>
<?php
	
	/*Deleting any code that could be inserted in those fields*/
	$_POST['pseudo'] = htmlspecialchars($_POST['pseudo']);
	$_POST['mail'] = htmlspecialchars($_POST['mail']);
	$_POST['mailVer'] = htmlspecialchars($_POST['mailVer']);
	$_POST['password'] = htmlspecialchars($_POST['password']);
	$_POST['passwordVer'] = htmlspecialchars($_POST['passwordVer']);
	$_POST['tel'] = htmlspecialchars($_POST['tel']);
	
	/*Saving some of the fields so the user doesn't have to retype everything if he made a mistake*/
	$_SESSION['mail'] = $_POST['mail'];
	$_SESSION['pseudo'] = $_POST['pseudo'];
	if(isset($_POST['tel']))
		$_SESSION['tel'] = $_POST['tel'];
	$_SESSION['birthDate'] = $_POST['birthDate'];
	
	/*Managing wrong captcha: http://www.phpcaptcha.org/documentation/quickstart-guide/*/
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
	else if((empty($_POST['birthDate'])))
	{
		header('location: Inscription.php?statut=birth_missing');
		exit;
	}
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
		
		$passHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		$Recordusers = file_get_contents('Secured/pseudaList.txt');
		
		/*Cette ligne nous donne un tableau avec des cases de la forme pseudo:mail@host.cc*/
		$Recordusersarray = explode ("\n", $Recordusers);
		
		$ExistentUserMail = false;
		$ExistentUserPseudal = false;
		
		foreach ($Recordusersarray as $Identification)
		{
			$split = explode(":", $Identification);
			if ($split[0] == $_POST['pseudo'])
			{
				$ExistentUserPseudal = true;
				break;
			}
			if($split[1] == $_POST['mail'])
			{
				$ExistentUserMail = true;
				break;
			}	
		}

		if($ExistentUserMail)
			header('location: Inscription.php?statut=mail_existing');
		else if($ExistentUserPseudal)
			header('location: Inscription.php?statut=user_existing');
		else
		{
			$f = file_get_contents("Secured/pseudHash.txt");
			$g = $f.$_POST['pseudo'].":".$passHash."\n";
			
			$addNewUser = $Recordusers.$_POST['pseudo'].':'.$_POST['mail']."\n";
			
			$f = file_put_contents('Secured/pseudHash.txt', $g);
			$Recordusers = file_put_contents('Secured/pseudalist.txt', $addNewUser);
			header('location: InscriptionReussie.php');
			
		}
		
	}
?>