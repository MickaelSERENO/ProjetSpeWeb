const SENT1Y      = 100;
const SENT2Y      = 300;
const FONT_HEIGHT = 35;

var currentType;
var sentences;
var canvas;
var ctx;

const Type = 
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
	var currentX = 0;

	for(var i=0; i < sentence.wordArray.length; i++)
	{
		ctx.fillText(sentence.wordArray[i].groupText, px+currentX, py);
		var measure = ctx.measureText(sentence.wordArray[i].groupText).width;
		ctx.strokeRect(px+currentX-spaceMeasure/3.0, py-FONT_HEIGHT+5, measure+spaceMeasure - spaceMeasure/3.0, FONT_HEIGHT);
		currentX += measure+spaceMeasure;
	}
}

function drawArrow(fromx, fromy, tox, toy)
{
    var headlen = 10;
    var angle = Math.atan2(toy-fromy,tox-fromx);
    ctx.moveTo(fromx, fromy);
    ctx.lineTo(tox, toy);
    ctx.lineTo(tox-headlen*Math.cos(angle-Math.PI/6),toy-headlen*Math.sin(angle-Math.PI/6));
    ctx.moveTo(tox, toy);
    ctx.lineTo(tox-headlen*Math.cos(angle+Math.PI/6),toy-headlen*Math.sin(angle+Math.PI/6));
}

function getWordLinkRect(words)
{
	ctx.font="25px Arial";
	var array = new Array();

	var spaceMeasure = ctx.measureText("       ").width;
	var currentX = spaceMeasure/3.0;
	for(var i=0; i < words.length; i++)
	{
		var measure = ctx.measureText(words[i].groupText).width;
		array.push([currentX-spaceMeasure/3.0, -FONT_HEIGHT+5, measure+2.0*spaceMeasure/3.0, FONT_HEIGHT]);
		currentX += measure+spaceMeasure;
	}

	return array;
}

function isInRect(rect, x, y)
{
	return (x > rect[0] && x < rect[0]+rect[2] &&
		    y > rect[1] && y < rect[1]+rect[3]);
}

function Sentence(sent1, sent2)
{
	this._sent1       = sent1;
	this._sent2       = sent2;
	this._currentType = Type.SAME;

	//Init the associative table
	this._links          = new Array();
	for(var i=0; i<this._sent1.wordArray.length; i++)
		this._links.push([i, null, 0]);

	this._word1LinksRect = getWordLinkRect(this._sent1.wordArray);
	this._word2LinksRect = getWordLinkRect(this._sent2.wordArray);

	for(var i=0; i < this._sent1.wordArray.length; i++)
		this._word1LinksRect[i][1] += SENT1Y;

	for(var i=0; i < this._sent2.wordArray.length; i++)
		this._word2LinksRect[i][1] += SENT2Y;

	this._startX = 0;
	this._endX   = 0;
	this._startY = 0;
	this._endY   = 0;
}

Sentence.prototype.getCurrentType = function(){return this._currentType;}
Sentence.prototype.setCurrentType = function(value){this._currentType = value;}

Sentence.prototype.draw           = function()
{

	ctx.save();
	ctx.setTransform(1, 0, 0, 1, 0, 0);
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.restore();

	this._drawSentences();
	this._drawLinks();
}

Sentence.prototype._drawLinks     = function()
{
	for(var i=0; i < this._links.length; i++)
	{
		if(this._links[i][1] != null)
		{
			var rect1 = this._word1LinksRect[this._links[i][0]];
			var rect2 = this._word2LinksRect[this._links[i][1]];

			switch(this._links[i][2])
			{
				case Type.SAME:
					ctx.strokeStyle = "Green";

					ctx.beginPath();
					ctx.moveTo(rect1[0] + rect1[2]/2.0, rect1[1]+rect1[3]);
					ctx.lineTo(rect2[0] + rect2[2]/2.0, rect2[1]);
					ctx.stroke();

					break;
				case Type.CONTRARY:
					ctx.strokeStyle = "Red";

					ctx.beginPath();
					ctx.moveTo(rect1[0] + rect1[2]/2.0, rect1[1]+rect1[3]);
					ctx.lineTo(rect2[0] + rect2[2]/2.0, rect2[1]);
					break;
				case Type.PRECISE:
					ctx.strokeStyle = "Blue";
					drawArrow(rect1[0] + rect1[2]/2.0, rect1[1]+rect1[3],
							  rect2[0] + rect2[2]/2.0, rect2[1]);
					break;
				case Type.GENERAL:
					ctx.strokeStyle = "Cyan";
					drawArrow(rect2[0] + rect2[2]/2.0, rect2[1],
							  rect1[0] + rect1[2]/2.0, rect1[1]+rect1[3]);
					break;
				default:
					break;
			}
		}
	}
}

Sentence.prototype._drawSentences = function()
{
	drawSentence(ctx, this._sent1, 0, SENT1Y);
	drawSentence(ctx, this._sent2, 0, SENT2Y);
}

Sentence.prototype.setStartPoint  = function(x, y)
{
	this._startX = x;
	this._startY = y;
	this._endX   = x;
	this._endY   = y;
}

Sentence.prototype.commitPoint    = function(x, y)
{
	this._endX   = x;
	this._endY   = y;

	var idWordStart = null;
	var idWordEnd   = null;
	var idSentStart = null;
	var idSentEnd   = null;

	//is the up point on wordlist 1 ?
	for(var i=0; i < this._word1LinksRect.length; i++)
	{
		if(isInRect(this._word1LinksRect[i], x, y))
		{
			idWordEnd = i;
			idSentEnd = 1;
			break;
		}
	}

	if(idSentEnd == null)
	{
		//if no, is the up point on wordlist 2 ?
		for(var i=0; i < this._word2LinksRect.length; i++)
		{
			if(isInRect(this._word2LinksRect[i], x, y))
			{
				idWordEnd = i;
				idSentEnd = 2;
				break;
			}
		}

		if(idSentEnd != null)
		{
			//if yes, is the down point on wordlist 1 ?
			for(var i=0; i < this._word1LinksRect.length; i++)
			{
				if(isInRect(this._word1LinksRect[i], this._startX, this._startY))
				{
					idWordStart = i;
					idSentStart = 1;
					break;
				}
			}
		}
	}

	else
	{
		//if yes, is the down point on wordlist 2 ?
		for(var i=0; i < this._word2LinksRect.length; i++)
		{
			if(isInRect(this._word2LinksRect[i], this._startX, this._startY))
			{
				idWordStart = i;
				idSentStart = 2;
				break;
			}
		}
	}

	if(idWordStart != null && idWordEnd != null)
	{
		if(idSentStart === 1)
		{
			for(var i=0; i < this._sent1.wordArray.length; i++)
				if(this._links[i][1] === idWordEnd)
					this._links[i][1] = null;

			this._links[idWordStart][1] = idWordEnd;
			this._links[idWordStart][2] = Type.SAME; //TODO
		}
		else
		{
			for(var i=0; i < this._sent1.wordArray.length; i++)
				if(this._links[i][1] === idWordStart)
					this._links[i][1] = null;

			this._links[idWordEnd][1] = idWordStart;
			this._links[idWordEnd][2] = Type.SAME; //TODO
		}
	}

	this.draw();
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

function onClickCanvas($event)
{
	sentences.setStartPoint($event.offsetX, $event.offsetY);
}

function onMouseUpCanvas($event)
{
	sentences.commitPoint($event.offsetX, $event.offsetY);
}

var myApp = angular.module("AppGame1", []);
myApp.controller("CanvasCtrl", function($scope)
{
	$scope.onClickCanvas   = onClickCanvas;
	$scope.onMouseUpCanvas = onMouseUpCanvas;
});

//The main
window.onload = function()
{
	canvas   = document.getElementById('canvasJeu1');
	ctx      = canvas.getContext('2d');
	promptSentences(2, 1, getSentencesFromServer, ctx);
}
