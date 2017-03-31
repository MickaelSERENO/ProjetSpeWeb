<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../Datas/Sentence.php';
require_once __DIR__.'/../Datas/StatsData.php';

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PSQLDatabase
{
	private $_conn=null;

	public function __construct()
	{
		$this->_conn = pg_pconnect("host=127.0.0.1 user=postgres dbname=postgres");
	}

	public function getIDPaireSentences($idPack, $idSents)
	{
		//Get the idPaire from the idPack
		$script       = "SELECT idPaire FROM PackPairesPairePhrases, PackPaires WHERE PackPairesPairePhrases.idPack = PackPaires.id AND idPack = $idPack;";
		$resultScript = pg_query($this->_conn, $script);

		//We want the pairSentences number $idSents
		$paires = pg_fetch_row($resultScript);

		if($paires == null)
			return null;

		$i = 0;
		while($i < $idSents && $row = pg_fetch_row($resultScript))
		{
			$paires = $row;
			$i++;
		}

		if($paires && $i == $idSents)
			return $paires[0];
		return null;
	}

	/*Function use to get the sentence number $idSents from the pack id $idPack*/
	public function getFromPackSentences($idPack, $idSents)
	{
		$idPaire = $this->getIDPaireSentences($idPack, $idSents);
		if($idPaire != null)
		{
			//Scripts
			$scriptSent1 = "SELECT GroupeMots.id, GroupeMots.texte FROM GroupeMots, PairePhrases WHERE GroupeMots.idPhrase = PairePhrases.idPhrase1 AND PairePhrases.idPaire = $idPaire;";
			$scriptSent2 = "SELECT GroupeMots.id, GroupeMots.texte FROM GroupeMots, PairePhrases WHERE GroupeMots.idPhrase = PairePhrases.idPhrase2 AND PairePhrases.idPaire = $idPaire;";

			$sent1Result   = pg_query($this->_conn, $scriptSent1);
			$sent2Result   = pg_query($this->_conn, $scriptSent2);

			$sent1         = array();
			$sent2         = array();

			while($row = pg_fetch_row($sent1Result))
				array_push($sent1, new WordGroup($row[0], trim($row[1])));

			while($row = pg_fetch_row($sent2Result))
				array_push($sent2, new WordGroup($row[0], trim($row[1])));

			return new PairSentences(new Sentence($sent1), new Sentence($sent2));
		}
		
		return null;
	}

	public function createHistoricGame1($userID, $idPack)
	{
		//Insert a new row in Historique table
		$dateStr = date('Y-m-d H:i:s');
		$scriptInsertToHisto = "INSERT INTO Historique(idHisto, idEleve, idGame, jour) VALUES (DEFAULT, '$userID', ''Game1'', $dateStr) RETURNING (idHisto);";
		$resultInsertToHisto = pg_query($this->_conn, $scriptInsertToHisto);

		//Get the idHisto thanks to RETURNING operation
		$idHisto = pg_fetch_row($resultInsertToHisto)[0];						 

		//Use cookies
	}

	//Commit to the cookies
	public function commitGame1ResultsCookies($idPack, $idSents, $results)
	{
		//Commit to cookies
		
		$encoders    = array(new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer  = new Serializer($normalizers, $encoders);

		$cookieTxt = $_COOKIE["game1Result"];
		$arrayResults;
		if($cookieTxt != "")
		{
			$arrayResults = json_decode($_COOKIE["game1Result"]);
			array_push($arrayResults, new Game1Result($idPack, $idSents, $results));
		}
		else
			$arrayResults = array(new Game1Result($idPack, $idSents, $results));

		setcookie("game1Result", $serializer->serialize($arrayResults, 'json'), time()+3600);

		//Get what we should have got
		$idPaire = $this->getIDPaireSentences($idPack, $idSents);
		if($idPaire != null)
		{
			//Get what the user should have got
			$scriptMapping = "SELECT AssociationMots.idGroupeMots1, AssociationMots.idGroupeMots2, AssociationMots.relation FROM AssociationMots, GroupeMots AS GroupeMots1, GroupeMots AS GroupeMots2, PairePhrases WHERE GroupeMots1.idPhrase = PairePhrases.idPhrase1 AND GroupeMots2.idPhrase = PairePhrases.idPhrase2 AND PairePhrases.idPaire = $idPaire;";

			$mappingResult = pg_query($this->_conn, $scriptMapping);
			$mappingArray  = array();

			//And returns it
            while($row = pg_fetch_row($mappingResult))
				array_push($mappingArray, new Mapping($row[0], $row[1], $row[2]));

			return new WordGroupMapping($mappingArray);
		}
		return null;
	}

	public function commitGame1Results($idPack, $idSent, $result)
	{
		//Get the idPaire from the idPack
		$sentences = $this->getFromPackSentences($idPack, $idSent);
		if($sentences)
		{
			//Commit result
			foreach($result as $r)
			{
				$commitScript = "INSERT INTO EleveResultG1(idGame1, idWord1, idWord2) VALUE({$sentences->getSent1()->getWordArray()[$r[0]]->getID()}, {$sentences->getSent2()->getWordArray()[$r[1]]->getID()}, {$r[2]});";
			}
		}
	}

	public function getAllFromListStudents($idTeacher)
	{
		$result = array();
		$script = "SELECT id, nom, prenom, nbGame1, nbGame2 FROM Eleve, EleveClasse WHERE Eleve.id = EleveClasse.idEleve AND EleveClasse.mailClasse = '$idTeacher';";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Student($row[0], trim($row[1]), trim($row[2]), trim($row[3]), trim($row[4])));

		return $result;
	}

	public function getHistoricFromListStudent($idTeacher)
	{
		$result = array();
		$script = "SELECT idHisto, Eleve.id, nom, prenom, idGame, jour FROM Eleve, EleveClasse, Historique WHERE EleveClasse.idEleve = Historique.idEleve AND Eleve.id = EleveClasse.idEleve AND mailClasse = '$idTeacher';";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Historic($row[0], $row[1], trim($row[2]), trim($row[3]), $row[4], date('Y-m-d H:i:s', trim($row[5]))));

		return $result;
	}
}
?>
