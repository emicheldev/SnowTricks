<?php

namespace App\Tests;

use App\Entity\Figure;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{    
    
    /**
     * testName
     *
     * @return void
     */
    public function testName()
    {
        $figure = new Figure();
        $name = "Test de nom de figure";

        $figure->setName($name);
        $this->assertEquals("Test de nom de figure", $figure->getName());
    }

    public function testDescription()
	{
		$figure = new Figure();
		$description = "Test de description de figure";

		$figure->setDescription($description);
		$this->assertEquals("Test de description de figure", $figure->getDescription());
	}

	public function testCreatedAt()
	{
		$figure = new Figure();
		$date = new \DateTime();
		$createdAt = $date;

		$figure->setCreatedAt($createdAt);
		$this->assertEquals($date, $figure->getCreatedAt());
	}

	public function testUpdatedAt()
	{
		$figure = new Figure();
		$date = new \DateTime();
		$updatedAt = $date;

		$figure->setUpdatedAt($updatedAt);
		$this->assertEquals($date, $figure->getUpdatedAt());
	}
}
