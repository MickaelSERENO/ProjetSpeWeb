<?php session_start();
<?php

<?php
	/*Managing wrong captcha: http://www.phpcaptcha.org/documentation/quickstart-guide/*/
	include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
	$securimage = new Securimage();

	if ($securimage->check($_POST['captcha_code']) == false) 
	{
	  // the captcha code was incorrect
	  header('location: Connexion.php?statut=wrong_captcha');
	  exit;
	}
	
	$_POST['pseudo'] = htmlspecialchars($_POST['pseudo']);
	$_POST['password'] = htmlspecialchars($_POST['password']);
	
	if (empty($_POST['pseudo']))
	{
		header('location: Connexion.php?statut=pseudo_missing');
		exit;
	}
	else if ((empty($_POST['password']) || (empty($_POST['passwordVer']))))
	{
		header('location: Connexion.php?statut=password_missing');
		exit;
	}
	else
	{
		$Recordusers = file_get_contents('Secured/pseudaHash.txt');
		$RecordusersArray=explode("\n", $Recordusers);
		
		$ExistentUserPseudal = false;
		$passwordHash = "";
		
		foreach ($Recordusersarray as $Identification)
		{
			$split = explode(":", $Identification);
			if ($split[0] == $_POST['pseudo'])
			{
				$ExistentUserPseudal = true;
				$passwordHash = $split[1];
				break;
			}
		}
		if($ExistentUserPseudal)
		{
			if(password_verify($_POST['pseudo'], passwordHash))
			{
				$_SESSION['verified_user']=1;
				$_SESSION['pseudo']=$POST['pseudo'];
				/*Récupération de la session dans la BD*/
			}
			else
			{
				header('location: Connexion.php?statut=wrong_password');
				exit;
			}
		}
		else
		{
			header('location: Connexion.php?statut=wrong_pseudo');
			exit;
		}
	}
?>