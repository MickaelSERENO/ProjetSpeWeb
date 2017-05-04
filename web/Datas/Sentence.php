<?php

class WordGroup
{
	private $_id;
	private $_text;

	public function __construct($id, $text)
	{
		$this->_id = $id;
		$this->_text = $text;
	}

	public function getID()
	{
		return $this->_id;
	}

	public function getGroupText()
	{
		return $this->_text;
	}
}

class Sentence
{
	private $_wordArray;
	private $_id;
	public function __construct($id, $wordArray)
	{
		$this->_wordArray = $wordArray;
		$this->_id = $id;
	}

	public function getWordArray()
	{
		return $this->_wordArray;
	}

	public function getId()
	{
		return $this->_id;
		
	}
}

class Mapping
{
	private $_wordGroup1;
	private $_wordGroup2;
	private $_relation;

	public function __construct($id1, $id2, $relation)
	{
		$this->_wordGroup1 = $id1;
		$this->_wordGroup2 = $id2;
		$this->_relation   = $relation;
	}

	public function getWordGroup1()
	{
		return $this->_wordGroup1;
	}

	public function getWordGroup2()
	{
		return $this->_wordGroup2;
	}

	public function getRelation()
	{
		return $this->_relation;
	}
}

class WordGroupMapping
{
	private $_mappingArray;

	public function __construct($array)
	{
		$this->_mappingArray = $array;
	}

	public function getMappingArray()
	{
		return $this->_mappingArray;
	}
}

class PairSentences
{
    private $_sent1;
    private $_sent2;

    public function __construct($sent1, $sent2)
    {
        $this->_sent1   = $sent1;
        $this->_sent2   = $sent2;
    }

	public function getSent1()
	{
		return $this->_sent1;
	}

	public function getSent2()
	{
		return $this->_sent2;
	}
}

class ResultSentences
{
	public $sent1;
	public $sent2;
	public $mapping;

	public function __construct($s1, $s2, $m)
	{
		$this->sent1=$s1;
		$this->sent2=$s2;
		$this->mapping=$m;
	}
}

class Game1Result
{
	public $idPack;
	public $idSents;
	public $results;

	public function __construct($idPack_, $idSents_, $results_)
	{
		$this->idPack = $idPack_;
		$this->idSents = $idSents_;
		$this->results = $results_;
	}
}

class ReturnNextSent
{
	public $nextSent;
	public $resultOldSent;

	public function __construct($ns, $r)
	{
		$this->nextSent = $ns;
		$this->resultOldSent = $r;
	}
}

?>
