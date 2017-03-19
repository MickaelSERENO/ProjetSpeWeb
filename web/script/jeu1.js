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
	drawSentence(ctx, jsonData.sent1, 0, 100);
	drawSentence(ctx, jsonData.sent2, 0, 300);
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

//Le main
window.onload = function()
{
	var canv = document.getElementById('canvasJeu1');
	var ctx  = canv.getContext('2d');
	promptSentences(2, 1, getSentencesFromServer, ctx);
}
