<?php

class Student
{
	public $id;
	public $nbGame1;
	public $nbGame2;

	public function __construct($id_, $nbGame1_, $nbGame2_)
	{
		$this->id = $id_;
		$this->nbGame1 = $nbGame1_;
		$this->nbGame2 = $nbGame2_;
	}
}

?>
