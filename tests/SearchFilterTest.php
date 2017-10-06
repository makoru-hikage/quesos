<?php

use PHPUnit\Framework\TestCase;
use DeltaX\Quesos\SearchFilter;
use DeltaX\Quesos\SearchFilterItem;

class SearchFilterTest extends TestCase {

	protected $exampleArray = [
		"sex" =>  ["sex", "=", "male"],
		"points" => ["points", ">", "9000"],
		"first_name" => [ "first_name", "in", [ "Kanor", "Hayden", "Chito", "Wally", "Paolo"] ]
	];

	public function testArrayOfArraysInput() {

		$input = $this->exampleArray;

    	$filter = new SearchFilter($input);

        $this->assertEquals($filter->toArray(), array_values($input));
       
	}

	public function testArrayOfItemsInput() {

		$input = [
			new SearchFilterItem("sex", "=", "male"),
    		new SearchFilterItem("points", ">", "9000"),
    		new SearchFilterItem( "first_name", "in", [ "Kanor", "Hayden", "Chito", "Wally", "Paolo"] )
    	];

    	$expectedArray = $this->exampleArray;

    	$filter = new SearchFilter($input);
    	
        $this->assertEquals($filter->toArray(), array_values($expectedArray));
       
	}

	public function testAddItem(){
		
		$filter = new SearchFilter($this->exampleArray);
		$oldCount = count($filter->getFilter());
		$filter = $filter->addFilter(['video_count', '>=', 3]);
		$newCount = count($filter->getFilter());

		$newItem = $filter->getFilter('video_count');

		$this->assertGreaterThan( $oldCount, $newCount );
		$this->assertEquals($newItem->toArray(), array_values(['video_count', '>=', 3]));

	}

	public function testRemoveItem(){
		
		$filter = new SearchFilter($this->exampleArray);
		$oldCount = count($filter->getFilter());
		$filter = $filter->removeFilter('points');
		$newCount = count($filter->getFilter());

		$nonexistentItem = null;

		try {
			$nonexistentItem = $filter->getFilter('points');
		} catch (\Exception $e) {
			$nonexistentItem = null;
		}

		$this->assertLessThan( $oldCount, $newCount );
		$this->assertNull($nonexistentItem);

	}

	public function testEditItemAndRenameColumn(){
		
		$filter = new SearchFilter($this->exampleArray);

		$oldItem = $filter->getFilter('first_name');

		$filter = $filter->editFilter(
			'first_name', 
			'in',  
			[ "Kanor", "Hayden", "Chito", "Wally", "Paolo", "Edgardo" ],
			'idols.first_name'
		);

		$newItem = $filter->getFilter('first_name');

		$this->assertEquals($newItem->getValue(), [ "Kanor", "Hayden", "Chito", "Wally", "Paolo", "Edgardo" ]);
		$this->assertEquals($newItem->getOperator(), 'in');
		$this->assertEquals($newItem->getColumn(), 'idols.first_name');
		
	}

	public function testGetOnlyWithComparisonOptrs(){
		$filter = new SearchFilter($this->exampleArray);

		$filter = $filter->getItemsWithComparisonOperators();

		$expected = [
			["sex", "=", "male"],
			["points", ">", "9000"]
		];

		$this->assertEquals($filter->toArray(), $expected);
	}

	public function testOnlyOnesIncluded(){

		$filter = new SearchFilter($this->exampleArray);
		$filter = $filter->only(['first_name']);

		$expected = [
			[
				"first_name", 
				"in", 
				[ "Kanor", "Hayden", "Chito", "Wally", "Paolo"] 
			]
		];

		$this->assertEquals($filter->toArray(), $expected);

	}



	


}