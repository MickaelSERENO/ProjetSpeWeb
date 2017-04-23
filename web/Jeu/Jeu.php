<?php session_start();?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="stylesheet" href="/CSS/Jeu.css" />
		<script src="/bower_components/angular/angular.min.js"></script>
		<script src="/bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<script src="/script/jeu1.js"></script>
		<title>Jeux</title>
	</head>

	<body ng-app="AppGame1">
		<header class="headerAcc">
			<?php include('../HeaderFooter/Header.inc.php'); ?>
		</header>
		<br/>

		<div class="backgroundBody">
			<h1> Titre du jeu </h1>
			<div class="consigne">
				<p>Utilisez le mode plein écran pour plus de confort</p>
			</div>
<?php if($_GET['jeu'] == 1):?>
			<div ng-controller="CanvasCtrl">
				<canvas  ng-mousemove="onMouseMoveCanvas($event)" ng-mousedown="onClickCanvas($event)" ng-mouseup="onMouseUpCanvas($event)" id="canvasJeu1" width="1000px" height="400px">
					Juste au cas ou.
				</canvas>
			</div>

			<div ng-controller="form">
				<form ng-submit="submit()">
					<div ng-repeat="v in operations" ng-show="showValue">
						<input type="radio" ng-value="v.value" ng-model="$parent.radio" 
						ng-change="changeRadio(v.value)"/>
						<label>{{v.text}}</label>
					</div>
					<input type="submit" value="ok">
				</form>
			</div>
<?php endif;?>

		</div>
		<br/>
		<footer>
			<?php include('../HeaderFooter/Footer.inc.php'); ?>
		</footer>
	</body>
</html>
