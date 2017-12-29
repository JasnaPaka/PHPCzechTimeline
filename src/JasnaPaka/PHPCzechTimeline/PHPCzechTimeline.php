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

		$item = new TimelineItem($id, $value, $this->getRating($value));
		$this->items[$id] = $item;
	}

	public function isValidValue($value) {
		return $this->normalizeValue($value) == null ? false: true;
	}

	public function getTimeline() {
		$items = $this->items;
		usort($items, function ($a, $b) {
			$aValue = $this->normalizeValue($a->getValue());
			$bValue = $this->normalizeValue($b->getValue());

			if ($aValue == $bValue) {
				if ($a->getRate() == $b->getRate()) {
					return 0;
				}

				return ($a->getRate() > $b->getRate()) ? 1 : -1;
			}

			return ($aValue > $bValue) ? 1 : -1;
		});

		return array_values($items);
	}

	private function normalizeValue($value) {

		if (strpos($value, "-")) {
			$value1 = (int) explode("-", $value)[0];
			$value2 = (int) explode("-", $value)[1];

			if ($value2 >= 100) {
				return $value2;
			}

			// rok je pouze dvoupísmený, přidáme první dva
			$str = substr(explode("-", $value)[0], 0, 2);

			return $str.$value2;
		}

		if (strpos($value, ",")) {
			$parts = explode(",", $value);
			$size = sizeof($parts);

			$lastPart = $parts[$size-1];

			// případné poznámky navíc eliminujeme
			preg_match_all('!\d+!', $lastPart, $matches);
			if (sizeof ($matches) == 1) {
				$lastPart = $matches[0][0];
			}

			if ($this->getIsNumber($lastPart)) {
				return (int) $lastPart;
			}

			return (int) $parts[$size - 1];
		}

		if ($this->getIsNumber($value)) {
			return (int) $value;
		}

		// případné poznámky navíc eliminujeme
		preg_match_all('!\d+!', $value, $matches);
		if (sizeof ($matches) == 1) {
			if ($this->getIsNumber($matches[0][0])) {
				return (int) $matches[0][0];
			}
		}

		return null;
	}

	/*
	 *
	 * 100 - rok
	 *
	 */
	protected function getRating($value) {


		return 100;
	}

	private function getIsNumber($value) {
		$value = trim($value);
		return ((string)(int)$value) == $value;
	}
}