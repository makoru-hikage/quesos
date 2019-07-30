<?php

use PHPUnit\Framework\TestCase;
use DeltaX\Quesos\QueryStringConverter;

class ConversionTest extends TestCase {

	public function testSimpleSearch() {
		$q = new QueryStringConverter([
			"first_name" => "Christina", 
			"last_name" => "Moran", 
		]);

		$convertedValue = $q->convert();

		$expectedValue = [
			"first_name" => ["first_name", "=", "Christina"],
			"last_name" => ["last_name", "=", "Moran"]
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}

	public function testComparativeOperators() {

		$q = new QueryStringConverter([
			"first_name" => "Christina", 
			"age" => "gte,18",
			"province" => "in,Carmona,Cavite,Indang"
		]);

		$convertedValue = $q->convert();

		$expectedValue = [
			"first_name" => ["first_name", "=", "Christina"],
			"age" => ["age", ">=", 18],
			"province" => ["province", "in", ["Carmona","Cavite","Indang"]],
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}

	public function testBetweenOperators() {

		$q = new QueryStringConverter([ 
			"age" => "bwn,18,25",
			"year_level" => "nbwn,3,4",
		]);

		$convertedValue = $q->convert();

		$expectedValue = [
			"age" => ["age", "between", [18,25]],
			"year_level" => ["year_level", "not between", [3,4]],
		];

		$this->assertEquals($convertedValue, $expectedValue);
	}

	public function testInOperators() {

		$q = new QueryStringConverter([ 
			"first_name" => "Kanor,Hayden,Chito,Wally,Paolo",
			"writer_surname" => "in,Poe,King,Inah,Mo"
		]);
		$convertedValue = $q->convert();

		$expectedValue = [
			"first_name" => [ "first_name", "in", [ "Kanor", "Hayden", "Chito", "Wally", "Paolo"] ],
			"writer_surname" => [ "writer_surname", "in", [ "Poe", "King", "Inah", "Mo" ] ],
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}


}