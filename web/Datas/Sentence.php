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
	public function __construct($wordArray)
	{
		$this->_wordArray = $wordArray;
	}

	public function getWordArray()
	{
		return $this->_wordArray;
	}
}

class Mapping
{
	private $_wordGroup1;
	private $_wordGroup2;

	public function __construct($id1, $id2)
	{
		$this->_wordGroup1 = $id1;
		$this->_wordGroup2 = $id2;
	}

	public function getWordGroup1()
	{
		return $this->_wordGroup1;
	}

	public function getWordGroup2()
	{
		return $this->_wordGroup2;
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
	private $_mapping;

    public function __construct($sent1, $sent2, $mapping)
    {
        $this->_sent1   = $sent1;
        $this->_sent2   = $sent2;
		$this->_mapping = $mapping;
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

?>
