<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Container;
use Dustin\Encapsulation\Encapsulation;
use Dustin\Encapsulation\NestedEncapsulation;
use PHPUnit\Framework\TestCase;

class NestedEncapsulationContainer extends Container
{
    protected function getAllowedClass(): ?string
    {
        return NestedEncapsulation::class;
    }
}

class ContainerTest extends TestCase
{
    public function testInitialization()
    {
        $container = new Container(['apple', 'kiwi', 'carambola']);

        $this->assertSame(
            $container->toArray(),
            ['apple', 'kiwi', 'carambola']
        );
    }

    public function testBasics()
    {
        $container = new Container(['apple', 'kiwi']);
        $container->add('carambola');

        $this->assertSame(
            $container->toArray(),
            ['apple', 'kiwi', 'carambola']
        );

        $this->assertSame($container->getAt(1), 'kiwi');
        $this->assertSame(count($container), 3);
        $this->assertFalse($container->isEmpty());

        $container->clear();
        $this->assertTrue($container->isEmpty());
    }

    public function testIteration()
    {
        $heroes = ['Batman', 'Superman', 'Flash'];
        $container = new Container($heroes);

        foreach ($container as $hero) {
            $this->assertTrue(\in_array($hero, $heroes));
        }
    }

    public function testSerialization()
    {
        $container = new Container(['foo', 'bar']);
        $serialized = serialize($container);

        $newContainer = unserialize($serialized);

        $this->assertSame(
            $container->toArray(),
            $newContainer->toArray()
        );

        $json = json_encode($container);
        $data = json_decode($json);

        $this->assertSame(
            $data,
            $container->toArray()
        );
    }

    public function testAllowedClass()
    {
        $container = new NestedEncapsulationContainer([new NestedEncapsulation()]);

        $this->expectException(\InvalidArgumentException::class);

        $container->add(new Encapsulation());
    }

    public function testMap()
    {
        $container = new Container(['Apple', 'Carrot']);

        $newContainer = $container->map(function (string $v) {
            return $v == 'Apple' ? 'Fruit' : 'Vegetable';
        });

        $this->assertSame(
            $newContainer->toArray(),
            ['Fruit', 'Vegetable']
        );
    }

    public function testReduce()
    {
        $container = new Container(['Hello', ' ', 'world']);

        $value = $container->reduce(function ($carry, $text) {
            $carry .= $text;

            return $carry;
        });

        $this->assertSame($value, 'Hello world');

        $container = new Container([' ', 'world', '!']);

        $value = $container->reduce(function ($carry, $text) {
            $carry .= $text;

            return $carry;
        }, 'Hello');

        $this->assertSame($value, 'Hello world!');
    }

    public function testFilter()
    {
        $container = new Container(['Iron Man', 'Captain America', null, 'Star-Lord', 'Drax']);

        $heroes = $container->filter();

        $this->assertSame(
            $heroes->toArray(),
            ['Iron Man', 'Captain America', 'Star-Lord', 'Drax']
        );

        $guardiansOfTheGalaxy = $container->filter(function ($name) {
            return \in_array($name, ['Star-Lord', 'Drax', 'Gamora', 'Rocket', 'Groot', 'Mantis']);
        });

        $this->assertSame(
            $guardiansOfTheGalaxy->toArray(),
            ['Star-Lord', 'Drax']
        );
    }

    public function testSlice()
    {
        $container = new Container(['Batman', 'Superman', 'Ant-Man', 'Hulk']);
        $marvelHeroes = $container->slice(2);

        $this->assertSame(
            $marvelHeroes->toArray(),
            ['Ant-Man', 'Hulk']
        );

        $dcHeroes = $container->slice(0, 2);

        $this->assertSame(
            $dcHeroes->toArray(),
            ['Batman', 'Superman']
        );

        $supermanAndAntMan = $container->slice(1, 2);

        $this->assertSame(
            $supermanAndAntMan->toArray(),
            ['Superman', 'Ant-Man']
        );
    }

    public function testSplice()
    {
        $container = new Container(['Hello', 'filler', 'filler', 'world']);

        $container->splice(1, 2);

        $this->assertSame(
            $container->toArray(),
            ['Hello', 'world']
        );

        $container->splice(0, 2, ['Alice', 'Bob']);

        $this->assertSame(
            $container->toArray(),
            ['Alice', 'Bob']
        );
    }

    public function testUnique()
    {
        $container = new Container(['Apple', 'Banana', 'Apple', 'Orange']);
        $container = $container->unique();

        $this->assertSame(
            $container->toArray(),
            ['Apple', 'Banana', 'Orange']
        );
    }

    public function testShift()
    {
        $container = new Container(['Hello', 'world']);

        $this->assertSame(
            $container->shift(),
            'Hello'
        );

        $this->assertSame(
            $container->toArray(),
            ['world']
        );
    }

    public function testUnshift()
    {
        $container = new Container(['world', '!']);
        $container->unshift('Hello');

        $this->assertSame(
            $container->toArray(),
            ['Hello', 'world', '!']
        );
    }

    public function testPop()
    {
        $container = new Container(['Hello', 'world']);

        $popped = $container->pop();

        $this->assertSame($popped, 'world');
        $this->assertSame($container->toArray(), ['Hello']);
    }

    public function testReplace()
    {
        $container = new Container(['Apple', 'Banana']);
        $container = $container->replace(['Orange']);

        $this->assertSame(
            $container->toArray(),
            ['Orange', 'Banana']
        );
    }

    public function testWalk()
    {
        $container = new Container([' Hello ', ' world']);

        $container->walk(function (string &$value) {
            $value = trim($value);
        });

        $this->assertSame(
            $container->toArray(),
            ['Hello', 'world']
        );
    }

    public function testReverse()
    {
        $container = new Container(['One', 'Two', 'Three']);
        $container = $container->reverse();

        $this->assertSame(
            $container->toArray(),
            ['Three', 'Two', 'One']
        );
    }

    public function testSearch()
    {
        $container = new Container(['Zero', 'One', 'Two']);

        $one = $container->search('One');

        $this->assertSame($one, 1);
    }

    public function testHas()
    {
        $container = new Container(['Iron Man', 'Captain America', 'Thor']);

        $this->assertTrue($container->has('Iron Man'));
    }

    public function testSort()
    {
        $container = new Container(['Tony', 'Steve', 'Peter', 'Thor']);
        $container->sort();

        $this->assertSame(
            $container->toArray(),
            ['Peter', 'Steve', 'Thor', 'Tony']
        );

        $container->sort(null, Container::DESCENDING);

        $this->assertSame(
            $container->toArray(),
            ['Tony', 'Thor', 'Steve', 'Peter']
        );

        $container = $container->reverse();
        $container->sort(function ($a, $b) {
            $aStartsWithT = strpos($a, 'T') === 0;
            $bStartsWithT = strpos($b, 'T') === 0;

            if ($aStartsWithT && $bStartsWithT) {
                return $a <=> $b;
            }

            if (!$aStartsWithT && !$bStartsWithT) {
                return $a <=> $b;
            }

            return $aStartsWithT ? -1 : 1;
        });

        $this->assertSame(
            $container->toArray(),
            ['Thor', 'Tony', 'Peter', 'Steve']
        );
    }

    public function testChunk()
    {
        $container = new Container(['Apple', 'Orange', 'Kiwi', 'Grape']);

        $chunks = $container->chunk(2);
        $result = [['Apple', 'Orange'], ['Kiwi', 'Grape']];

        foreach ($chunks as $index => $chunk) {
            $this->assertSame(
                $chunk->toArray(),
                $result[$index]
            );
        }
    }

    public function testMerge()
    {
        $container = Container::merge(new Container(['a', 'b']), new Container(['c', 'd']), new Container(['e', 'f']));

        $this->assertSame(
            $container->toArray(),
            ['a', 'b', 'c', 'd', 'e', 'f']
        );
    }
}
