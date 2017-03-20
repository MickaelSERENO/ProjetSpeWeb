const SENT1Y = 100;
const SENT2Y = 300;

var myApp = angular.module("MyApp", []);
var currentType;
var sentences;
var ctx;

const Type
{
	SAME:1,
	CONTRARY: 2,
	PRECISE: 3,
	GENERAL: 4,	
}

function drawSentence(ctx, sentence, px, py)
{
	ctx.font="25px Arial";
	ctx.strokeStyle = "Green";
	var spaceMeasure = ctx.measureText("       ").width;
	px += spaceMeasure/3.0;
	var fontHeight = 35;
	var currentX = 0;

	for(var i=0; i < sentence.wordArray.length; i++)
	{
		ctx.fillText(sentence.wordArray[i].groupText, px+currentX, py);
		var measure = ctx.measureText(sentence.wordArray[i].groupText).width;
		ctx.strokeRect(px+currentX-spaceMeasure/3.0, py-fontHeight+5, measure+spaceMeasure - spaceMeasure/3.0, fontHeight);
		currentX += measure+spaceMeasure;
	}
}

function getWordLinkRect()
{
	var array = new Array();
}

function Sentence(sent1, sent2)
{
	this._sent1       = sent1;
	this._sent2       = sent2;
	this._currentType = Type.SAME;

	//Init the associative table
	this._links          = new Array();
	for(var i=0; i<this._sent1.words.length; i++)
		this.links.push([i, null, 0]);

	this._word1LinksRect = getWordLinkRect(this._sent1.words);
	this._word2LinksRect = getWordLinkRect(this._sent2.words);
}

Sentence.prototype.getCurrentType = function(){return this._currentType;}
Sentence.prototype.setCurrentType = function(value){this._currentType = value;}

Sentence.prototype.draw           = function()
{
	this._drawSentences();
	this._drawLinks();
}

Sentence.prototype._drawLinks     = function()
{
	for(var i=0; i < this._links.length; i++)
	{
		if(this._links[i][1])
		{
		}
	}
}

Sentence.prototype._drawSentences = function()
{
	drawSentence(ctx, this._sent1, 0, SENT1Y);
	drawSentence(ctx, this._sent2, 0, SENT2Y);
}

function promptSentences(idPackSentence, idSentence, callback, data=null)
{
	var httpCtx = new XMLHttpRequest();
	httpCtx.onreadystatechange = function()
	{
		if(httpCtx.readyState == 4 && (httpCtx.status == 200 || httpCtx.status == 0))
		{
			var receive = JSON.parse(httpCtx.responseText);
			callback(data, receive);
		}
	}
	httpCtx.open("POST", "ClientQuery/handlingGame1.php", true);
	httpCtx.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	httpCtx.send("idPrompt=1&idPack="+idPackSentence+"&idSent="+idSentence);
}

function getSentencesFromServer(ctx, jsonData)
{
	sentences = new Sentence(jsonData.sent1, jsonData.sent2);
	sentences.draw();
}

function linkTypeCtrl($scope)
{
	currentType = Type.SAME;
	$scope.$watch('type', function(value){

	});
}

//The main
window.onload = function()
{
	var canv = document.getElementById('canvasJeu1');
	ctx      = canv.getContext('2d');
	promptSentences(2, 1, getSentencesFromServer, ctx);
}
