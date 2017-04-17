var canvas;
var ctx;
var phraseJeu = ['Ceci ','est', 'la ', 'phrase ', 'de ', 'la ', 'partie.']


var app = angular.module('demoApp', []);

app.controller('monCtrl', function($scope){
	$scope.valeur = 12;
	$scope.bgC = 'blue';
//	$scope.phraseJeu = ['Ceci ','est ', 'la ', 'phrase ', 'de ', 'la ', 'partie.']
	$scope.phraseJeu = phraseJeu;
	$scope.hoverPhrase = function(eventE)
	{
		$scope.valeur = eventE.clientX;
	}
	
	$scope.changeColor = function(eventE)
	{
		$scope.valeur = $scope.valeur +2;
		$scope.bgC = 'red';
	}
});

function drawSentence()
{
	var measure =10;
    ctx.font = "40px Arial";
    ctx.strokeStyle = "Green";
	
	for(var i=0;i<phraseJeu.length;i++)
	{
		ctx.shadowBlur = 10;
		ctx.shadowColor="black";
	    ctx.textAlign='left';
	    ctx.fillText(phraseJeu[i],measure+15,200);
		ctx.strokeStyle="red";
		ctx.strokeRect(measure,160,150,50);
		ctx.shadowOffsetX=5;
		ctx.shadowOffsetY=5;
	
	    measure += 150;
	}
}

/* main pour charger le canvas */
window.onload = function()
{
    canvas = document.getElementById("canvasJeu2");
    ctx = canvas.getContext("2d");
	drawSentence();
	ctx.fillText("test",10,20);
}