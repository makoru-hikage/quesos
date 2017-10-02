<?php

namespace DeltaX\Quesos;

use DeltaX\Quesos\SearchFilterItem;

class SearchFilter {


	/**
	 * An associative array
	 * of SearchFilterItems
	 * where the keys are
	 * the column names
	 * to avoid duplication.
	 * 
	 * @var array
	 */

	protected $filterItems;

	/**
	 * This object must not be
	 * devoid of items.
	 * 
	 * @var array
	 */
	public function __construct($filterItems){
		$this->addFilters($filterItems);
	}

	/**
	 * The expected value's format is:
	 * an array of arrays that are valid 
	 * for SearchFilterItem, or an array
	 * of SearchFilterItems
	 *
	 * @param array $filterItems
	 * @return self
	 */
	public function addFilters(array $filterItems){

		foreach ($filterItems as $filterItem) {
			$this->addFilter($filterItem);
		}

		return $this;
	}

	/**
	 * Should the name already exist in the filter,
	 * it shall be overwritten. So, if there'll
	 * be duplicate(s) in a list, the operator 
	 * and the value of the latest one shall be 
	 * accepted.
	 * 
	 * @param \DeltaX\Crud\MenuService\SearchFilterItem|array $filterItem
	 * @return self
	 */
	public function addFilter($filterItem) {

		$itemIsObject = $filterItem instanceof SearchFilterItem;

		//By this way, if the var is neither a SearchFilterItem
		//or an array, error shall be thrown
		$filterName = $itemIsObject ?
			$filterItem->getName() :
			$filterItem[0];

		$this->filterItems[$filterName] = $itemIsObject ? 
			$filterItem : 
			new SearchFilterItem($filterItem);

		return $this;
	}

	/**
	 * Gets the filterItems
	 *
	 * @param string $name
	 * @return array|null
	 * @throws \DeltaX\Exceptions\NullItemException
	 * 
	 */
	public function getFilter($name = null) {

		//Should an argument be supplied but the
		//item does not exist.
		if( $name && ! isset($this->filterItems[$name]) ){
			return null;
		}

		return $name ? $this->filterItems[$name] : $this->filterItems;
	}

	/**
	 * Edits an item in the array. 
	 * Add the fourth argument if 
	 * it is necessary to rename
	 * the column.
	 *
	 * @param string $name
	 * @param string $operator
	 * @param mixed $value
	 * @param string $newColumnName
	 * @return self
	 * @throws \DeltaX\Exceptions\NullItemException
	 */
	public function editFilter($name, $operator, $value, $newColumnName = null){

		$filterItem = $this->filterItems[$name];

		if ($newColumnName) {
			$filterItem = $filterItem->renameColumn($newColumnName);
		}
		
		$filterItem->setValueAndOperator($value, $operator);
		$this->filterItems[$name] = $filterItem;

		return $this;
	}

	/**
	 * Renames the column property of an item
	 * 
	 * @param  string $name          
	 * @param  string $newColumnName 
	 * @return self
	 * @throws \DeltaX\Exceptions\NullItemException
	 */
	public function renameColumn(string $name, string $newColumnName){

		if(! isset($this->filterItems[$name])){
			throw new Exception ("The field $name does not exist");
		}

		$filterItem = $this->filterItems[$name];

		$filterItem->renameColumn($newColumnName);
		$this->filterItems[$name] = $filterItem;

		return $this;
	}

	/**
	 * Removes a filter item by name
	 * 
	 * @param string $name
	 * @return self
	 * @throws \DeltaX\Exceptions\NullItemException
	 */
	public function removeFilter(string $name) {

		if(! $this->filterItems[$name]){
			throw new Exception ("The field $name does not exist");
		}

		unset($this->filterItems[$name]);
		return $this;
	}

	/**
	 * Get all the items with comparison operators
	 * 
	 * @return self 
	 */
	public function getItemsWithComparisonOperators() {

		$filterItems = [];
		$comparisonOperators = ['=', '<', '>', '<=', '>=', '!=', 'like', 'not like'];

		foreach ($this->filterItems as $key => $item) {
			if (in_array($item->getOperator(), $comparisonOperators)) {
				$filterItems[$key] = $item;
			}
		}

		return new self($filterItems);

	}


	/**
	 * Remove items with names not listed in the array
	 * 
	 * @param  array  $itemNames 
	 * @return self            
	 */
	public function only(array $itemNames){

		$filtrate = function ($filterItem) use ($itemNames){
			return in_array($filterItem, $itemNames);
		};

		$result = array_filter( $this->filterItems, $filtrate, ARRAY_FILTER_USE_KEY );

		return new self($result);
	}

	/**
	 * Returns an array of arrays of 3 elements:
	 * ['column', 'operator', 'value']
	 * Should $keyValuePairOnly be true:
	 * ['column => value']
	 *
	 * @param bool $keyValuePairOnly
	 * @return array
	 */
	public function toArray($keyValuePairOnly = false) {
		$filterItems = [];

		foreach ($this->filterItems as $key => $item) {
			
			if ($keyValuePairOnly) {
				$filterItems[$key] = $item->getValue();
				continue;
			}

			$filterItems[] = $item->toArray();
		}

		return $filterItems;
	}

	/**
	 * See json_encode in PHP manual
	 *
	 * @param int $options
	 * @return string
	 */
	public function toJson(int $options) {
		return json_encode($this->filterItems, $options);
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