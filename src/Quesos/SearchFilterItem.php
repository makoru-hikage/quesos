<?php

namespace DeltaX\Quesos;


class SearchFilterItem {

	/**
	 * VALID_OPERATORS
	 * @var array
	 */

	const VALID_OPERATORS = [
        'eq' => '=', 
        'gt' => '<', 
        'lt' => '>', 
        'gte' => '<=', 
        'lte' => '>=', 
        'ne' => '!=',
        'like' => 'like', 
        'nlike' => 'not like', 
        'bwn' => 'between',
        'nbwn' => 'nbetween',
        'in' => 'in',
        'nin' => 'nin'
    ];


	/**
	 * Made for avoiding duplication
	 * of columns in a set called
	 * SearchFilter
	 * @var string
	 */

	protected $name;

	/**
	 * The column
	 * @var string
	 */

	protected $column;


	/**
	 * operator
	 * @var string
	 */

	protected $operator;


	/**
	 * value
	 * @var mixed
	 */

	protected $value;

	/**
	 * __construct
	 *
	 * @param string|array $column
	 * @param string $operator
	 * @param mixed $value
	 * @return void
	 */
	public function __construct($column, string $operator = null, $value = null) {

		if ( ! $this->isValidColumn($column) || ! $this->isValidColumn($column[0]) ) {
			throw new \Exception("Column name is not valid", 1);	
		}

		if ( is_array($column) ){

			$this->column = $column[0];
			$this->name = $this->column;
			$this->operator = $column[1];
			$this->value = $column[2];

			if ( ! in_array($column[1], static::VALID_OPERATORS) ){
				$this->operator = '=';
				$this->value = $column[1];
			}

			return;
		} 

		$this->column = $column;
		$this->operator = $operator;
		$this->value = $value;
		
		$this->name = $this->column;

	}

	/**
	 * Check if it is valid as an SQL column name
	 * 
	 * @param  array   $input 
	 * @return boolean
	 */
	public function isValidColumn ($input){
		return (bool) preg_match('/[a-zA-Z_][a-zA-Z0-9_]*/', $input[0]);
	}

	/**
	 * Returns the name property
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the column property
	 * 
	 * @return string
	 */
	public function getColumn() {
		return $this->column;
	}

	/**
	 * Returns the operator property
	 * 
	 * @return string
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * Returns the value property
	 * 
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Sets the value and operator
	 * 
	 * @param mixed $value
	 * @param string $operator
	 * @return mixed
	 */
	public function setValueAndOperator($value, $operator = '=') {
		$this->value = $value;
		$this->operator = $operator;

		return $this;
	}

	/**
	 * Is the operator vaild?
	 * 
	 * @return bool
	 */
	public function operatorIsValid() {
		return in_array($this->operator, static::VALID_OPERATORS);
	}

	/**
	 * Edit a column
	 * 
	 * @param string $tableName 
	 * @return  self
	 */
	public function renameColumn(string $columnName){

		$this->column = $columnName;

		return $this;
	}

	/**
	 * Returns an indexed array representation
	 * 
	 * @return array
	 */
	public function toArray() {
		return [
			$this->column,
			$this->operator,
			$this->value
		];
	}

	/**
	 * See json_encode in PHP manual
	 * 
	 * @param int $options
	 * @return array
	 */
	public function toJson(int $options = null) {
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Returns a JSON of the object
	 * For further options, call toJson
	 * with arguments instead
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->toJson();
	}

}
