<?php

require_once __DIR__.'/../Datas/Sentence.php';

class PSQLDatabase
{
	private $_conn=null;

	function __construct()
	{
		$this->_conn = pg_pconnect("host=127.0.0.1 user=postgres dbname=postgres");
	}

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

			$scriptMapping = "SELECT AssociationMots.idGroupeMots1, AssociationMots.idGroupeMots2, AssociationMots.relation FROM AssociationMots, GroupeMots AS GroupeMots1, GroupeMots AS GroupeMots2, PairePhrases WHERE GroupeMots1.idPhrase = PairePhrases.idPhrase1 AND GroupeMots2.idPhrase = PairePhrases.idPhrase2 AND PairePhrases.idPaire = $idPaire;";

			$sent1Result   = pg_query($this->_conn, $scriptSent1);
			$sent2Result   = pg_query($this->_conn, $scriptSent2);
			$mappingResult = pg_query($this->_conn, $scriptMapping);

			$sent1         = array();
			$sent2         = array();
			$mapping       = array();

			while($row = pg_fetch_row($sent1Result))
				array_push($sent1, new WordGroup($row[0], trim($row[1])));

			while($row = pg_fetch_row($sent2Result))
				array_push($sent2, new WordGroup($row[0], trim($row[1])));

            while($row = pg_fetch_row($mappingResult))
				array_push($mapping, new Mapping($row[0], $row[1]));

			return new PairSentences(new Sentence($sent1), new Sentence($sent2), new WordGroupMapping($mapping));
		}
		return null;
	}

	function commitGame1Results($idPack, $idSents, $results)
	{
		$sent = $this->getFromPackSentences($idPack, $idSents);
		//TODO use results
	}
}

?>
