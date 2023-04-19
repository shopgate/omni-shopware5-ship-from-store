<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Encapsulation;
use Dustin\Encapsulation\Exception\UncomparableException;
use Dustin\Encapsulation\IntersectionCalculation;
use Dustin\Encapsulation\NestedEncapsulation;
use PHPUnit\Framework\TestCase;

class MyObject
{
}

class IntersectionCalculationTest extends TestCase
{
    public function testFieldIntersection()
    {
        $a = new Encapsulation(['foo' => 'foo', 'bar' => 'bar']);
        $b = new Encapsulation(['foo' => 'foo', 'hello' => 'world']);

        $result = ['foo'];

        $this->assertSame(
            $a->getFieldIntersection($b),
            $result
        );

        $this->assertSame(
            IntersectionCalculation::getFieldIntersection($a, $b),
            $result
        );
    }

    public function testFieldDifference()
    {
        $a = new Encapsulation(['foo' => 'foo', 'bar' => 'bar']);
        $b = new Encapsulation(['foo' => 'foo', 'hello' => 'world']);

        $resultA = ['bar'];
        $resultB = ['hello'];

        $this->assertSame(
            $a->getFieldDifference($b),
            $resultA
        );

        $this->assertSame(
            IntersectionCalculation::getFieldDifference($a, $b),
            $resultA
        );

        $this->assertSame(
            $b->getFieldDifference($a),
            $resultB
        );

        $this->assertSame(
            IntersectionCalculation::getFieldDifference($b, $a),
            $resultB
        );
    }

    public function testIntersection()
    {
        $a = new Encapsulation([
            'foo' => 'foo',
            'bar' => 'bar',
            'nested1' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
            'nested2' => new Encapsulation([
                'key1' => 'value1',
                'key2' => 'value2',
            ]),
            'nested3' => new Encapsulation([
                'key1' => 'value1',
            ]),
        ]);

        $b = new Encapsulation([
            'foo' => 'foo',
            'hello' => 'world',
            'nested1' => [
                'key1' => 'value1',
            ],
            'nested2' => [
                'key1' => 'value1',
            ],
            'nested3' => [],
        ]);

        $intersection = $a->getIntersection($b)->toArray();

        $this->assertEquals(
            $intersection,
            [
                'foo' => 'foo',
                'nested1' => new NestedEncapsulation([
                    'key1' => 'value1',
                ]),
                'nested2' => new NestedEncapsulation([
                    'key1' => 'value1',
                ]),
            ]
        );
    }

    public function testDifference()
    {
        $a = new Encapsulation([
            'foo' => 'foo',
            'bar' => 'bar',
            'nested1' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
            'nested2' => new Encapsulation([
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => new Encapsulation([
                    'key1' => 'value1',
                    'key2' => 'value2',
                ]),
            ]),
            'nested3' => new Encapsulation([
                'key1' => 'value1',
            ]),
            'nullValue' => new Encapsulation(),
            'equalArray' => [
                'hello' => 'world',
            ],
        ]);

        $b = new Encapsulation([
            'foo' => 'foo',
            'hello' => 'world',
            'nested1' => [
                'key1' => 'value1',
            ],
            'nested2' => [
                'key1' => 'value1',
                'key3' => [
                    'key1' => 'value1',
                ],
            ],
            'nested3' => [],
            'nullValue' => null,
            'equalArray' => [
                'hello' => 'world',
            ],
        ]);

        $difference = $a->getDifference($b)->toArray();

        $this->assertEquals(
            $difference,
            [
                'bar' => 'bar',
                'nested1' => new NestedEncapsulation([
                    'key2' => 'value2',
                ]),
                'nested2' => new NestedEncapsulation([
                    'key2' => 'value2',
                    'key3' => new NestedEncapsulation([
                        'key2' => 'value2',
                    ]),
                ]),
                'nested3' => new NestedEncapsulation([
                    'key1' => 'value1',
                ]),
                'nullValue' => new NestedEncapsulation(),
            ]
        );
    }

    public function testUncomparableException()
    {
        $a = new Encapsulation([
            'foo' => 'foo',
            'bar' => 'bar',
            'test' => new MyObject(),
        ]);

        $b = new Encapsulation([
            'foo' => 'foo',
            'hello' => 'world',
            'test' => null,
        ]);

        $this->expectException(UncomparableException::class);

        $a->getIntersection($b);
    }

    public function testCollectionMissingFieldDifference()
    {
        $a = new Encapsulation([
            'foo' => 'foo',
            'bar' => 'bar',
            'test' => new Encapsulation([
                'key1' => 'value1',
            ]),
        ]);

        $b = new Encapsulation([
            'foo' => 'foo',
            'hello' => 'world',
        ]);

        $difference = $a->getDifference($b);

        $this->assertEquals(
            $difference,
            new NestedEncapsulation([
                'bar' => 'bar',
                'test' => new NestedEncapsulation([
                    'key1' => 'value1',
                ]),
            ])
        );
    }
}
