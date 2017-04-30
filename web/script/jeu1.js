const SENT1Y      = 100;
const SENT2Y      = 300;
const FONT_HEIGHT = 35;

const Type = 
{
	SAME:1,
	CONTRARY: 2,
	PRECISE: 3,
	GENERAL: 4,	
}

const MouseType = 
{
	NOTHING:0,
	MOVING:1,
	ENDING:2,
	STARTING:3,
}

const Context = 
{
	PACK_SELECTOP:0,
	INGAME:1,
	END_GAME:2,
}

var currentSentenceID;
var currentType;
var sentences;
var canvas;
var ctx;
var gameCtx=Context.PACK_SELECTION;
var pack;
var end;

function clearCanvas()
{
	ctx.save();
	ctx.setTransform(1, 0, 0, 1, 0, 0);
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.restore();
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
	ctx.beginPath();
    ctx.moveTo(fromx, fromy);
    ctx.lineTo(tox, toy);
    ctx.lineTo(tox-headlen*Math.cos(angle-Math.PI/6),toy-headlen*Math.sin(angle-Math.PI/6));
    ctx.moveTo(tox, toy);
    ctx.lineTo(tox-headlen*Math.cos(angle+Math.PI/6),toy-headlen*Math.sin(angle+Math.PI/6));
	ctx.stroke();
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

function End()
{
	this._text = "Fin de partie !";
}

End.prototype.draw = function()
{
	ctx.fillText(this._text, 50, 50);
}


function Pack(arrayPack)
{
	this._arrayPack    = arrayPack;
	this._packSelected = -1;
	this._rectArray = this._initRectArray();
	this._textPos = this._getTextPos();
}

Pack.prototype.getIDPack = function()
{
	if(this._packSelected >= 0)
		return this._arrayPack[this._packSelected].id;
	else
		return null;
}

Pack.prototype._getTextPos = function()
{
	var array = new Array();

	var py = 50;
	for(var i=0; i < this._arrayPack.length; i+=2)
	{
		var px = 30;
		for(var j=0; j < this._arrayPack.length - i && j < 2; j++)
		{
			var spaceMeasure = ctx.measureText("       ").width;
			px += spaceMeasure/3.0;
			array.push([px, py]);
			px+=400;
		}

		py+=FONT_HEIGHT+20;
	}

	return array;
}

Pack.prototype._initRectArray = function()
{
	var array = new Array();

	var py = 50;
	for(var i=0; i < this._arrayPack.length; i+=2)
	{
		var px = 30;
		for(var j=0; j < this._arrayPack.length - i && j < 2; j++)
		{
			ctx.font="25px Arial";
			ctx.strokeStyle = "Green";
			var spaceMeasure = ctx.measureText("       ").width;
			px += spaceMeasure/3.0;

			var measure = ctx.measureText(this._arrayPack[i].text).width;
			array.push([px-spaceMeasure/3.0, py-FONT_HEIGHT+5, measure+2*spaceMeasure/3, FONT_HEIGHT]);

			px+=400;
		}

		py+=FONT_HEIGHT+20;
	}

	return array;
}

Pack.prototype.draw = function()
{
	clearCanvas();

	//Draw packs text and rect
	ctx.font="25px Arial";
	for(var i=0; i < this._rectArray.length; i++)
	{
		ctx.fillText(this._arrayPack[i].text, this._textPos[i][0], this._textPos[i][1]);
		if(i== this._packSelected)
			ctx.strokeStyle = "Green";
		else
			ctx.strokeStyle = "Brown";

		ctx.strokeRect(this._rectArray[i][0], this._rectArray[i][1], this._rectArray[i][2], this._rectArray[i][3]);
	}
}

Pack.prototype.selectPack = function(x, y)
{
	for(var i=0; i < this._rectArray.length; i++)
	{
		if(x > this._rectArray[i][0] && x < this._rectArray[i][0] + this._rectArray[i][2] &&
		   y > this._rectArray[i][1] && y < this._rectArray[i][1] + this._rectArray[i][3])
		{
			this._packSelected = i;
			this.draw();
			break;
		}
	}
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
	this._mouseType = MouseType.NOTHING;
}

Sentence.prototype.getResults     = function()
{
	return this._links;
}

Sentence.prototype.draw           = function()
{
	clearCanvas();

	this._drawSentences();
	this._drawLinks();
	if(this._mouseType === MouseType.MOVING)
		this._drawPartialLine();
}

Sentence.prototype._drawPartialLine = function()
{
	console.log("partialDraw");
	ctx.strokeStyle = "Gray";
	ctx.beginPath();
	ctx.moveTo(this._startX, this._startY);
	ctx.lineTo(this._endX, this._endY);
	ctx.stroke();
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
					ctx.stroke();
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

Sentence.prototype.setMousePos = function(x, y)
{
	this._endX = x;
	this._endY = y;
	this._mouseType = MouseType.MOVING;
	this.draw();
}

Sentence.prototype.setStartPoint  = function(x, y)
{
	this._startX = x;
	this._startY = y;
	this._endX   = x;
	this._endY   = y;
	this._mouseType = MouseType.STARTING;
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
			this._links[idWordStart][2] = currentType;
		}
		else
		{
			for(var i=0; i < this._sent1.wordArray.length; i++)
				if(this._links[i][1] === idWordStart)
					this._links[i][1] = null;

			this._links[idWordEnd][1] = idWordStart;
			this._links[idWordEnd][2] = currentType;
		}
	}

	this._mouseType = MouseType.ENDING;

	this.draw();
}

function promptSentences(idPackSentence, idSentence, callback, data=null)
{
	var httpCtx = new XMLHttpRequest();
	httpCtx.onreadystatechange = function()
	{
		if(httpCtx.readyState == 4 && (httpCtx.status == 200 || httpCtx.status == 0))
		{
			callback(data, httpCtx.responseText);
		}
	}
	httpCtx.open("POST", "/ClientQuery/handlingGame1.php", true);
	httpCtx.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	httpCtx.send("idPrompt=1&idPack="+idPackSentence);
	currentSentenceID = idSentence;
}

function promptListPack()
{
	var httpCtx = new XMLHttpRequest();
	httpCtx.onreadystatechange = function()
	{
		if(httpCtx.readyState == 4 && (httpCtx.status == 200 || httpCtx.status == 0))
		{
			console.log(httpCtx.responseText);
			pack = new Pack(JSON.parse(httpCtx.responseText));
			pack.draw();
		}
	}
	httpCtx.open("POST", "/ClientQuery/handlingGame1.php", true);
	httpCtx.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	httpCtx.send("idPrompt=0");
}

function getSentencesFromServer(data, response)
{
	if(response === "-1")
	{
		sentences = null;
		gameCtx = Context.END_GAME;
		end = new End();
		clearCanvas();
		end.draw();
	}
	else
	{

		var jsonData = JSON.parse(response);
		sentences = new Sentence(jsonData.sent1, jsonData.sent2);
		sentences.draw();
	}
}

function onClickCanvas($event)
{
	if(gameCtx === Context.INGAME)
	{
		if(sentences)
			sentences.setStartPoint($event.offsetX, $event.offsetY);
	}
	else if(gameCtx === Context.PACK_SELECTION)
	{
		if(pack)
			pack.selectPack($event.offsetX, $event.offsetY);
	}
}

function onMouseMoveCanvas($event)
{
	if(gameCtx == Context.INGAME)
	{
		if(sentences && (sentences._mouseType === MouseType.STARTING || sentences._mouseType === MouseType.MOVING))
		{
			sentences.setMousePos($event.offsetX, $event.offsetY);
		}
	}
	else if(gameCtx == Context.PACK_SELECTION)
	{
	}
}

function onMouseUpCanvas($event)
{
	if(gameCtx == Context.INGAME)
	{
		if(sentences)
			sentences.commitPoint($event.offsetX, $event.offsetY);
	}
	else if(gameCtx == Context.PACK_SELECTION)
	{
	}
}

function onMouseWheelUp($event)
{
	if(gameCtx == Context.PACK_SELECTION)
	{
		//TODO need to scroll down
	}
}

function onMouseWheelDown($event)
{
	if(gameCtx == Context.PACK_SELECTION)
	{
		//TODO need to scroll down
	}
}

var myApp = angular.module("AppGame1", []);
myApp.controller("CanvasCtrl", function($scope)
{
	$scope.onClickCanvas   = onClickCanvas;
	$scope.onMouseUpCanvas = onMouseUpCanvas;
	$scope.onMouseMoveCanvas = onMouseMoveCanvas;
});

myApp.controller("form", function($scope)
{
	$scope.operations =
	[ 
		{
			"text"  : "Similitude",
			"value" : Type.SAME
		},

		{
			"text"  : "Contraire",
			"value" : Type.CONTRARY
		},

		{
			"text"  : "Specification",
			"value" : Type.PRECISE
		},

		{
			"text"  : "Generalisation",
			"value" : Type.GENERAL
		}
	];

	$scope.showValue = false;

	$scope.changeRadio = function(value)
	{
		currentType = value;
	};

	$scope.radio = currentType = Type.SAME;

	$scope.submit = function()
	{
		if(gameCtx == Context.INGAME)
		{
			var results = JSON.stringify(sentences.getResults());
			var httpCtx = new XMLHttpRequest();

			httpCtx.onreadystatechange = function()
			{
				if(httpCtx.readyState == 4 && (httpCtx.status == 200 || httpCtx.status == 0))
				{
					getSentencesFromServer(null, httpCtx.responseText);
				}
			}
			httpCtx.open("POST", "/ClientQuery/handlingGame1.php", true);
			httpCtx.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			httpCtx.send("idPrompt=2&idPack=1&results="+results);
			currentSentenceID++;
		}
		else if(gameCtx == Context.PACK_SELECTION)
		{
			//Move to ingame
			var idPack = pack.getIDPack();
			if(idPack != null)
			{
				gameCtx = Context.INGAME;
				$scope.showValue = true;
				promptSentences(idPack, 0, getSentencesFromServer);
			}
		}
		else
		{
			//TODO Maybe we should do a thing here...
		}
	};
});

//The main
window.onload = function()
{
	canvas   = document.getElementById('canvasJeu1');
	ctx      = canvas.getContext('2d');
	canvas.width = window.innerWidth-0.1*window.innerWidth;
	promptListPack();
}
