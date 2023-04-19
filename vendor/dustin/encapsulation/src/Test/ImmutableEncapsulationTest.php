<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Container;
use Dustin\Encapsulation\Encapsulation;
use Dustin\Encapsulation\Exception\ImmutableException;
use Dustin\Encapsulation\ImmutableEncapsulation;
use Dustin\Encapsulation\ImmutableTrait;
use Dustin\Encapsulation\PropertyEncapsulation;
use PHPUnit\Framework\TestCase;

class MyEncapsulation extends PropertyEncapsulation
{
    use ImmutableTrait;
    protected $foo;
}

class ImmutableEncapsulationTest extends TestCase
{
    public function testImmutableEncapsulation()
    {
        $encapsulation = new ImmutableEncapsulation(['foo' => 'bar']);

        $this->expectException(ImmutableException::class);
        $encapsulation->set('hello', 'world');
    }

    public function testImmutableTrait()
    {
        $encapsulation = new MyEncapsulation(['foo' => 'bar']);

        $this->expectException(ImmutableException::class);

        $encapsulation->set('foo', 'barBar');
    }

    public function testAdd()
    {
        $encapsulation = new ImmutableEncapsulation(['list' => new Container()]);

        $this->expectException(ImmutableException::class);

        $encapsulation->add('list', 'foo');
    }

    public function testUnset()
    {
        $encapsulation = new ImmutableEncapsulation(['foo' => 'bar']);

        $this->expectException(ImmutableException::class);
        $encapsulation->unset('foo');
    }

    public function testIsMutable()
    {
        $mutableEncapsulation = new Encapsulation();
        $immutableEncapsulation1 = new ImmutableEncapsulation();
        $immutableEncapsulation2 = new MyEncapsulation();

        $this->assertTrue($mutableEncapsulation->isMutable());
        $this->assertFalse($immutableEncapsulation1->isMutable());
        $this->assertFalse($immutableEncapsulation2->isMutable());
    }
}
