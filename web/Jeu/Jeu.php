<?php session_start();

?>

<?php
	if(!isset($_SESSION['verified_use']) || $_SESSION['verified_use'] != 1)
	{
		header('location: /Session/Connexion.php');
		exit;
	}
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



<?php if($_GET['jeu'] == 1):?>
			<script src="/script/jeu1.js"></script>
			<h1> Appariement de mots</h1>
			<div ng-controller="CanvasCtrl" id="divJeu">
				<div class="divCanvas">
					<canvas  ng-mousemove="onMouseMoveCanvas($event)" ng-mousedown="onClickCanvas($event)" ng-mouseup="onMouseUpCanvas($event)" id="canvasJeu1" width="1000px" height="400px">
						Juste au cas ou.
					</canvas>
				</div>

				<div id="formJeu1" ng-controller="form">
					<form ng-submit="submit()">
						<div ng-repeat="v in operations" ng-show="showValue" class="radioJeu1" ng-class="radioJeu1">
							<input type="radio" ng-value="v.value" ng-model="radio.value" ng-change="changeRadio(v.value)"/>
							<label class="radioLabelJ1">{{v.text}}</label>
						</div>
						<br/>
						<div class="okJeu1">
							<input type="submit" value="ok">
						</div>
					</form>
				</div>
			</div>
<?php endif;?>


<?php if($_GET['jeu'] == 2):?>
	
	<script src="../script/Jeu2.js"></script>
	<link rel="stylesheet" href="/CSS/jeu2.css" />
	<h1> Jeu de reformulation </h1>
	<div id="divJeu">
		<div ng-controller="CanvasController">
			<canvas ng-mousedown="onClickCanvas($event)" ng-mousemove="onMouseMove($event)" ng-mouseup="onMouseUpCanvas($event)", id="canvasJeu2" width="100%" height="100%">
			</canvas>
		</div>
		<form id="chooseSent" style="position:absolute; top:5%; left:5%">
			<h1>Creer une partie</h1>
			<p>
				<label for="name">Nom de la partie :</label>
				<input type="text" name="name" id="name" size="20">
				<br />
				<br />
				<label for="sent">Phrase a reformuler :</label>
				<input type="text" name="sent" id="sent" size="100">
			</p>
			<input type="submit" value="new Game" onClick="validForm('<?php echo $_SESSION['pseudo']?>', '<?php echo $_SESSION['mail']?>')"/>
			<text id="errMessage"> Erreur : nom deja donne</text>
		</form>
		<input type="text" id="answer" size="30">
		<div id="joinGame" style="position:absolute;">
			<h1>Rejoindre une partie</h1>
			<input type="submit" value="refresh" onClick="printGames()">
			<table id="table">
			   <tr id="firstLine">
				   <th align="center"> </th>
				   <th align="center">Partie</th>
				   <th align="center">Nb de joueurs</th>
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
