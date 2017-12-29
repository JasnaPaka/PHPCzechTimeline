<?php

namespace JasnaPaka\PHPCzechTimeline;

include "src/JasnaPaka/PHPCzechTimeline/PHPCzechTimeline.php";
include "src/JasnaPaka/PHPCzechTimeline/Timelineitem.php";

use PHPUnit\Framework\TestCase;
use JasnaPaka\PHPCzechTimeline\PHPCzechTimeline;

class PHPCzechTimelineTest extends TestCase
{

	private function getOutputStr($output) {
		$str = "";

		foreach($output as $value) {
			$str = $str.sprintf ("[%d:%s]", $value->getId(), $value->getValue());
		}

		return $str;
	}

	/**
	 * @expectedException Exception
	 */
	public function testAddItemDuplicateError()
	{
		$this->expectException(\Exception::class);

		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1990");
		$timeline->addItem("1", "1990");
	}

	public function testAddItemEmptyError()
	{
		$this->expectException(\Exception::class);
		$timeline = new PHPCzechTimeline();
		$timeline->addItem(null, "1990");
	}

	public function testAddItemEmptyError2()
	{
		$this->expectException(\Exception::class);
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("   ", "1990");
	}

	public function testAddItemIsValidValue()
	{
		$timeline = new PHPCzechTimeline();
		$this->assertFalse($timeline->isValidValue(null));
		$this->assertFalse($timeline->isValidValue(""));
		$this->assertTrue($timeline->isValidValue("1990"));
		$this->assertFalse($timeline->isValidValue("abc"));
		$this->assertTrue($timeline->isValidValue("1947, 1995"));
		$this->assertTrue($timeline->isValidValue("1978, 2014 (nová)"));
		$this->assertTrue($timeline->isValidValue("2011 (obnovení)"));
	}

	public function testGetTimelineSimple()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1980");
		$timeline->addItem("2", "1976");
		$timeline->addItem("3", "1990");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1976][1:1980][3:1990]", $outputStr);
	}

	public function testGetTimelineSimple2()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "2001 - 2002");
		$timeline->addItem("2", "1976");
		$timeline->addItem("3", "1990");
		$timeline->addItem("4", "2005   -   2007");
		$timeline->addItem("5", "2011, 2017");


		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1976][3:1990][1:2001 - 2002][4:2005   -   2007][5:2011, 2017]", $outputStr);
	}

	public function testGetTimelineSimple3()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "2005");
		$timeline->addItem("2", "1970, 1990 (nový)");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1970, 1990 (nový)][1:2005]", $outputStr);
	}

	public function testGetTimelineSimple4()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "2011");
		$timeline->addItem("2", "2010 (obnovení)");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:2010 (obnovení)][1:2011]", $outputStr);
	}

	public function testGetTimelineSimple5() {
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1856-57");
		$timeline->addItem("2", "1856");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1856][1:1856-57]", $outputStr);
	}

}