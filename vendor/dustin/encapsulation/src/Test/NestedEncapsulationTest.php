<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Encapsulation;
use Dustin\Encapsulation\NestedEncapsulation;
use PHPUnit\Framework\TestCase;

class NestedEncapsulationTest extends TestCase
{
    public function testInitialization()
    {
        $this->expectException(\InvalidArgumentException::class);

        $encapsulation = new NestedEncapsulation([new Encapsulation()]);
    }

    public function testSetValue()
    {
        $encapsulation = new NestedEncapsulation();

        $encapsulation->set('foo', 1);
        $encapsulation->set('bar', 'bar');
        $encapsulation->set('hello', ['world']);
        $encapsulation->set('inner', new NestedEncapsulation());

        $this->expectException(\InvalidArgumentException::class);

        $encapsulation->set('invalid', new Encapsulation());
    }

    public function testAdd()
    {
        $encapsulation = new NestedEncapsulation();
        $encapsulation->set('movies', ['Terminator']);

        $this->expectException(\InvalidArgumentException::class);
        $encapsulation->add('movies', new Encapsulation());
    }

    public function testSetNestedValue()
    {
        $encapsulation = new NestedEncapsulation();

        $this->expectException(\InvalidArgumentException::class);

        $encapsulation->set('foo', [new Encapsulation()]);
    }
}
