<?php

use PHPUnit\Framework\TestCase;
use DeltaX\Quesos\QueryStringConverter;

class ConversionTest extends TestCase {

	public function testSimpleSearch() {

		$convertedValue = QueryStringConverter::convert([
			"first_name" => "Christina", 
			"last_name" => "Moran", 
		]);

		$expectedValue = [
			"first_name" => ["first_name", "=", "Christina"],
			"last_name" => ["last_name", "=", "Moran"]
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}

	public function testComparativeOperators() {

		$convertedValue = QueryStringConverter::convert([
			"first_name" => "Christina", 
			"age" => "gte,18",
			"province" => "in,Carmona,Cavite,Indang",

		]);

		$expectedValue = [
			"first_name" => ["first_name", "=", "Christina"],
			"age" => ["age", ">=", 18],
			"province" => ["province", "in", ["Carmona","Cavite","Indang"]],
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}

	public function testBetweenOperators() {

		$convertedValue = QueryStringConverter::convert([ 
			"age" => "bwn,18,25",
			"year_level" => "nbwn,3,4",

		]);

		$expectedValue = [
			"age" => ["age", "between", [18,25]],
			"year_level" => ["year_level", "not between", [3,4]],
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}

	public function testInOperators() {

		$convertedValue = QueryStringConverter::convert([ 
			"first_name" => "Kanor,Hayden,Chito,Wally,Paolo",
			"writer_surname" => "Poe,King,Inah,Moe"

		]);

		$expectedValue = [
			"first_name" => [ "first_name", "in", [ "Kanor", "Hayden", "Chito", "Wally", "Paolo"] ],
			"writer_surname" => [ "writer_surname", "in", [ "Poe", "King", "Inah", "Moe" ] ],
		];

		$this->assertEquals($convertedValue, $expectedValue);

	}


}