<?php session_start()?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="stylesheet" href="/CSS/Tutoriel.css" />
		<title>Jeux</title>
	</head>

	<body>
		<header class="headerAcc">
			<?php include('../HeaderFooter/Header.inc.php'); ?>
		</header>
		<br/>
		<div class="backgroundBody">
			<?php if($_GET['jeu'] == 1):?>
			<h1> Appariement de mots </h1>
			<div class="tuto">
				<img class="slide" src="/res/Img/TutoJ1/slide01.jpg">
				<img class="slide" src="/res/Img/TutoJ1/slide02.jpg">
				<img class="slide" src="/res/Img/TutoJ1/slide03.jpg">
			<?php endif;?>
			<?php if($_GET['jeu'] == 2):?>
			<h1> Jeu de reformulation </h1>
			<div class="tuto">
				<img class="slide" src="/res/Img/TutoJ2/slide01.jpg">
				<img class="slide" src="/res/Img/TutoJ2/slide02.jpg">
				<img class="slide" src="/res/Img/TutoJ2/slide03.jpg">
			<?php endif;?>
				<div class="bouton">
					<button class="display-left" onclick="plusDivs(-1)">&#10094;</button>
					<button class="display-right" onclick="plusDivs(+1)">&#10095;</button>
				</div>
			</div>
			<script>
				var slideIndex = 1;
				showDivs(slideIndex);

				function plusDivs(n) {
				    showDivs(slideIndex += n);
				}

				function showDivs(n) {
				    var i;
				    var x = document.getElementsByClassName("slide");
				    if (n > x.length) {slideIndex = 1}
				    if (n < 1) {slideIndex = x.length} ;
				    for (i = 0; i < x.length; i++) {
				        x[i].style.display = "none";
				    }
				    x[slideIndex-1].style.display = "block";
				}
			</script>
		</div>
		<br/>
		<footer>
			<?php include('../HeaderFooter/Footer.inc.php'); ?>
		</footer>
	</body>
</html>
