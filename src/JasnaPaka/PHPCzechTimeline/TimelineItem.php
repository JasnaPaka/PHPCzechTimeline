<?php

namespace JasnaPaka\PHPCzechTimeline;

class TimelineItem
{
	private $id;
	private $value;
	private $rate;

	public function __construct($id, $value, $rate = 100)
	{
		$this->id = $id;
		$this->value = $value;
		$this->rate = $rate;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getRate()
	{
		return $this->rate;
	}
}