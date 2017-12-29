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

				return ($a->getRate() < $b->getRate()) ? 1 : -1;
			}

			return (((int) $aValue) > ((int) $bValue)) ? 1 : -1;
		});

		return array_values($items);
	}

	private function normalizeValue($value) {

		$nonAsciiValue = $this->getNonAsciiStr($value);

		// Výraz "17. století"
		if ($this->endsWith($nonAsciiValue, ". stoleti")) {
			$str = $nonAsciiValue;
			$str = str_replace(". stoleti", "", $str);
			if ($this->getIsNumber($str)) {
				return (int) $str."00";
			}
		}

		// Výraz "60. léta" (je myšleno 1960 apod.).
		if ($this->endsWith($nonAsciiValue, ". leta")) {
			$str = $nonAsciiValue;
			$str = str_replace(". leta", "", $str);
			if ($this->getIsNumber($str)) {
				return (int) "19".$str;
			}
		}

		// Výraz "Přelom 17. a 18. století"
		if (strpos($nonAsciiValue, "Prelom") === 0) {
			preg_match_all('!\d+!', $value, $matches);
			if (sizeof ($matches) == 1) {
				$val = $matches[0][1];
				if (strlen($val) == 2) {
					return (int)$val . "00";
				}
			}
		}

		// Výraz "Počátek 18. století"
		if ((strpos($nonAsciiValue, "Pocatek") === 0)
			&& ($this->endsWith($nonAsciiValue, ". stoleti"))) {
			preg_match_all('!\d+!', $value, $matches);

			if (sizeof ($matches) == 1) {
				$val = $matches[0][0];
				if (strlen($val) == 2) {
					return (int)$val . "00";
				}
			}
		}

		if (strpos($value, "-")) {
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
	 * 100 - rok (např. 1950)
	 * 150 - přibližně rok (např. "kolem 1950")
	 *
	 */
	protected function getRating($value) {

		$value = $this->getNonAsciiStr($value);

		if (strpos(strtolower($value), ". stoleti") !== false) {
			return 120;
		}

		if (strpos(strtolower($value), ". leta") !== false) {
			return 120;
		}

		if (strpos(strtolower($value), "kolem") !== false) {
			return 150;
		}

		if (strpos(strtolower($value), "prelom") !== false) {
			return 150;
		}

		if (strpos(strtolower($value), "asi") !== false) {
			return 200;
		}

		if (mb_strpos(strtolower($value), "pred", 0) !== false) {
			return 250;
		}

		if (strpos(strtolower($value), "cca.") !== false) {
			return 200;
		}

		if ((strpos(strtolower($value), "pocatek") !== false)
			&& (strpos(strtolower($value), ". stoleti") !== false)) {
			return 200;
		}

		return 100;
	}

	private function getIsNumber($value) {
		$value = trim($value);
		return ((string)(int)$value) == $value;
	}

	private function getNonAsciiStr($str) {

		$str = str_replace("ř", "r", $str);
		$str = str_replace("ť", "t", $str);
		$str = str_replace("í", "i", $str);
		$str = str_replace("é", "e", $str);
		$str = str_replace("č", "c", $str);
		$str = str_replace("á", "a", $str);

		return $str;
	}

	private function endsWith($haystack, $needle)
	{
		$length = strlen($needle);

		return $length === 0 ||
			(substr($haystack, -$length) === $needle);
	}
}