<?php

namespace DeltaX\Quesos;

use DeltaX\Quesos\SearchFilter;

class QueryStringConverter {

	/**
	 * VALID_OPERATORS
	 * @var array
	 */
	const VALID_OPERATORS = [
        'eq' => '=', 
        'gt' => '>', 
        'lt' => '<', 
        'gte' => '>=', 
        'lte' => '<=', 
        'ne' => '!=',
        'like' => 'like', 
        'nlike' => 'not like', 
        'bwn' => 'between',
        'nbwn' => 'not between',
        'in' => 'in',
        'nin' => 'not in'
	];

	/**
	 * @var array $parsedQueryString
	 * 
	 * The query string in array form
	 */
	protected $parsedQueryString;
	
	/**
	 * The constructor, needs a parsed query string to be processed later.
	 * 
	 * @param array $parsedQueryString
	 */
	public function __construct(array $parsedQueryString){
		$this->parsedQueryString = $parsedQueryString;
	}

	/**
	 * Convert a string with comma into an array.
	 * If the input is "1,2", the output is [1, 2]
	 * Should there be no comma. Nothing happens
	 *
	 * @param  string $value 
	 * @return array|mixed
	 */
	protected static function convertToArrayValue($value){
		
		//Should a comma be found amidst the said string.
		if (strpos($value, ',') !== false) {

			//WARNING: Should there be a space after a certain comma
			//There'll be a space before the subsequent value
			//such as "v1, v2" will become ["v1", " v2"]
			return explode(',', $value);
		}

		return $value;

	}

	/**
	 * Extracts the first operator in a refined Query Value
	 *
	 * @param  array  $value 
	 * @return string
	 */
	protected static function getOperatorInQueryValue(array $value){

		//Should the first element of an item belong to
		//VALID_OPERATORS... 
		if (array_key_exists($value[0], static::VALID_OPERATORS)) {
			return static::VALID_OPERATORS[$value[0]];
		}

		//Were you looking for an array with the same content?
		return '=';
	}

	/**
	 * Extracts the attribute value in a refined Query Value
	 * 
	 * @param  array $value
	 * @return mixed
	 */
	protected static function getValueInQueryValue(array $value){

		//Should the first element of an item belong to
		//VALID_OPERATORS...
		if (array_key_exists($value[0], static::VALID_OPERATORS)) {

			$value = array_slice($value, 1);

			//Items with one-element arrays are turned into their values
			//Such as that col1 => [1] turns to col1 => 1
			$value = count($value) > 1 ? $value : $value[0];

		}

		return $value;
	}



	/**
	 * Convert a whole parsed query string to an array suitable for
	 * a customized search
	 *
	 * A parsed query string is an array derived from a raw query string
	 * 
	 * @return array
	 */
	public static function convert(){

		$parsedQueryString = $this->parsedQueryString;
		$searchFilterItems = [];

		foreach ($parsedQueryString as $key => $queryValue) {
		
			//A query string item with a value that contains a comma (,)
			//will be converted to array. Otherwise, leave it as is.
			$value = self::convertToArrayValue($queryValue);

			//The default operator: it shall not be changed unless
			//the query value turns out to be an array whose first element
			//corresponds to a key in VALID_OPERATORS
			$operator = '=';

			if ( is_array($value) ) {
				$operator = self::getOperatorInQueryValue($value);
				$value = self::getValueInQueryValue($value);
			}
			
			// Should the value be a string that can be a number, convert it.
			$value = is_numeric($value) ? $value + 0 : $value;
			
			//If the operator is still default
			//and the value turns out to be an array
			$operator = is_array($value) && $operator === '=' ? 'in' : $operator;

			//"sort" is a special item in a search filter and it is always
			//an array. If your need says otherwise, it's up to you.
			$value = $key === 'sort' && ! is_array($value) ? [ $value ] : $value;

			// "age=gte,18" becomes "age" => [ "age", ">=", 18 ]
			$searchFilterItems[$key] = [$key, $operator, $value];
		}

		return $searchFilterItems;
	}

}
