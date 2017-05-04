<?php
namespace ClientQuery;

//Load symfony
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/PSQLDatabase.php';
require_once __DIR__.'/../Datas/Sentence.php';

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use PSQLDatabase;



//header("Content-Type:text/plain.txt");
$encoders    = array(new JsonEncoder());
$normalizers = array(new ObjectNormalizer());
$serializer  = new Serializer($normalizers, $encoders);

$input = explode("&", json_decode(file_get_contents('php://input')));

for($i=0; $i<count($input); $i=$i+1)
{
	$inputs[$i] = explode("=", $input[$i]);
}

for($i=0; $i<count($inputs); $i=$i+1)
{
	switch($inputs[$i][0]){
		/*L'action demandée au serveur*/
		case "action":
			$action = $inputs[$i][1];
			break;
		/*Le nom de la partie.*/
		case "gameName":
			$gameName = $inputs[$i][1];
			break;
		/*L'id du client (son mail)*/
		case "idPlayer":
			$idPlayer = $inputs[$i][1];
			break;
		/*Le pseudo du joueur*/
		case "namePlayer":
			$namePlayer = $inputs[$i][1];
			break;
		/*La phrase à reformuler si "newGame", ou le groupe de mots proposé pour "addSent"*/
		case "sentence":
			$sent = $inputs[$i][1];
			break;
		/*Position du premier mot à reformuler dans la phrase de base.*/
		case "borneInf":
			$borneInf = (int)($inputs[$i][1]);
			break;
		
		/*Position du premier mot à reformuler dans la phrase de base.*/
		case "borneSup":
			$borneSup = (int)($inputs[$i][1]);
			break;
	}
}

$file = fopen('parties.txt', 'r+');
if(!isset($action)) echo "Erreur";

switch($action)
{
	/*Crée une partie, sauf si elle existe vraiment.
	Nécessite le nom de la partie, l'id et le nom du joueur, ainsi que la phrase de base*/
	case "newGame":
		if(gameExists($file, $gameName))
		{
			echo "gameExists";
		}
		else
		{
			addGame($file, $gameName, $idPlayer, $namePlayer, $sent);
			echo "OK";
		}
		break;
		
		
	/*Un joueur est ajouté à la partie, sauf s'il y appartient déjà, ou si la partie n'existe pas*/
	/*nécessite le nom de la partie, le nom et l'id du joueur. Renvoie la phrase de la partie*/
	case "newPlayer":
		echo joinGame($file, $gameName, $idPlayer, $namePlayer);
		break;
	/*Enlève un joueur de sa partie. Nécessite le nom du joueur et le nom de la partie*/
	case "exitPlayer":
		echo deletePlayer($file, $gameName, $idPlayer);
		break;	
	/*Renvoie la liste des noms de parties associés aux nombres de participants, sous le format :
	/*Nompartie1:nb1\n
	Nompartie2:nb2\n etc...
	Renvoie noGame s'il n'y a pas de parties.*/
	case "getListGames":
		$res = getGames($file);
		if(count($res) == 0)
			echo "noGame";
		else{
			for($i=0; $i<count($res); $i++)
			{
				echo $res[$i]."\n";
			}
		}
		break;
	/*Renvoie le nombres de joueurs qui jouent à une partie.
	Nécessite le nom de la partie.*/
	case "getNbPlayers":
		
		echo getNbPlayers($file, $gameName);
		break;
	/*Renvoie la phrase à reformuler sous forme de String
	Nécessite le nom de la partie.*/
	case "getFirstSent" :
		echo getFirstSentence($file, $gameName);
		break;
		
	/*Renvoie les phrases proposées par le joueur*/
	/*Nécessite le nom de la partie et l'id du joueur*/
	case "getPlayerSents" :
		$tab = getPlayerSentences($file, $gameName, $idPlayer);
		if(count($tab) == 0)
			echo "noSent";
		/*else if(strcmp($tab, "playerNotFound") == 0 || strcmp($tab, "gameNotFound")==0)
		{
			echo $tab;
		}*/
		else{
			for($i=0; $i<count($tab); $i++)
			{
				echo $tab[$i];
			}
		}
		break;
	
	/*Renvoie les phrases proposées par les autres joueurs*/
	/*Nécessite le nom de la partie et le nom des joueurs*/
	case "getOtherSents" :
		$tab = getOtherSentences($file, $gameName, $idPlayer);
		if(count($tab) == 0)
			echo "noSent";
		else{
			for($i=0; $i<count($tab); $i++)
			{
				echo $tab[$i]."\n";
			}
		}
		break;
	
	
	/*Ajoute la phrase proposée par un joueur*/
	/*Nécessite le nom de la partie, l'id du joueur, le groupe de mots proposé par le joueur
	et les bornes de la position de ces mots dans la phrase de base.
	Si un seul mot est remplacé, borneInf = borneSup*/
	case "addSent" :
		$firstSent = explode(" ", getFirstSentence($file, $gameName));
		if($borneInf > $borneSup || $borneInf < 1 || $borneSup <1 || $borneSup > count($firstSent) || $borneInf > count($firstSent))
		{
			echo "errorBornes";
			break;
		}
		else
		{
			$newSent = "";
			for($i=0; $i<count($firstSent); $i++)
			{
				if($i < $borneInf-1 || $i >= $borneSup)
				{
					$newSent = $newSent.$firstSent[$i];
				}
				else{
					$newSent = $newSent.$sent;
					$i=$borneSup-1;
				}
				if($i < count($firstSent)-1)
					$newSent = $newSent." ";
			}
			echo addSentence($file, $gameName, $idPlayer, $newSent);
			break;
		}
}

fclose($file);

function getFirstSentence($file, $gameName)
{
	rewind($file);
	do{
		$line = fgets($file);
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{
			$line = fgets($file);
			return explode(":", fgets($file))[1];
		}
	}while(!feof($file) && $line != false);
	return false;

}

function getPlayerSentences($file, $gameName, $idPlayer)
{
	rewind($file);
	$sents = array();
	$line = "";
	while(!feof($file)){
		$line = fgets($file);
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{
			while(strcmp($line, "+++\n") != 0)
			{
				$line = fgets($file);
				$expl = explode(":", $line);
				if(strcmp($expl[0], "Player")==0 && strcmp($expl[1], $idPlayer)==0)
				{
					$line = fgets($file);
					$line = fgets($file);
					while(strcmp($line, "---\n") != 0 && strcmp($line, "---") != 0)
					{
						array_push($sents, $line);
						$line=fgets($file);
					}
					return $sents;
				}
			}
			return "playerNotFound";
		}
	}
	return "gameNotFound";
}

function getOtherSentences($file, $gameName, $idPlayer)
{
	rewind($file);
	$sents = array();
	$line = "";
	while(!feof($file)){
		$line = fgets($file);
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{
			while(strcmp($line, "+++\n") != 0)
			{
				$line = fgets($file);
				$expl = explode(":", $line);
				if(strcmp($expl[0], "Player")==0 && strcmp($expl[1], $idPlayer)!=0)
				{
					$line=fgets($file);
					$line=fgets($file);
					while(strcmp($line, "---\n") != 0)
					{
						array_push($sents, $line);
						$line=fgets($file);
					}
				}
			}
			return $sents;
		}
	}
	return "gameNotFound";
}

/*Retourne la liste des jeux et leur nombre de joueur*/
function getGames($file)
{	
	$games = array();
	rewind($file);
	$line = "";
	while(strcmp(explode(":", $line)[0], "Game")!=0 && !feof($file)){
		$line = fgets($file);
	}
	$line = explode("\n", $line)[0];
	$line2 = explode("\n", fgets($file))[0];
	if(!feof($file))
	{	
		$game = explode(":", $line)[1].":".explode(":", $line2)[1];
		array_push($games, $game);
	}
	do{
		if(strcmp(fgets($file), "+++\n")==0 && !feof($file))
		{
			$line = "";
			while(strcmp(explode(":", $line)[0], "Game")!=0 && !feof($file)){
				$line = fgets($file);
			}
			$line = explode("\n", $line)[0];
			$line2 = explode("\n", fgets($file))[0];
			if(!feof($file))
			{
				$game = explode(":", $line)[1].":".explode(":", $line2)[1];
				array_push($games, $game);
			}
		}
	}while(!feof($file));
	return $games;
}



function getNbPlayers($file, $gameName)
{	
	$nbPlayers = 0;
	rewind($file);
	do{
		$line = fgets($file);
		if(strcmp(explode(":", $line)[0], "Game")==0 && strcmp(explode(":", $line)[1], $gameName."\n")==0)
		{
				$nbPlayers = explode(":", fgets($file))[1];
		}
	}while(!feof($file));
	return $nbPlayers;

}

/*return true if the game named $gameName exists, false otherwise*/
function gameExists($file, $gameName)
{
	rewind($file);
	do{
		$line = fgets($file);
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{
			return true;
		}
	}while(!feof($file) && $line != false);
	return false;
}

//Add one new game with his sentence and his new player at the end of the file.
function addGame($file, $gameName, $idPlayer, $namePlayer, $sent)
{
	fseek($file, 0, SEEK_END);
	fputs($file, "\nGame:".$gameName."\n");
	fputs($file, "nbPlayers:1\n");
	fputs($file, "Sentence:".$sent."\n");
	fputs($file, "Player:".$idPlayer.":".$namePlayer."\n");
	fputs($file, "Score:0\n");
	fputs($file, "---\n");
	fputs($file, "+++\n");
}

/*Delete the game named $gameName in the file*/
function deleteGame($file, $gameName)
{
	$count = 0;
	rewind($file);
	do{
		$count++;
		$line = fgets($file);
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{
			$first = $count;
			while(!feof($file) && $line != false){
				$count++;
				$line = fgets($file);
				if(strcmp($line, "+++\n")==0)
				{
					deleteLines($file, $first, $count);
					return "OK";
				}
			}
			deleteLines(file, $first, $count);
			return "OK";
		}
	}while(!feof($file) && $line != false);
	return "gameNotFound";
}

/*Delete one player of the game named $gameName, and the entire game if he was alone
Can return "OK", "gameNotFound" or "playerNotFound*/
function deletePlayer($file, $gameName, $idPlayer)
{
	rewind($file);
	$count = 0;
	do{
		$count++;
		$line = fgets($file);
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{	
			$count++;
			$lineNbPlayers = $count;
			$nbPlayers = explode(":", fgets($file))[1];
			if($nbPlayers == 1){
				$count++;
				$line = fgets($file);
				if(strcmp($idPlayer, explode(":", fgets($file))[1]) == 0)
				{
					return deleteGame($file, $gameName);
				}
				else return "playerNotFound";
			}
			else
			{
				while(!feof($file) && $line != false)
				{
					$line = fgets($file);
					$count++;
					if(strcmp(explode(":", $line)[0],"Player")==0 && strcmp(explode(":", $line)[1], $idPlayer)==0)
					{
						$first = $count;
						while(!feof($file) && $line != false)
						{
							$line = fgets($file);
							$count++;
							if(strcmp($line, "---\n")==0)
							{
								deleteLines($file, $first, $count);
								editNumberPlayers($file, $lineNbPlayers, $nbPlayers-1);
								return "OK";
							}
						}
					}
				}
				return "playerNotFound";
			}
		}
	}while(!feof($file) && $line != false);
	return "gameNotFound";

}

/*Edit the line number $line of the file which should contain
the previous number of players in a game, and replace it with $nb.*/
function editNumberPlayers($file, $line, $nb){

	rewind($file);
	for($i=1; $i<$line; $i++)
	{
		$line = fgets($file);
		fputs($file, "nbPlayers:".(string)((int)$nb)."\n");
	}
}

/*TODO : Change the score of the player in the game*/
function editScore($file, $line, $score){
	rewind($file);
	for($i=1; $i<$score; $i++)
	{
		$line = fgets($file);
		fputs($file, "Score:".(string)((int)$nb)."\n");
	}
}

function joinGame($file, $gameName, $idPlayer, $namePlayer)
{
	rewind($file);
	$lines = array();
	$i=0;
	while(!feof($file))
	{
		$lines[$i] = fgets($file);
		if(strcmp("Player", explode(":", $lines[$i])[0]) == 0 && strcmp(explode(":", $lines[$i])[1], $idPlayer)==0)
		{
			return "playerAlreadyExists";
		}
		$i=$i+1;
	}
	rewind($file);
	ftruncate($file, 0);
	
	$answer = "gameNotFound"; //message à retourner.
	for($i=0; $i<count($lines); $i++)
	{
		fputs($file, $lines[$i]);
		if(strcmp(explode(":", $lines[$i])[0], "Game")==0 && strcmp(explode(":", $lines[$i])[1], $gameName."\n")==0)
		{
			$i++;
			fputs($file, "nbPlayers:".(string)((int)(explode(":", $lines[$i])[1])+1)."\n");
			$i++;
			$answer = explode(":", $lines[$i])[1];
			while(strcmp($lines[$i], "+++\n")!=0)
			{
				fputs($file, $lines[$i]);
				$i++;
			}
			fputs($file, "Player:".$idPlayer.":".$namePlayer."\n");
			fputs($file, "Score:0\n");
			fputs($file, "---\n");
			fputs($file, "+++\n");
		}
	}
	return $answer;
}

function addSentence($file, $gameName, $idPlayer, $newSent)
{
	rewind($file);
	$countLine = 0;
	$numLine = 0;
	$tab = array();
	while(!feof($file)){
		$line = fgets($file);
		$tab[$countLine] = $line;
		$countLine += 1;
		if(strcmp($line, "Game:".$gameName."\n")==0)
		{
			while(strcmp($line, "+++\n") != 0)
			{	
				$line = fgets($file);
				$tab[$countLine] = $line;
				$countLine += 1;
				$expl = explode(":", $line);
				if(strcmp($expl[0], "Player")==0 && strcmp($expl[1], $idPlayer)==0)
				{
					while(strcmp($line, "---\n") != 0)
					{
						$line=fgets($file);
						$tab[$countLine] = $line;
						$countLine += 1;
					}
					$numLine = $countLine-1;
				}
			}
		}
	}
	
	rewind($file);
	ftruncate($file, 0);
	for($i=0; $i<count($tab); $i++)
	{	
		if($i == $numLine){
			fputs($file, $newSent."\n");
		}
		fputs($file, $tab[$i]);
	}

}

/*TODO*/
function getScore($file, $gameName, $idPlayer)
{;
}

/*Delete line number $num of the file (first line = 1)*/
function deleteLine($file, $num){
	rewind($file);
	ftruncate($file, 0);
	$lines = array();
	$i=0;
	while(!feof($file))
	{
		array_push($lines, fgets($file));
		$i=$i+1;
	}
	rewind($file);
	for($i=0; $i<count($lines); $i++)
	{
		if($i!=$num-1)
			fputs($file, $lines[$i]);
	}
}

/*Delete all the line between the number $first and the number $last (both included) in the file*/
function deleteLines($file, $first, $last){
	rewind($file);
	$lines = array();
	$i=0;
	while(!feof($file))
	{
		$lines[$i] = fgets($file);
		$i=$i+1;
	}
	rewind($file);
	ftruncate($file, 0);
	for($i=0; $i<count($lines); $i++)
	{
		if($i<$first-1 || $i>=$last)
		{	
			fputs($file, $lines[$i]);
		}
	}
}
?>