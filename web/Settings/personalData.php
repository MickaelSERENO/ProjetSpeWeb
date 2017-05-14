<?php session_start();?>
<?php
	if(!isset($_SESSION['mail']))
	{
		header('location: /Session/Connexion.php');
		exit;
	}
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="utf-8" />
		<script src="../bower_components/angular/angular.min.js"></script>
		<script src="../bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<script src="../script/stats.js"></script>
		<link rel="stylesheet" type="text/css" href="/CSS/stats.css">
		<link rel="stylesheet" type="text/css" href="/CSS/Accueil.css">
	</head>

	<header class="headerAcc">
		<?php include('../HeaderFooter/Header.inc.php'); ?>
	</header>

	<body ng-app="statsApp">
<?php
?>
	</body>

	<footer>
		<?php include('../HeaderFooter/Footer.inc.php'); ?>
	</footer>
</html>
