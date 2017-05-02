<?php session_start();

$_SESSION['userID'] = 1;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/CSS/Accueil.css" />
		<link rel="stylesheet" href="/CSS/Jeu.css" />
		<script src="/bower_components/angular/angular.min.js"></script>
		<script src="/bower_components/xmlhttprequest/XMLHttpRequest.js"></script>
		<title>Jeux</title>
	</head>

	<body ng-app="AppGame">
		<header class="headerAcc">
			<?php include('../HeaderFooter/Header.inc.php'); ?>
		</header>
		<br/>

		<div class="backgroundBody">
			<h1> Titre du jeu </h1>





<?php if($_GET['jeu'] == 1):?>
			<script src="/script/jeu1.js"></script>

			<div ng-controller="CanvasCtrl" id="divJeu">
				<div class="divCanvas">
					<canvas  ng-mousemove="onMouseMoveCanvas($event)" ng-mousedown="onClickCanvas($event)" ng-mouseup="onMouseUpCanvas($event)" id="canvasJeu1" width="1000px" height="400px">
						Juste au cas ou.
					</canvas>
				</div>

				<div id="formJeu1" ng-controller="form">
					<form ng-submit="submit()">
						<div ng-repeat="v in operations" ng-show="showValue">
							<input type="radio" ng-value="v.value" ng-model="$parent.radio" class="radioJeu1" 
							ng-change="changeRadio(v.value)"/>
							<label>{{v.text}}</label>
						</div>
						<input type="submit" value="ok">
					</form>
				</div>
			</div>
<?php endif;?>


<?php if($_GET['jeu'] == 2):?>

	<script src="../script/Jeu2.js"></script>
	<link rel="stylesheet" href="CSS/jeu2.css" />
	<div id="divJeu">
		<div ng-controller="CanvasController">
			<canvas ng-mousedown="onClickCanvas($event)" ng-mousemove="onMouseMove($event)" ng-mouseup="onMouseUpCanvas($event)", id="canvasJeu2" width="100%" height="100%">
			</canvas>
		</div>
		<form id="chooseSent" style="position:absolute; top:5%; left:5%">
			<h1>Create a new game</h1>
			<p>
				<label for="name">Name of the game :</label>
				<input type="text" name="name" id="name" size="20">
				<br />
				<br />
				<label for="sent">Sentence to rephrase :</label>
				<input type="text" name="sent" id="sent" size="100">
			</p>
			<input type="submit" value="new Game" onClick="validForm('Jean-Christophe', 'test@test.truc')"/>
			<text id="errMessage"> Error : Name already given</text>
		</form>
		
		<div id="joinGame" style="position:absolute;">
			<h1>Join a game</h1>
			<input type="submit" value="refresh" onClick="printGames()">
			<table id="table">
			   <tr id="firstLine">
				   <th align="center"> </th>
				   <th align="center">Game</th>
				   <th align="center">Players</th>
			   </tr>
			   
			</table>
		</div>
		
	</div>
<?php endif;?>



		</div>
		<br/>
		<footer>
			<?php include('../HeaderFooter/Footer.inc.php'); ?>
		</footer>
	</body>
</html>
