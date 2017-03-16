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
	httpCtx.overrideMimeType('application/json');
	httpCtx.open("POST", "handlingGame1.php", true);
	httpCtx.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	httpCtx.send("idPrompt=1&idPack="+idPackSentence+"$idSent="+idSentence);
}

function getSentencesFromServer(sent, jsonData)
{
	print(jsonData);
}

function drawSentence(ctx, sentence, px, py)
{
}

//$locationProvider.html5Mode(true);

//var canv = document.getElementById('canvasJeu1');
//var ctx  = canv.getContext('2d');
//var packSentenceID = location.search().packSentence();

promptSentences(2, 1, getSentencesFromServer);
