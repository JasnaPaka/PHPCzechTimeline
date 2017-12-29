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
		$this->assertTrue($timeline->isValidValue("1856-57"));
		$this->assertTrue($timeline->isValidValue("kolem 1950"));
		$this->assertTrue($timeline->isValidValue("kolem roku 1970"));
		$this->assertTrue($timeline->isValidValue("asi 1902"));
		$this->assertTrue($timeline->isValidValue("Před 1863"));
		$this->assertTrue($timeline->isValidValue("před 1863"));
		$this->assertTrue($timeline->isValidValue("cca. 1935"));
		$this->assertTrue($timeline->isValidValue("17. století"));
		$this->assertTrue($timeline->isValidValue("60. léta"));
		$this->assertTrue($timeline->isValidValue("Přelom 17. a 18. století"));
		$this->assertTrue($timeline->isValidValue("Počátek 17. století"));
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

	public function testGetTimelineSimple5()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1856-57");
		$timeline->addItem("2", "1856");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1856][1:1856-57]", $outputStr);
	}

	public function testGetTimeline1()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1950");
		$timeline->addItem("2", "kolem 1950");
		$timeline->addItem("3", "kolem roku 1960");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:kolem 1950][1:1950][3:kolem roku 1960]", $outputStr);
	}

	public function testGetTimeline2()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1950");
		$timeline->addItem("2", "asi 1950");
		$timeline->addItem("3", "před 1950");
		$timeline->addItem("4", "Před 1950");
		$timeline->addItem("5", "cca. 1950");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[3:před 1950][4:Před 1950][2:asi 1950][5:cca. 1950][1:1950]", $outputStr);
	}

	public function testGetTimeline3()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "1734");
		$timeline->addItem("2", "1700");
		$timeline->addItem("3", "1675");
		$timeline->addItem("4", "17. století");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[3:1675][4:17. století][2:1700][1:1734]", $outputStr);
	}


	public function testGetTimeline4()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "60. léta");
		$timeline->addItem("2", "1959");
		$timeline->addItem("3", "1960");
		$timeline->addItem("4", "1961");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1959][1:60. léta][3:1960][4:1961]", $outputStr);
	}

	public function testGetTimeline5()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "Přelom 17. a 18. století");
		$timeline->addItem("2", "1799");
		$timeline->addItem("3", "1800");
		$timeline->addItem("4", "1801");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1799][1:Přelom 17. a 18. století][3:1800][4:1801]", $outputStr);
	}

	public function testGetTimeline6()
	{
		$timeline = new PHPCzechTimeline();
		$timeline->addItem("1", "Počátek 18. století");
		$timeline->addItem("2", "1799");
		$timeline->addItem("3", "1800");
		$timeline->addItem("4", "1801");

		$output = $timeline->getTimeline();
		$outputStr = $this->getOutputStr($output);

		$this->assertEquals("[2:1799][1:Počátek 18. století][3:1800][4:1801]", $outputStr);
	}
}