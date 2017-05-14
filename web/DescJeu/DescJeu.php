<?php session_start();
	if(!isset($_GET['jeu']))
	{
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
		die();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="stylesheet" href="/CSS/DescJeu.css" />
		<title>Jeux</title>
	</head>

	<body>
		<header class="headerAcc">
			<?php include('../HeaderFooter/Header.inc.php'); ?>
		</header>
		<br/>
		<div class="backgroundBody">
			<?php if($_GET['jeu'] == 1):?>
			<h1> Jeu d'appariement </h1>
			<?php endif;?>

			<?php if($_GET['jeu'] == 2):?>
			<h1> Jeu de reformulation </h1>
			<?php endif;?>

			<div class="descJeu">
				<div class="jeuImage">
					<img src="/res/Img/01.jpg" alt="Jeu 1" /> 
				</div>

				<?php if($_GET['jeu'] == 1):?>
				<div class="description">
					<p> Description du jeu 1. Un Jeu d'appariement. Description à compléter ... </p>
				</div>
				<?php endif;?>

				<?php if($_GET['jeu'] == 2):?>
				<div class="description">
					<p> Description du jeu 2. Un jeu de reformulation. Description à compléter ... </p>
				</div>
				<?php endif;?>
				<div class="bouton">
				<div><p> <?php echo "<a href=\"/Tutoriel/Tutoriel.php?jeu=$_GET[jeu]\">";?> Tutoriel </a></p></div>
				<div><p> <?php echo "<a href=\"/Jeu/Jeu.php?jeu=$_GET[jeu]\">";?> Jouer </a></p></div>
				</div>
			</div>
		</div>
		<br/>
		<footer>
			<?php include('../HeaderFooter/Footer.inc.php'); ?>
		</footer>
	</body>
</html>
