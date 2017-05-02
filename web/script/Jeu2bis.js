// js/todoList.js
//'use strict';


var isClicked;
var mouseX;
var mouseY;
var ctx;
var canvas;
var sentence;
var gameName;
var jeuEnCours;


function Joueur (nom) {
  this.nom = nom || "";
  this.score = 0;
}

function Partie(id){
	this.id = id;
	var joueurs = [];
}


function getCookie(sName) {
        var oRegex = new RegExp("(?:; )?" + sName + "=([^;]*);?");
        if (oRegex.test(document.cookie)) {
                return decodeURIComponent(RegExp["$1"]);
        } else {
                return null;
        }
}

function printGames()
{
	table = document.getElementById('table');
	
	while (table.firstChild) {
		if(table.getAttribute("id") != "firstLine")
			table.removeChild(table.firstChild);
	}
	data = "action=getListGames";
	ajaxPost("ClientQuery/handlingGame2.php", data, function (response) {
		
		if(response == "noGame")
		{
			document.getElementById("joinGame").style.display = "none";
			return;
		}
		
		document.getElementById("joinGame").style.display = "initial";
		if(response.substr(response.length-1, response.length) == "\n")
		{
			response = response.substr(0, response.length-1);
		}
		
		var games = response.split("\n");
		for(var i=0; i<games.length; i++)
		{	
			name = games[i].split(":")[0];
			num = games[i].split(":")[1];
			var newLine = document.getElementById('table').insertRow(-1);
			c1 = newLine.insertCell(-1);
			button = document.createElement("INPUT");
			button.setAttribute("type", "submit");
			button.setAttribute("value", "Join");
			button.setAttribute("onClick", "joinGame('"+name+"')");
			c1.setAttribute("align", "center");
			c1.appendChild(button);
			c2 = newLine.insertCell(1);
			c2.setAttribute("align", "center");
			c2.innerHTML += name;
			c3 = newLine.insertCell(2);
			c3.setAttribute("align", "center");
			c3.innerHTML += num
		}
	}, true);
}
function validForm(namePlayer, idPlayer)
{
	var form = document.getElementById('chooseSent');
	var tab = document.getElementById('joinGame');
	sentence = document.getElementById('sent').value;
	gameName = document.getElementById('name').value;
	errMessage = document.getElementById('errMessage');
	
	data = "action=newGame&gameName="+gameName+"&namePlayer="+namePlayer+"&idPlayer="+idPlayer+"&sentence="+sentence;
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
			if(response == "gameExists")
			{
				errMessage.style.display = "initial";			
			}
			else if (response == "OK")
			{
				jeuEnCours = true;
				form.style.display = "none";
				tab.style.display = "none";
				testGame(idPlayer);
			}
		}, true);
	
}

function testGame(idPlayer)
{
	data = "action=addSent&gameName="+gameName+"&idPlayer="+idPlayer+"&sentence=Do you want to build a snowman ?&borneInf=1&borneSup=7";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("THEONLYTEST " + response + "\n");
		}, true);
	data = "action=addSent&gameName="+gameName+"&idPlayer="+idPlayer+"&sentence=J'aimerais&borneInf=1&borneSup=2";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("THEONLYTEST " + response + "\n");
		}, true);
		
	data = "action=addSent&gameName="+gameName+"&idPlayer="+idPlayer+"&sentence=elementaire de glace&borneInf=4&borneSup=6";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
		}, true);
	
	
	data = "action=getFirstSent&gameName="+gameName;
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("Phrase d'origine : " + response + "\n");
		}, true);
	
	data = "action=getPlayerSents&gameName="+gameName+"&idPlayer="+idPlayer;
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("Phrases du joueur : " + response + "\n");
		}, true);
	
	
}

function testGame2(gameName)
{
	data = "action=getOtherSents&gameName="+gameName+"&idPlayer=machin@bidule.fr";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("Phrases des autres : " + response + "\n");
		}, true);


}
function joinGame(gameName)
{
	var form = document.getElementById('chooseSent');
	var tab = document.getElementById('joinGame');
	data = "action=newPlayer&gameName="+gameName+"&idPlayer=machin@bidule.fr&namePlayer=Jeanne-Dark";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			if(response == "OK"){
				form.style.display = "none";
				tab.style.display = "none";
				testGame2(gameName);
			}
		}, true);
		


}

function draw() {
	ctx.fillStyle = "orange";
	ctx.font = "25px Arial";
	ctx.fillText("Hello World", 50, 50);
	/*for (var i = 0; i < sentence.length; i++) {
		ctx.fillText(sentence[i], 50+100*i, 50);
	}*/
}

function clearCanvas()
{
	ctx.save();
	ctx.setTransform(1, 0, 0, 1, 0, 0);
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.restore();
}

function onClickCanvas($event)
{
	isClicked = 1;
	mouseX = $event.offsetX;
	mouseY = $event.offsetY;
}


function onMouseMove($event)
{
	if(isClicked == 1)
	{
		clearCanvas();
		ctx.fillStyle = "rgb(0,0,255)";
		ctx.fillRect(mouseX, mouseY, $event.offsetX-mouseX, $event.offsetY-mouseY);
		draw();
	}
}

function onMouseUpCanvas($event)
{
	clearCanvas();
	draw();
	isClicked = 0;

}

var myApp = angular.module("AppGame2", []);
myApp.controller("CanvasController", function($scope)
{
	$scope.onClickCanvas   = onClickCanvas;
	$scope.onMouseMove = onMouseMove;
	$scope.onMouseUpCanvas = onMouseUpCanvas;
	
});


// Exécute un appel AJAX POST
// Prend en paramètres l'URL cible, la donnée à envoyer et la fonction callback appelée en cas de succès
// Le paramètre isJson permet d'indiquer si l'envoi concerne des données JSON
function ajaxPost(url, data, callback, isJson) {
    var req = new XMLHttpRequest();
    req.open("POST", url);
	
    req.onreadystatechange = function()
	{
		if(req.readyState == 4 && (req.status == 200 || req.status == 0))
		{
			callback(req.responseText);
		}
	}
    req.addEventListener("error", function () {
        console.error("Erreur réseau avec l'URL " + url);
    });
	
    if (isJson) {
	
        // Définit le contenu de la requête comme étant du JSON
        req.setRequestHeader("Content-Type", "application/json");
        // Transforme la donnée du format JSON vers le format texte avant l'envoi
        data = JSON.stringify(data);
		
    }
    req.send(data);
}

function getSentenceFromServer(response)
{
	console.log(response);
	var jsonData = JSON.parse(response);
	sentence = new Sentence(jsonData);
	draw();
	
}

window.onload = function()
{
	isClicked = 0;
	jeuEnCours = false;
	
	//idPlayer = getCookie("user");/*todo : avoir le bon nom de cookie.*/
	var data = "AAA";
	
	canvas   = document.getElementById('canvasJeu2');
	ctx=canvas.getContext('2d');
	ctx.canvas.width  = 0.8*window.innerWidth;
	ctx.canvas.height = 0.8*window.innerHeight;
	//canvas.style.backgroundColor = 'yellow';
	
	/*data = "action=newPlayer&gameName=firstGame&idPlayer=elle@elle.fr&namePlayer=Jean-Gustave";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
	
	data = "action=newPlayer&gameName=secGame&idPlayer=bof@elle.fr&namePlayer=Jean-Jacques";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
		
	data = "action=newPlayer&gameName=secGame&idPlayer=bof@bof.fr&namePlayer=Jean-Michel";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);*/
	
	errMessage = document.getElementById('errMessage');
	errMessage.style.display = "none";
	printGames();
	
}

/*window.beforeunload = function()
{

	data = "action=exitPlayer&gameName=firstGame&idPlayer=moi@moi.com";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
	
	/*data = "action=exitPlayer&gameName=firstGame&idPlayer=elle@elle.fr";
	ajaxPost("ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);*/
		
}*/

window.onresize = function()
{
	ctx.canvas.width  = 0.8*window.innerWidth;
	ctx.canvas.height = 0.8*window.innerHeight;
	draw();
}