<?php session_start();?>
<?php
	//Load symfony
	require_once __DIR__.'/../../vendor/autoload.php';
	require_once __DIR__.'/../ClientQuery/PSQLDatabase.php';

	use Symfony\Component\Serializer\Serializer;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use PSQLDatabase;

	/*Deleting any code that could be inserted in those fields*/
	$_POST['passwd'] = htmlspecialchars($_POST['passwd']);
	$_POST['confirmPasswd'] = htmlspecialchars($_POST['confirmPasswd']);

	if((empty($_POST['passwd']) || (empty($_POST['confirmPasswd']))))
	{
		header("location: studentCaracteristics.php?studentID=$_POST[studentID]&statut=empty");
		exit;
	}

	else if($_POST['passwd'] != $_POST['confirmPasswd'])
	{
		header("location: studentCaracteristics.php?studentID=$_POST[studentID]&statut=different");
		exit;
	}

	else if(strlen($_POST['passwd'])<8 || strlen($_POST['passwd']>32))
	{
		header("location: studentCaracteristics.php?studentID=$_POST[studentID]&statut=length");
		exit;
	}

	$passHash = password_hash($_POST['passwd'], PASSWORD_BCRYPT);
	$psql = new PSQLDatabase();
	$psql->reinitStudentPasswd($_POST['studentID'], $passHash);

	header("location: studentCaracteristics.php?studentID=$_POST[studentID]&statut=confirm");
 ?>
