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

	$_POST['pseudo'] = htmlspecialchars($_POST['pseudo']);
	$_POST['password'] = htmlspecialchars($_POST['password']);
	
	if (empty($_POST['pseudo']))
	{
		header('location: Connexion.php?statut=pseudo_missing');
		exit;
	}
	else if (empty($_POST['password']))
	{
		header('location: Connexion.php?statut=password_missing');
		exit;
	}
	else
	{
		$pseudal=$_POST['pseudo'];
		$passhash=$_POST['password'];
		
		$prompter = new PSQLDatabase();
		
		if($prompter->existPseudo($pseudal))
		{
			if($prompter->cmpPassHashPseudal($passhash, $pseudal))
			{
				/*Récupération de la session dans la BD*/
				$_SESSION['verified_user']=$prompter->isVerifiedUserPseudal($pseudal);
				$_SESSION['pseudo']=$pseudal;
				$_SESSION['mail']=$prompter->getMailFromPseudal($pseudal);
				
				if($_SESSION['verified_user']==1)
					header('location: /Accueil/Accueil.php');
				else if($_SESSION['verified_user']==0)
					header('location: VerifInscr.php?statut=non_verified_user');
			}
			else
			{
				header('location: Connexion.php?statut=wrong_password');
				exit;
			}
		}
		else if($prompter->existMail($pseudal))
		{
			if($prompter->cmpPassHashMail($passhash, $pseudal))
			{
				/*Récupération de la session dans la BD*/
				$_SESSION['verified_user']=$prompter->isVerifiedUserPseudal($pseudal);
				$_SESSION['mail']=$pseudal;
				$_SESSION['pseudal']=$prompter->getPseudalFromMail($pseudal);
				
				if($_SESSION['verified_user']==1)
					header('location: /Accueil/Accueil.php');
				else if($_SESSION['verified_user']==0)
					header('location: VerifInscr.php?statut=non_verified_user');
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