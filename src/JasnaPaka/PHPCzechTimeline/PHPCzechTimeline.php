<?php

namespace JasnaPaka\PHPCzechTimeline;

class PHPCzechTimeline
{
	// id - value
	private $items;

	public function __construct()
	{
		$this->items = array();
	}

	public function addItem($id, $value) {

		if ($id == null) {
			throw new \Exception("Zadane 'id' je prazdne.");
		}

		$id = trim($id);
		if (strlen($id) == 0) {
			throw new \Exception("Zadane 'id' je prazdne.");
		}

		if (array_key_exists($id, $this->items)) {
			throw new \Exception("Pokus o pridani duplicitniho prvku pole. Id: ".$id);
		}

		if ($value == null || strlen(trim($value)) == 0) {
			throw new \Exception("Hodnota je prazdna.");
		}

		// Test, zda hodnotu umime zpracovat.
		if (!$this->isValidValue($value)) {
			throw new \Exception(("Hodnota je neplatna."));
		}

		$item = new TimelineItem($id, $value);
		$this->items[$id] = $item;
	}

	public function isValidValue($value) {
		// uveden pouze rok
		if($this->getIsNumber($value)) {
			return true;
		}

		// uveden rok od/do - oddělovač pomlčka
		if (substr_count($value, "-") == 1) {
			$parts = explode("-", $value);
			if ($this->getIsNumber($parts[0]) && $this->getIsNumber($parts[1])) {
				return true;
			}
		}

		// uvedeno více dat oddělených čárkou
		if (substr_count($value, ",") >= 1) {
			$parts = explode(",", $value);
			$size = sizeof($parts);

			if ($this->getIsNumber($parts[$size-1])) {
				return true;
			}
		}

		return false;
	}

	public function getTimeline() {
		$items = $this->items;
		usort($items, function ($a, $b) {
			$aValue = $this->normalizeValue($a->getValue());
			$bValue = $this->normalizeValue($b->getValue());

			if ($aValue == $bValue) {
				return 0;
			}

			return ($aValue > $bValue) ? 1 : -1;
		});

		return array_values($items);
	}

	private function normalizeValue($value) {

		if (strpos($value, "-")) {
			return (int) explode("-", $value)[0];
		}

		if (strpos($value, ",")) {
			$parts = explode(",", $value);
			$size = sizeof($parts);

			return (int) $parts[$size - 1];
		}

		return (int) $value;
	}

	private function getIsNumber($value) {
		$value = trim($value);
		return ((string)(int)$value) == $value;
	}
}