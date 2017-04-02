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
	public $idGame;
	public $date;

	public function __construct($idS, $idH, $idG, $d)
	{
		$this->idStudent = $idS;
		$this->id        = $idH;
		$this->idGame    = $idG;
		$this->date      = $d;
	}
}

?>
