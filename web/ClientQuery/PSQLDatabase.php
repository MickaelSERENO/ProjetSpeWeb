<?php

require_once __DIR__.'/../Datas/Sentence.php';
require_once __DIR__.'/../Datas/StatsData.php';

class PSQLDatabase
{
	private $_conn=null;

	function __construct()
	{
		$this->_conn = pg_pconnect("host=127.0.0.1 user=postgres dbname=postgres");
	}

	/*Function use to get the sentence number $idSents from the pack id $idPack*/
	public function getFromPackSentences($idPack, $idSents)
	{
		//Get the idPaire from the idPack
		$script       = "SELECT idPaire FROM PackPairesPairePhrases, PackPaires WHERE PackPairesPairePhrases.idPack = PackPaires.id AND idPack = $idPack;";
		$resultScript = pg_query($this->_conn, $script);

		//We want the pairsentences number $idSents
		$paires       = pg_fetch_row($resultScript);
		if($paires)
		{
			if(!array_key_exists($idSents, $paires))
				return null;

			$idPaire     = $paires[$idSents];
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
		
		return null;
	}

	function getAllFromListStudents($idTeacher)
	{
		$result = array();
		$script = "SELECT id, nbGame1, nbGame2 FROM Eleve, EleveClasse WHERE Eleve.id = EleveClasse.idEleve AND EleveClasse.mailClasse = '$idTeacher';";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Student($row[0], $row[1], $row[2]));

		return result;
	}

	function commitGame1Results($idPack, $idSents, $idHisto, $results)
	{
		//Get the idPaire from the idPack
		$script       = "SELECT idPaire FROM PackPairesPairePhrases, PackPaires WHERE PackPairesPairePhrases.idPack = PackPaires.id AND idPack = $idPack;";
		$resultScript = pg_query($this->_conn, $script);

		//We want the pairsentences number $idSents
		$paires       = pg_fetch_row($resultScript);
		if($paires)
		{
			if(!array_key_exists($idSents, $paires))
				return null;

			$idPaire     = $paires[$idSents];
			//Scripts, get the idwords
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


			//Commit result
			foreach($result as $r)
			{
				$commitScript = "INSERT INTO EleveResultG1(idGame1, idWord1, idWord2) VALUE({$sent1[$r[0]]->getID()}, {$sent2[$r[1]]->getID()}, {$r[2]});";
			}

			//Now get what the user should have got
			$scriptMapping = "SELECT AssociationMots.idGroupeMots1, AssociationMots.idGroupeMots2, AssociationMots.relation FROM AssociationMots, GroupeMots AS GroupeMots1, GroupeMots AS GroupeMots2, PairePhrases WHERE GroupeMots1.idPhrase = PairePhrases.idPhrase1 AND GroupeMots2.idPhrase = PairePhrases.idPhrase2 AND PairePhrases.idPaire = $idPaire;";

			$mappingResult = pg_query($this->_conn, $scriptMapping);
			$mappingArray  = array();

			//And returns it
            while($row = pg_fetch_row($mappingResult))
				array_push($mappingArray, new Mapping($row[0], $row[1], $row[2]));
			return new WordGroupMapping($mapping);
		}
		
		return null;
	}
}

?>
