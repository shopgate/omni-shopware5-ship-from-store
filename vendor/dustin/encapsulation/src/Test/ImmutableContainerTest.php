<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Container;
use Dustin\Encapsulation\Exception\ImmutableException;
use Dustin\Encapsulation\ImmutableContainer;
use PHPUnit\Framework\TestCase;

class ImmutableContainerTest extends TestCase
{
    public function testImmutableContainer()
    {
        $container = new ImmutableContainer(['foo', 'bar']);

        $this->expectException(ImmutableException::class);
        $container->add('test');
    }

    public function testImmutableMerge()
    {
        $container1 = new Container(['foo', 'bar']);
        $container2 = new Container(['hello', 'world']);

        $immutableContainer = ImmutableContainer::merge($container1, $container2);

        $this->assertEquals(
            $immutableContainer->toArray(),
            ['foo', 'bar', 'hello', 'world']
        );

        $this->assertTrue($immutableContainer instanceof ImmutableContainer);
    }
}
