<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="stylesheet" href="/CSS/JeuAccueil.css" />
		<title>Jeux</title>
	</head>

	<body>
		<header class="headerAcc">
			<?php include('../HeaderFooter/Header.inc.php'); ?>
		</header>

		<br/>
		<div class="backgroundBody">
			<h1>Jeux</h1>
			<div id="JA_listeJeu">
				<div class="JA_jeu">
					<div class="JA_jeuImage">
						<a href="/DescJeu/DescJeu.php?jeu=1"><img src="/res/Img/01.jpg" alt="Jeu 1" /></a> 
					</div>
					<div class="JA_jeuPresentation">
						<h2>Jeu 1</h2>
						<div class="JA_presentationText">
							<p>Petite description rapide du jeu.Et du blabla pour voir l'aligment de texte et la différence de taillec : bbb hjbzk éezrf vbrjq,s;qn rvekbf rdjfevgbs kzekbfkb kddjvb djvbq ffdjnv kfdbs; fdvs kedbfk dskqbk sjdbfkl</p>
						</div>
					</div>
				</div>
				<div class="JA_jeu">
					<div class="JA_jeuImage">
						<a href="/DescJeu/DescJeu.php?jeu=2"><img src="/res/Img/02.jpg" alt="Jeu 2" /></a> 
					</div>
					<div class="JA_jeuPresentation">
						<h2>Jeu 2</h2>
						<div class="JA_presentationText">
							<p>Petite description rapide du jeu.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br/>
		<footer>
			<?php include('../HeaderFooter/Footer.inc.php'); ?>
		</footer>
	</body>
</html>
