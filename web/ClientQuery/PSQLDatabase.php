<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../Datas/Sentence.php';
require_once __DIR__.'/../Datas/StatsData.php';
require_once __DIR__.'/../Datas/Game1Data.php';

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
			$scriptSent1 = "SELECT GroupeMots.id, GroupeMots.texte, idPhrase1 FROM GroupeMots, PairePhrases WHERE GroupeMots.idPhrase = PairePhrases.idPhrase1 AND PairePhrases.idPaire = $idPaire;";
			$scriptSent2 = "SELECT GroupeMots.id, GroupeMots.texte, idPhrase2 FROM GroupeMots, PairePhrases WHERE GroupeMots.idPhrase = PairePhrases.idPhrase2 AND PairePhrases.idPaire = $idPaire;";

			$sent1Result   = pg_query($this->_conn, $scriptSent1);
			$sent2Result   = pg_query($this->_conn, $scriptSent2);

			$idSent1 = null;
			$idSent2 = null;

			$sent1         = array();
			$sent2         = array();

			while($row = pg_fetch_row($sent1Result))
			{
				$idSent1 =$row[2];
				array_push($sent1, new WordGroup($row[0], trim($row[1])));
			}

			while($row = pg_fetch_row($sent2Result))
			{
				$idSent2 =$row[2];
				array_push($sent2, new WordGroup($row[0], trim($row[1])));
			}

			return new PairSentences(new Sentence($idSent1, $sent1), new Sentence($idSent2, $sent2));
		}
		
		return null;
	}

	public function getResultFromPackSentences($idPack, $idSents)
	{
		$pairSentences = $this->getFromPackSentences($idPack, $idSents);
		$idSent1 = $pairSentences->getSent1()->getId();
		$idSent2 = $pairSentences->getSent2()->getId();

		//TODO improve, don't need EVERY association
		$script = "SELECT AssociationMots.idGroupeMots1, AssociationMots.idGroupeMots2, AssociationMots.relation FROM AssociationMots, GroupeMots as GM1, GroupeMots as GM2 WHERE AssociationMots.idGroupeMots1 = GM1.id AND AssociationMots.idGroupeMots2 = GM2.id  AND GM1.idPhrase = $idSent1 AND GM2.idPhrase = $idSent2 ORDER BY GM1.id ASC;";

		$scriptResult = pg_query($this->_conn, $script);
		$result = array();

		while($row = pg_fetch_row($scriptResult))
			array_push($result, new Mapping($row[0], $row[1], $row[2]));

		return new ResultSentences($pairSentences->getSent1(), $pairSentences->getSent2(), new WordGroupMapping($result));
	}

	public function createHistoricGame1($results, $userID, $idPack)
	{
		//Insert a new row in Historique table
		$dateStr = date('Y-m-d H:i:s');
		$scriptInsertToHisto = "INSERT INTO Historique(idHisto, idGame, jour) VALUES (DEFAULT, 'Game1', '$dateStr') RETURNING idHisto;";
		error_log($scriptInsertToHisto);
		$resultInsertToHisto = pg_query($this->_conn, $scriptInsertToHisto);

		//Get the idHisto thanks to RETURNING operation
		$idHisto = pg_fetch_row($resultInsertToHisto)[0];						 

		//Create EleveHistoG1

		//Commit results
		$length = count($results);
		for($i=0; $i < $length; $i++)
		{
			$this->commitGame1Results($idHisto, $userID, $idPack, strval($i), $results[$i]);
		}
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

	public function commitGame1Results($idHisto, $userID, $idPack, $idSent, $result)
	{
		//Get the idPaire from the idPack
		$sentences = $this->getFromPackSentences($idPack, $idSent);
		$trueIDSent = $this->getIDPaireSentences($idPack, $idSent);
		$scriptHistoG1 = "INSERT INTO EleveHistoG1(idGame1, idEleve, idHisto, idPack, idPairePhrase) VALUES (DEFAULT, '$userID', '$idHisto', '$idPack', '$trueIDSent') RETURNING idGame1";
		$histoG1Result = pg_query($this->_conn, $scriptHistoG1);
		$idGame1 = pg_fetch_row($histoG1Result)[0];						 
		

		if($sentences)
		{
			//Commit result
			foreach($result as $r)
			{
				if($r[0] != null && $r[1] != null)
				{
					$commitScript = "INSERT INTO EleveResultG1(idGame1, idWord1, idWord2, operation) VALUE('idGame1', '{$sentences->getSent1()->getWordArray()[$r[0]]->getID()}', '{$sentences->getSent2()->getWordArray()[$r[1]]->getID()}', '{$r[2]}');";
					$commitScriptResult = pg_query($this->_conn, $commitScript);
				}
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

		$script = "(SELECT DISTINCT Historique.idHisto, jour, 1 FROM Historique, EleveHistoG1, EleveClasse WHERE EleveHistoG1.idEleve=EleveClasse.idEleve AND Historique.idHisto = EleveHistoG1.idHisto AND EleveClasse.mailClasse = '$idTeacher') UNION
		           (SELECT DISTINCT Historique.idHisto, jour, 2 FROM Historique, ClasseHistoG2 WHERE ClasseHistoG2.idHisto = Historique.idHisto AND mailProf = '$idTeacher') ORDER BY jour ASC;";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Historic($row[0], -1, trim($row[1]), $row[2]));

		return $result;
	}

	public function getHistoricFromStudent($idStudent, $idTeacher)
	{
		$result = array();
		$script = "(SELECT Historique.idHisto, idGame1, jour FROM Historique, EleveHistoG1 WHERE idEleve='$idStudent' AND Historique.idHisto = EleveHistoG1.idHisto) UNION
		           (SELECT Historique.idHisto, idGame2, jour FROM Historique, ClasseHistoG2 WHERE ClasseHistoG2.idHisto = Historique.idHisto AND mailProf = '$idTeacher') ORDER BY jour ASC;";
		$resultScript = pg_query($this->_conn, $script);
		while($row = pg_fetch_row($resultScript))
			array_push($result, new Historic($row[0], $row[1], trim($row[2])));

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
	
	public function existEleve($pseudeleve)
	{
		$script       = "SELECT CASE WHEN EXISTS (SELECT id FROM Eleve WHERE pseudo = '$pseudeleve') THEN CAST(1 AS BIT) ELSE CAST(0 AS BIT) END;";
		$resultScript = pg_query($this->_conn, $script);
		$row=pg_fetch_row($resultScript);
		
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
		return password_verify($passTest, $row[0]);
	}
	
	public function cmpPassHashMail($passTest, $mail)
	{
		$script       = "SELECT password FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return password_verify($passTest, $row[0]);
	}
	
	public function cmpPassHashEleve($passTest, $eleve)
	{
		$script       = "SELECT password FROM Eleve WHERE pseudo = '$eleve';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return password_verify($passTest, $row[0]);
		
	}
	
	public function getMailProfFromEleve($eleve)
	{
		$script       = "SELECT mailClasse FROM EleveClasse, Eleve WHERE id = '$eleve' AND EleveClasse.idEleve = Eleve.id;";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return $row[0];
	}
	
	public function getPassHash($mail)
	{
		$script       = "SELECT password FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		
		$row = pg_fetch_row($resultScript);
		return $row[0];
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

	public function getListPackGame1($mail)
	{
		$script = "SELECT id, nom FROM PackPaires WHERE mailClasse = '$mail';";
		$resultScript = pg_query($this->_conn, $script);
		if($resultScript)
		{
			$arrayPack = array();
			while($row = pg_fetch_row($resultScript))
			{
				array_push($arrayPack, new PackG1($row[0], trim($row[1])));
			}
			return $arrayPack;
		}
		return null;
	}

	public function getClassID($mail)
	{
		$script = "SELECT id FROM Classe WHERE mail = '$mail';";
		$resultScript = pg_query($this->_conn, $script);

		$row = pg_fetch_row($resultScript);
		if($row)
			return $row[0];
		return null;
	}

	public function addStudent($mail, $nameStudent, $surnameStudent, $password)
	{
		$idClass = $this->getClassID($mail);
		if($idClass == null)
			return -1;

		$passHash = password_hash($password, PASSWORD_BCRYPT);
		$pseudo = dechex($idClass).substr($nameStudent, 0, 4).substr($surnameStudent, 0, 4);
		if($this->existEleve($pseudo))
			return 0;

		$script = "INSERT INTO Eleve(id, pseudo, nom, prenom, password, nbGame1, nbGame2) VALUES (DEFAULT, '$pseudo', '$nameStudent', '$surnameStudent', '$passHash', '0', '0') RETURNING Eleve.id;";
		$resultScript = pg_query($this->_conn, $script);

		$idEleve = pg_fetch_row($resultScript)[0];

		$script = "INSERT INTO EleveClasse(idEleve, mailClasse) VALUES ('$idEleve', '$mail');";
		$resultScript = pg_query($this->_conn, $script);

		return 1;
	}
}
?>
