<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Exception\NotUnsettableException;
use Dustin\Encapsulation\Exception\PropertyNotExistsException;
use Dustin\Encapsulation\Exception\StaticException;
use Dustin\Encapsulation\PropertyEncapsulation;
use PHPUnit\Framework\TestCase;

class MovieNight extends PropertyEncapsulation
{
    protected static $total = 0;

    protected static $totalFriendsCount = 0;

    protected $drink;

    protected string $location;

    protected int $friendsCount = 0;

    protected array $snacks = [];

    protected array $movies = ['Zombieland'];

    public static function getTotal()
    {
        return static::$total;
    }

    public static function setTotal($total)
    {
        static::$total = $total;
    }

    public function getLocation()
    {
        echo 'Get location';

        return $this->location;
    }

    public function setLocation($location)
    {
        echo 'Set location';

        $this->location = $location;
    }

    public function getFriendsCount()
    {
        echo 'Get friendsCount';

        return $this->friendsCount;
    }

    public function setFriendsCount($friendsCount)
    {
        $this->friendsCount = $friendsCount;
    }

    public function getMovies()
    {
        return $this->movies;
    }

    public function setMovies($movies)
    {
        $this->movies = $movies;
    }

    public function addSnacks($snack)
    {
        echo 'Add snacks';

        $this->snacks[] = $snack;
    }
}

class PropertyEncapsulationTest extends TestCase
{
    public function testInitialization()
    {
        $encapsulation = new MovieNight(['location' => 'cinema']);

        $this->assertSame(
            $encapsulation->toArray(),
            [
                'drink' => null,
                'location' => 'cinema',
                'friendsCount' => 0,
                'snacks' => [],
                'movies' => ['Zombieland'],
            ]
        );
    }

    public function testSetGetAndAdd()
    {
        $encapsulation = new MovieNight();

        $encapsulation->set('drink', 'cola');

        $drink = $encapsulation->get('drink');
        $this->assertSame($drink, 'cola');

        $encapsulation->add('movies', 'Resident Evil');
        $encapsulation->addList('movies', ['Grindhouse: Planet Terror', 'Shaun of the Dead']);

        $this->assertSame(
            $encapsulation->get('movies'),
            ['Zombieland', 'Resident Evil', 'Grindhouse: Planet Terror', 'Shaun of the Dead']
        );
    }

    public function testSetterMethod()
    {
        $encapsulation = new MovieNight();

        $this->expectOutputString('Set location');
        $encapsulation->set('location', 'cinema');
    }

    public function testGetterMethod()
    {
        $encapsulation = new MovieNight();

        $this->expectOutputString('Get friendsCount');

        $encapsulation->get('friendsCount');
    }

    public function testAdderMethod()
    {
        $encapsulation = new MovieNight();

        $this->expectOutputString('Add snacks');

        $encapsulation->add('snacks', 'peanuts');
    }

    public function testSetWithWrongPropertyType()
    {
        $encapsulation = new MovieNight();

        $this->expectException(\TypeError::class);

        $encapsulation->set('location', ['name' => 'cinema']);
    }

    public function testSetWithMissingProperty()
    {
        $encapsulation = new MovieNight();

        $this->expectException(PropertyNotExistsException::class);

        $encapsulation->set('is3d', true);
    }

    public function testSetWithStaticProperty()
    {
        $encapsulation = new MovieNight();

        $this->expectException(StaticException::class);

        $encapsulation->set('totalFriendsCount', 2);
    }

    public function testUnsetWithMissingProperty()
    {
        $encapsulation = new MovieNight();

        $this->expectException(PropertyNotExistsException::class);

        $encapsulation->unset('missingProperty');
    }

    public function testUnsetWithStaticProperty()
    {
        $encapsulation = new MovieNight();

        $this->expectException(StaticException::class);

        $encapsulation->unset('total');
    }

    public function testUnsettable()
    {
        $encapsulation = new MovieNight();

        $this->expectException(NotUnsettableException::class);

        $encapsulation->unset('friendsCount');
    }

    public function testHas()
    {
        $encapsulation = new MovieNight();

        $this->assertTrue($encapsulation->has('location'));
        $this->assertFalse($encapsulation->has('theme'));
        $this->assertFalse($encapsulation->has('total'));
    }

    public function testGetFields()
    {
        $encapsulation = new MovieNight();

        $this->assertSame(
            $encapsulation->getFields(),
            ['drink', 'location', 'friendsCount', 'snacks', 'movies']
        );
    }
}
