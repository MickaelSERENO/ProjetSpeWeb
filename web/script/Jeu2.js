// js/todoList.js
//'use strict';


var isClicked;
var mouseX;
var mouseY;
var ctx;
var canvas;
var sentence;
var gameName;

const SPACE_BLANK = 50;
const START_MARGE = 10;
var timeY =200;
var playerArray = new Array();
var phraseArray = new Array();
var phrasePlayerArray = [];
var selectedWords = [];
var requestID =0;
var mousePos;

/* Pour les animations du countdown */
window.requestAnimFrame = function(){
    return (
        window.requestAnimationFrame       || 
        window.webkitRequestAnimationFrame || 
        window.mozRequestAnimationFrame    || 
        window.oRequestAnimationFrame      || 
        window.msRequestAnimationFrame     || 
        function(callback){
            window.setTimeout(callback, 500 / 60);
        }
    );
}();


class Chrono
{
	constructor(s)
	{
		this.sec = s;
		this.cen = 0;
	}

	drawLine()
	{
	    ctx.strokeStyle= 'hsl(348, 100%, 40%)';
	    ctx.lineWidth=10;
	    ctx.beginPath();
	    ctx.arc(800,100,65,0,2*Math.PI);
	    ctx.stroke();
	    ctx.restore();
	}

	drawTimer()
	{
	    var nbDisplay = ((180 - this.sec)%180)/180;
		var angle = (2*Math.PI)*(nbDisplay)+(Math.PI/-2);
	    ctx.strokeStyle= 'hsl(56, 100%, 68%)';
	    ctx.lineWidth=8;
	    ctx.beginPath();
	    ctx.arc(800,100,65,Math.PI/-2,angle);
	    ctx.stroke();
	    ctx.restore();

	}
	
	countdown(timer)
	{
		this.cen--;
		if(this.cen<0)
		{
			this.sec--;
			this.cen = 9;
			timeY*= 1.1;
		}
		if(this.sec>=0 && this.cen>=0)
		{ 
		    ctx.clearRect(747,80,107,70);
		    ctx.fillText(this.sec+" : "+this.cen,800,100);
		    this.drawLine();
		    this.drawTimer(this.cen);
		    requestID=requestAnimFrame(this.countdown.bind(this));
		}
		else
		{
			this.sec=0;this.cen=0;
		    cancelAnimationFrame(requestID);
		    this.sec=0;
		    this.cen =0;
		}
	}
};

class TextBlock
{
	constructor(text,hgt)
	{
		this.text = text;
		this.w = ctx.measureText(this.text).width;
		this.h = hgt;
		this.x = 0;
		this.y = 0;
	}
	
	get text()
	{
		return this._text;
	}
	
	get h()
	{
		return this._h;
	}
	
	get w()
	{
		return this._w;
	}
	
	set text(newt)
	{
		this._text = newt;
		this.w = ctx.measureText(this.text).width;
	}
	set h(hgt) 
	{
		this._h = hgt;
	}
	
	set w(wgt)
	{
		this._w = wgt;
	}

	setSize(hgt,wgt)
	{
		this.h(hgt);
		this.w(wgt);
	}
	
	createSentence(abX,abY)
	{
		ctx.font = "25px Arial";
		ctx.lineWidth=1;
		ctx.strokeStyle = "Green";
		ctx.shadowBlur = 10;
		ctx.shadowColor = "black";
		ctx.textAlign='center';
		ctx.textBaseline="middle";
		ctx.fillText(this.text,abX+2+this.w/2,abY+this.h/2);
		ctx.strokeRect(abX,abY,this.w+5,this.h);
		this.x = abX;
		this.y = abY;
		ctx.shadowOffsetX=2;
		ctx.shadowOffsetY=2;
		ctx.shadowBlur = 0;
		ctx.save();
	}
};



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
	while(table.childElementCount>1) {
		table.deleteRow(1);
	}
	data = "action=getListGames";
	ajaxPost("../ClientQuery/handlingGame2.php", data, function (response) {
		
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
			gameName = games[i].split(":")[0];
			num = games[i].split(":")[1];
			var newLine = document.getElementById('table').insertRow(-1);
			c1 = newLine.insertCell(-1);
			button = document.createElement("input");
			button.setAttribute("type", "submit");
			button.setAttribute("value", "Join");
			button.setAttribute("onClick", "rejoinGame('"+gameName+"')");
			c1.setAttribute("align", "center");
			c1.appendChild(button);
			c2 = newLine.insertCell(1);
			c2.setAttribute("align", "center");
			c2.innerHTML += gameName;
			c3 = newLine.insertCell(2);
			c3.setAttribute("align", "center");
			c3.innerHTML += num
		}
	}, true);
}


function rejoinGame(gameName){
	var form = document.getElementById('chooseSent');
	var tab = document.getElementById('joinGame');
	data = "action=newPlayer&gameName="+gameName+"&idPlayer=machin@bidule.fr&namePlayer=Jeanne-Dark";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
			if(response != "gameNotFound" && response != "playerAlreadyExists")
			{
				sentence = response;
				form.style.display = "none";
				tab.style.display = "none";
				//testGame2(gameName);
				gamePart1();
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
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
			if(response == "gameExists")
			{
				errMessage.style.display = "initial";			
			}
			else if (response == "OK")
			{
				form.style.display = "none";
				tab.style.display = "none";
				gamePart1();
				//testGame(idPlayer);
			}
		}, true);
	
}

/*function testGame(idPlayer)
{
	data = "action=addSent&gameName="+gameName+"&idPlayer="+idPlayer+"&sentence=Do you want to build a snowman ?&borneInf=1&borneSup=7";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("THEONLYTEST " + response + "\n");
		}, true);
	data = "action=addSent&gameName="+gameName+"&idPlayer="+idPlayer+"&sentence=J'aimerais&borneInf=1&borneSup=2";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("THEONLYTEST " + response + "\n");
		}, true);
		
	data = "action=addSent&gameName="+gameName+"&idPlayer="+idPlayer+"&sentence=elementaire de glace&borneInf=4&borneSup=6";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
		}, true);
	
	
	data = "action=getFirstSent&gameName="+gameName;
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("Phrase d'origine : " + response + "\n");
		}, true);
	
	data = "action=getPlayerSents&gameName="+gameName+"&idPlayer="+idPlayer;
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("Phrases du joueur : " + response + "\n");
		}, true);
	
	
}

function testGame2(gameName)
{
	data = "action=getOtherSents&gameName="+gameName+"&idPlayer=machin@bidule.fr";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log("Phrases des autres : " + response + "\n");
		}, true);


}*/

function createNewWord(phraseBlock)
{
	let wBlock = new TextBlock(phraseBlock.text,phraseBlock.h);
	console.log(wBlock.text+" : "+wBlock.x+"  , "+wBlock.w+" , "+wBlock.h);
	return wBlock;
}




/*function draw() {
	ctx.fillStyle = "orange";
	ctx.font = "25px Arial";
	ctx.fillText("Hello World", 50, 50);

}*/

function clearCanvas()
{
	ctx.save();
	//canvas.style.backgroundColor = 'yellow';
	
	ctx.setTransform(1, 0, 0, 1, 0, 0);
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.restore();
}

function onClickCanvas($event)
{
	clearCanvas();
	selectedWords=[];
	isClicked = 1;
	mousePos = {
		x: $event.offsetX,
		y: $event.offsetY
	};
	printWords();
	for(i=0;i<phraseArray.length;i++)
	{
		posX = phraseArray[i].x;
		posY = phraseArray[i].y;
		prec = phraseArray[i].w;
		suiv = phraseArray[i].h;
	}
	
}


function onMouseMove($event)
{
	if(isClicked == 1)
	{	
		clearCanvas();
		ctx.fillStyle = "rgba(0,0,255,0.2)";
		ctx.fillRect(mousePos.x, mousePos.y, $event.offsetX-mousePos.x, $event.offsetY-mousePos.y);
		printWords();
		//draw();
	}
}

function onMouseUpCanvas($event)
{
	clearCanvas();
	selectedWords = [];
	max = -1;
	min = 1000;
	for(i=0;i<phraseArray.length;i++)
	{
		posX = phraseArray[i].x;
		posY = phraseArray[i].y;
		prec = phraseArray[i].w;
		suiv = phraseArray[i].h;
		if( (mousePos.x<=posX && mousePos.y<=posY && $event.offsetX>=posX+prec && $event.offsetY>=posY+suiv) ||
			($event.offsetX<=posX && $event.offsetY<=posY && mousePos.x>=posX+prec && mousePos.y>=posY+suiv))
		{
			selectedWords.push(phraseArray[i]);
			if(i<min) min=i;
			if(i>max) max = i;
		}
		
		else if( (mousePos.x>posX) && (mousePos.x<(posX+prec)) && (mousePos.y>posY) && (mousePos.y<(posY+suiv)) )
		{
			min=i;
			max=i;
			selectedWords.push(phraseArray[i]);
			console.log("done: "+phraseArray[i].text +" x: "+phraseArray[i].x+" y: "+phraseArray[i].y+" w: "+phraseArray[i].w+"  time: "+timeY);
			var cnw = createNewWord(phraseArray[i]);
			phrasePlayerArray.push(cnw);
			console.log(phrasePlayerArray[phrasePlayerArray.length-1]);
			console.log(phraseArray[i]);
			phrasePlayerArray[phrasePlayerArray.length-1].createSentence(phraseArray[i].x,/*phraseArray[i].y+100*/timeY);
			break;				
		}
	}
	printWords(min, max);
	
	isClicked = 0;
	if(selectedWords.length > 0)
	{		;
	}
}

function printWords(min=-1, max=-1)
{
	var ms =10;
	for(var i=0;i<phraseArray.length;i++)
	{
		if(i<min || i>max)
		{
			ctx.fillStyle = "rgba(0,0,0,1)";
		}
		else
		{
			ctx.fillStyle = "rgba(255,255,0,1)";
		}
		phraseArray[i].createSentence(ms,160);
		ms += phraseArray[i].w+SPACE_BLANK;
	}
}

var myApp = angular.module("AppGame", []);
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
	//draw();
	
}

function gamePart1()
{


	isClicked = 0;
	jeuEnCours = false;
	
	//idPlayer = getCookie("user");/*todo : avoir le bon nom de cookie.*/
	var data = "AAA";
	
	canvas   = document.getElementById('canvasJeu2');
	ctx=canvas.getContext('2d');
	ctx.font = "25px Arial";
	
	phraseJeu = sentence.split(" ");
	for(var i=0;i<phraseJeu.length;i++)
	{
		phraseArray.push(new TextBlock(phraseJeu[i],40));
	}
	
	var ms =10;
	for(var i=0;i<phraseArray.length;i++)
	{
		phraseArray[i].createSentence(ms,160);
		ms += phraseArray[i].w+SPACE_BLANK;

	}

	var chr = new Chrono(100);
	requestID = requestAnimFrame(chr.countdown());
}

window.onload = function()
{
	canvas   = document.getElementById('canvasJeu2');
	div = document.getElementById('divJeu');
	createGame = document.getElementById('chooseSent');
	joinGame = document.getElementById('joinGame');
	ctx=canvas.getContext('2d');
	ctx.canvas.position = "absolute";
	canvas.left = div.style.left;
	canvas.top = div.style.top;
	canvas.width  = div.offsetWidth;
	canvas.height = div.offsetHeight+500;
	
	createGame.style.left = canvas.offsetLeft +10+  "px";
	createGame.style.top = canvas.offsetTop +10+  "px";
	
	joinGame.style.left = canvas.offsetLeft +10+ "px";
	joinGame.style.top = canvas.offsetTop + 300 + "px";
	/*
	join = document.getElementById('joinGame');
	//join.position = "absolute";
	join.left = canvas.left;
	join.top = canvas.top;
*/	
	
	//position
	
	/*data = "action=newPlayer&gameName=firstGame&idPlayer=elle@elle.fr&namePlayer=Jean-Gustave";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
	
	data = "action=newPlayer&gameName=secGame&idPlayer=bof@elle.fr&namePlayer=Jean-Jacques";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
		
	data = "action=newPlayer&gameName=secGame&idPlayer=bof@bof.fr&namePlayer=Jean-Michel";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
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
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
	
	data = "action=exitPlayer&gameName=firstGame&idPlayer=elle@elle.fr";
	ajaxPost("../ClientQuery/handlingGame2.php", data,
		function (response) {
			console.log(response);
		}, true);
}*/

/*window.onresize = function()
{
	ctx.canvas.width  = 1.1*window.innerWidth;
	ctx.canvas.height = 1.1*window.innerHeight;
	//draw();
}*/