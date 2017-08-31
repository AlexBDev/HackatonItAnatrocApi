<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 30/08/17
 * Time: 15:59
 */

namespace Tests\AppBundle\Model;

use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Model\Localisation;

class LocalisationTest extends WebTestCase
{
    private $_localisation;

    protected function setUp()
    {
        $this->_localisation = new Localisation(4.5,4.877);
    }

    public function testGetLng()
    {
        $this->assertEquals(4.877,$this->_localisation->getLng());
    }

    public function testGetLat()
    {
        $this->assertEquals(4.5,$this->_localisation->getLat());
    }

    public function testLat()
    {
        $this->assertClassHasAttribute("lat", Localisation::class);
    }

    public function testLng()
    {
        $this->assertClassHasAttribute("lng", Localisation::class);
    }



}
