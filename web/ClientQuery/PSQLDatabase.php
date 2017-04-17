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
		$this->_conn = pg_pconnect("host=127.0.0.1 user=postgres dbname=postgres password=postgresql");
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

		$script = "(SELECT Historique.idHisto, idGame, jour FROM Historique, EleveHistoG1, EleveClasse WHERE EleveHistoG1.idEleve=EleveClasse.idEleve AND Historique.idHisto = EleveHistoG1.idHisto AND EleveClasse.mailClasse = '$idTeacher') UNION
		           (SELECT Historique.idHisto, idGame, jour FROM Historique, ClasseHistoG2 WHERE ClasseHistoG2.idHisto = Historique.idHisto AND mailProf = '$idTeacher') ORDER BY jour ASC;";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Historic($row[0], $row[1], date('Y-m-d H:i:s', trim($row[2]))));

		return $result;
	}

	public function getHistoricFromStudent($idStudent, $idTeacher)
	{
		$result = array();
		$script = "(SELECT Historique.idHisto, idGame, jour FROM Historique, EleveHistoG1 WHERE idEleve='$idStudent' AND Historique.idHisto = EleveHistoG1.idHisto) UNION
		           (SELECT Historique.idHisto, idGame, jour FROM Historique, ClasseHistoG2 WHERE ClasseHistoG2.idHisto = Historique.idHisto AND mailProf = '$idTeacher') ORDER BY jour ASC;";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Historic($row[0], $row[1], date('Y-m-d H:i:s', trim($row[2]))));

		return $result;
	}
	
	public function getStudentCara($idStudent)
	{
		$script = "SELECT nom, prenom, nbGame1, nbGame2 FROM Eleve WHERE Eleve.id='$idStudent';";
		$resultScript = pg_query($this->_conn, $script);

		if($row=pg_fetch_row($resultScript))
			return new Student($idStudent, trim($row[0]), trim($row[1]), $row[2], $row[3]);
		return null;
	}
	
	
	/*Function which will put the user in the database, he still have to verify his mail adress*/
	public function registerTeacherClass($mail, $pseudo, $passhash, $verify, $code)
	{
		if($verify)
			$script       = "INSERT INTO Classe VALUES('$pseudo', '$mail', '$passhash', 't', '$code');";
		else
			$script       = "INSERT INTO Classe VALUES('$pseudo', '$mail', '$passhash', 'f', '$code');";
		
		$resultScript = pg_query($this->_conn, $script);
		/*$data = array('nom'=>$pseudo, 'mail'=>$mail, 'password'=>$passhash, 'verifiedUser'=>$verify, 'code'=>$code);
		$res = pg_insert($this->_conn, 'Classe', $data);
		if ($res)
			echo "Les données POSTées ont pu être enregistrées avec succès.\n";
		else 
			echo "Il y a un problème avec les données.\n";*/
		
		pg_query($this->_conn, 'COMMIT');
		return true;
	}
	
	/*Update the boolean to true, the account is now validated*/
	public function verifyTeacherClass($mail)
	{
		$script       = "UPDATE Classe SET verifiedUser = 't' WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		pg_query($this->_conn, 'COMMIT');
		
		return true;
	}
	
	public function updateVerifCode($mail, $code)
	{
		$script       = "UPDATE Classe SET code = '$code' WHERE mail ='$mail';";
		$resultScript = pg_query($this->_conn, $script);
		pg_query($this->_conn, 'COMMIT');
		
		return true;
	}
	
	public function isVerifiedUserMail($mail)
	{
		$script       = "SELECT verifiedUser FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		if(row[0])
			return true;
		else
			return false;
	}
	
	public function isVerifiedUserPseudal($pseudal)
	{
		$script       = "SELECT verifiedUser FROM Classe WHERE nom = '$pseudal';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		if(row[0])
			return true;
		else
			return false;
	}
	
	public function compare_code($mail, $code)
	{
		$script       = "SELECT code FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		if(strcmp($row[0], $code)==0)
			return true;
		else
			return false;
	}
	
	public function getPseudalFromMail($mail)
	{
		$script       = "SELECT nom FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		$row = pg_fetch_row($resultScript);
		return $row[0];
	}
	
	public function getMailFromPseudal($pseudal)
	{
		$script       = "SELECT mail FROM Classe WHERE nom = '$pseudo';";
		$resultScript = pg_query($this->_conn, $script);
		$row = pg_fetch_row($resultScript);
		return $row[0];
	}
	
	public function existPseudo($pseudo)
	{
		$script       = "SELECT CASE WHEN EXISTS (SELECT mail FROM Classe WHERE nom = '$pseudo') THEN CAST(1 AS BIT) ELSE CAST(0 AS BIT) END;";
		$resultScript = pg_query($this->_conn, $script);
		$row=pg_fetch_row($resultScript);
		
		if($row[0])
			return true;
		else
			return false;
	}
	
	public function existMail($mail)
	{
		$script       = "SELECT CASE WHEN EXISTS (SELECT nom FROM Classe WHERE mail = '$mail') THEN CAST(1 AS BIT) ELSE CAST(0 AS BIT) END;";
		$resultScript = pg_query($this->_conn, $script);
		$row=pg_fetch_row($resultScript);
		/*$script       = "SELECT nom FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);*/
	
		if($row[0])
			return true;
		else
			return false;
	}
	
	public function cmpPassHashPseudal($passTest, $pseudal)
	{
		$script       = "SELECT password FROM Classe WHERE nom = '$pseudal';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return row[0];
	}
	
	public function cmpPassHashMail($passTest, $mail)
	{
		$script       = "SELECT password FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return row[0];
	}
	
	public function getPassHash($mail)
	{
		$script       = "SELECT password FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return row[0];
	}
	
	public function updatePassHash($mail, $passHash)
	{
		$script       = "UPDATE Classe SET password = '$passHash' WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		return true;
	}

	public function reinitStudentPasswd($studentID, $passHash)
	{
		$script = "UPDATE Eleve SET password = '$passHash' WHERE studentID = '$studentID';";
		$resultScript = pg_query($this->_conn, $script);
		
		return true;
	}
}
?>
