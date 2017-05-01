const SPACE_BLANK = 50;
const START_MARGE = 10;
var timeY =200;
var canvas;
var ctx;
var playerArray = [];
var phraseJeu = ['Ceci','est', 'la', 'phrase', 'de', 'la', 'partie.'];
var phraseArray = [];
var phrasePlayerArray = [];
var requestID =0;

/* Pour les animations du countdown */
window.requestAnimFrame = function(){
    return (
        window.requestAnimationFrame       || 
        window.webkitRequestAnimationFrame || 
        window.mozRequestAnimationFrame    || 
        window.oRequestAnimationFrame      || 
        window.msRequestAnimationFrame     || 
        function(callback){
            window.setTimeout(callback, 1000 / 60);
        }
    );
}();

var app = angular.module('demoApp', []);

app.controller('monCtrl', function($scope){
	$scope.valeur = 12;
	$scope.bgC = 'blue';
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

app.controller('CanvasCtrl', function($scope){
	$scope.detectBlock = detectBlock;
});

class Player
{
	constructor(nom,id,table)
	{
		this.name = nom;
		this.id = id;
		if (this.wordTaped == undefined)
		{
			this.wordTaped =[]
		}
		this.wordTaped.push(table);
	}
	
	get name()
	{ return this._name; }
	
	get id() { return this._id; }
	
	get wordTaped() { return this._wordTaped;}
	
	set name(n) {this._name = n;}
	
	set id(iden) {this._id = iden;}
	
	set wordTaped(elementT) 
	{
		this.add(elementT);
	}
	
	add(newObj)
	{
		this._wordTaped.push(newObj);
	}
};

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
			timeY*= 1.01;
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
		return this;
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
		ctx.font = "35px Arial";
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

/* Recupere les coordonnes exactes du click dans le canvas */
function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
      x: evt.clientX - rect.left,
      y: evt.clientY - rect.top
    };
}

/* Event pour determiner si on clique sur un des mots de la phrase */
function detectBlock(evt)
{
	mousePos = getMousePos(canvas,evt);
	for(i=0;i<phraseArray.length;i++)
	{
		posX = phraseArray[i].x;
		posY = phraseArray[i].y;
		prec = phraseArray[i].w;
		suiv = phraseArray[i].h;
		if( (mousePos.x>posX) && (mousePos.x<(posX+prec)) && (mousePos.y>posY) && (mousePos.y<(posY+suiv)) )
			{
				console.log("done: "+phraseArray[i].text +" x: "+phraseArray[i].x+" y: "+phraseArray[i].y+" w: "+phraseArray[i].w+"  time: "+timeY);
				createInputWord(phraseArray[i]);
				break;				
			}
	}
}

function createNewWord(newText)
{
	let wBlock = new TextBlock(newText,40);
	console.log(wBlock.text+" : "+wBlock.x+"  , "+wBlock.w+" , "+wBlock.h);
	return wBlock;
}

function createInputWord(phraseBlock)
{
	var input = document.createElement('input');
	input.type = 'text';
	input.style.position = 'fixed';
	input.style.left = phraseBlock.x+7+'px';
	input.style.top = phraseBlock.y + phraseBlock.h + 10+'px';
	var cnw = createNewWord("New Text");
	cnw.x = phraseBlock.x;
	cnw.y = phraseBlock.y; 
	phrasePlayerArray.push(cnw);
	input.onkeydown = enterWord;
	document.body.appendChild(input);
	input.focus();
}

function enterWord(evt)
{
	/* Si la touche Entree est appuyee */
	console.log("keycode : "+evt.keyCode);
	if(evt.keyCode === 13)
	{
		console.log(this.value);
		phrasePlayerArray[phrasePlayerArray.length-1].text = this.value;
		phrasePlayerArray[phrasePlayerArray.length-1].createSentence(phrasePlayerArray[phrasePlayerArray.length-1].x,/*phraseArray[i].y+100*/timeY);
		document.body.removeChild(this);
	}
}

/* main pour charger le canvas */
window.onload = function()
{
	// Initialisation du canvas
    canvas = document.getElementById("canvasJeu2");
    ctx = canvas.getContext("2d");
	ctx.canvas.width = window.innerWidth;
	ctx.canvas.height = window.innerHeight;
	ctx.font = "35px Arial";
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
	
	//playerArray.push(new Player("j1",1,10));
}