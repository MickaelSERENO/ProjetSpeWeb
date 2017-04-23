const SPACE_BLANK = 50;
const START_MARGE = 10;
var canvas;
var ctx;
var playerArray = new Array();
var phraseJeu = ['Ceci','est', 'la', 'phrase', 'de', 'la', 'partie.'];
var phraseArray = new Array();


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
	
	countdown(timeStamp)
	{
		ctx.clearRect(80,80,200,200);
		ctx.fillText(this.sec+" : "+this.cen,200,100);
		this.cen--;
		if(this.cen<=0)
		{
			this.sec--;
			this.cen = 10;
		}
		if(this.sec>=0)
			requestAnimationFrame(this.countdown());
		else
			cancelAnimationFrame(this.countdown());
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
		ctx.font = "35px Arial";
		ctx.strokeStyle = "Green";
		ctx.shadowBlur = 10;
		ctx.shadowColor = "black";
		ctx.textAlign='center';
		ctx.textBaseline="middle";
		ctx.fillText(this.text,abX+2+this.w/2,abY+this.h/2);
		ctx.strokeStyle = "Green";
		ctx.strokeRect(abX,abY,this.w+5,this.h);
		this.x = abX;
		this.y = abY;
		ctx.shadowOffsetX=2;
		ctx.shadowOffsetY=2;
		//ctx.shadowBlur = 0;
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
				/*ctx.fillRect(phraseArray[i].x,phraseArray[i].y,phraseArray[i].w,phraseArray[i].h);
				ctx.clearRect(phraseArray[i].x,phraseArray[i].y,phraseArray[i].w+6,phraseArray[i].h);*/
				phraseArray[i].text="NewText";
				phraseArray[i].createSentence(phraseArray[i].x,phraseArray[i].y+100);
				console.log("done: "+phraseArray[i].text +" x: "+phraseArray[i].x+" y: "+phraseArray[i].y+" w: "+phraseArray[i].w);
				//phraseArray[i].createSentence(phraseArray[i].x,phraseArray[i].y);
				var cnw =createNewWord(phraseArray[i]);
				//playerArray[0].wordTaped.
				cnw.createSentence(phraseArray[i].x,phraseArray[i].y+100);
				break;				
			}
	}
}

function createNewWord(phraseBlock)
{
	wBlock = new TextBlock("NEW BLOCK",phraseBlock.hgt);
	console.log(wBlock.text+" : "+wBlock.x+"  , "+wBlock.w);
	return wBlock;

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
	//drawSentence();
	var ms =10;
	for(var i=0;i<phraseArray.length;i++)
	{
		phraseArray[i].createSentence(ms,160);
		ms += phraseArray[i].w+SPACE_BLANK;
	}
	var chr = new Chrono(100);
	playerArray.push(new Player("j1",1,10));
	//ctx.fillText(chr.sec+" : "+chr.cen,200,100);
	//requestAnimationFrame(chr.countdown);
	//chr.countdown();
}