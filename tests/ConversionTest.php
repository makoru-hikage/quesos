<?php

use PHPUnit\Framework\TestCase;
use Quesos\QueryStringConverter;

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

	public function testAllComparativeOperators() {

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

	public function testAllBetweenOperators() {

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


}