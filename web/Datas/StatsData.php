<?php

class Student
{
	public $id;
	public $lastName;
	public $firstName;
	public $nbGame1;
	public $nbGame2;

	public function __construct($id_, $lastName_, $firstName_, $nbGame1_, $nbGame2_)
	{
		$this->id        = $id_;
		$this->lastName  = $lastName_;
		$this->firstName = $firstName_;
		$this->nbGame1   = $nbGame1_;
		$this->nbGame2   = $nbGame2_;
	}
}

class Historic
{
	public $idStudent;
	public $lastName;
	public $firstName;
	public $idGame;
	public $date;

	public function __construct($idH, $idS, $lastName_, $firstName_, $idG, $d)
	{
		$this->id        = $idH;
		$this->idStudent = $idS;
		$this->lastName  = $lastName_;
		$this->firstName = $firstName_;
		$this->idGame    = $idG;
		$this->date      = $d;
	}
}

?>
