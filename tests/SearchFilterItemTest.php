<?php

use PHPUnit\Framework\TestCase;
use DeltaX\Quesos\SearchFilterItem;

class SearchFilterItemTest extends TestCase {

	public function testArrayInput() {

		$item = new SearchfilterItem(['age', '=', 12]);

		$this->assertEquals($item->getName(), 'age');
		$this->assertEquals($item->getColumn(), 'age');
		$this->assertEquals($item->getOperator(), '=');
		$this->assertEquals($item->getValue(), 12);

	}

	public function testUsualInput() {

		$item = new SearchfilterItem('age', '=', 12);

		$this->assertEquals($item->getName(), 'age');
		$this->assertEquals($item->getColumn(), 'age');
		$this->assertEquals($item->getOperator(), '=');
		$this->assertEquals($item->getValue(), 12);

	}

	public function testInvalidItem(){

		$valid1 = true;
		$valid2 = true;
		$item = null;

		try {
			$item = new SearchfilterItem(['12', '=', 12]);
		} catch (\Exception $e) {
			$valid = false;
		}

		$this->assertFalse($valid);

		try {
			$item = new SearchfilterItem('==', '=', 12);
		} catch (\Exception $e) {
			$valid2 = false;
		}

		$this->assertFalse($valid2);

	}

	public function testColumnRenaming()	{

		//Useful when it is impractical to name a prefixed column
		//in an HTTP request but you need to prefix it later for the
		//sake of disambiguating a column name in an SQL query
		$item = new SearchfilterItem('age', '=', 12);

		$item = $item->renameColumn('users.age');

		$this->assertEquals($item->getColumn(), 'users.age');

	}

	public function testTurnToArray()	{
		
		$item = new SearchfilterItem('age', '=', 12);

		$arr = $item->toArray();

		$this->assertEquals($arr, ['age', '=', 12]);

	}

	


}